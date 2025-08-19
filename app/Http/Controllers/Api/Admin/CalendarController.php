<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\BlockTimeSlotRequest;
use App\Http\Resources\ApiResponse;
use App\Models\BlockedTimeSlot;
use App\Services\VisitSlotsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class CalendarController extends Controller
{
    protected $visitSlotsService;

    public function __construct(VisitSlotsService $visitSlotsService)
    {
        $this->visitSlotsService = $visitSlotsService;
    }

    /**
     * Get day view with all time slots and their status
     */
    public function dayView(Request $request, string $date): JsonResponse
    {
        try {
            // Validate date format
            $validated = $request->validate([
                'include_past' => 'nullable|boolean'
            ]);

            if (!Carbon::createFromFormat('Y-m-d', $date)) {
                return ApiResponse::error('Invalid date format. Use Y-m-d format.', 400);
            }

            $dateCarbon = Carbon::parse($date);
            
            // Check if date is a weekend
            if ($dateCarbon->isWeekend()) {
                return ApiResponse::error('Laboratory visits are only available on weekdays (Monday - Friday).', 422);
            }

            // Check if date is too far in the past (unless specifically requested)
            if (!($validated['include_past'] ?? false) && $dateCarbon->lt(now()->subDays(7))) {
                return ApiResponse::error('Cannot view calendar data more than 7 days in the past.', 422);
            }

            // Get all slots with their status
            $slotsWithStatus = $this->visitSlotsService->getAllSlotsWithStatus($date);

            // Calculate summary
            $totalSlots = count($slotsWithStatus);
            $availableSlots = count(array_filter($slotsWithStatus, fn($slot) => $slot['status'] === 'available'));
            $bookedSlots = count(array_filter($slotsWithStatus, fn($slot) => $slot['status'] === 'booked'));
            $blockedSlots = count(array_filter($slotsWithStatus, fn($slot) => $slot['status'] === 'blocked'));

            $summary = [
                'date' => $date,
                'formatted_date' => $dateCarbon->format('d M Y'),
                'day_name' => $dateCarbon->format('l'),
                'is_weekend' => $dateCarbon->isWeekend(),
                'is_past' => $dateCarbon->lt(now()->toDateString()),
                'total_slots' => $totalSlots,
                'available_slots' => $availableSlots,
                'booked_slots' => $bookedSlots,
                'blocked_slots' => $blockedSlots,
                'availability_percentage' => $totalSlots > 0 ? round(($availableSlots / $totalSlots) * 100, 1) : 0,
            ];

            Log::info('Calendar day view accessed', [
                'admin_user_id' => $request->user()->id,
                'date' => $date,
                'summary' => $summary
            ]);

            return ApiResponse::success([
                'summary' => $summary,
                'slots' => $slotsWithStatus,
            ], 'Calendar day view retrieved successfully');

        } catch (\Exception $e) {
            Log::error('Failed to retrieve calendar day view', [
                'error' => $e->getMessage(),
                'admin_user_id' => $request->user()->id,
                'date' => $date
            ]);

            return ApiResponse::error(
                'Failed to retrieve calendar day view',
                500,
                null,
                $e->getMessage()
            );
        }
    }

    /**
     * Block one or more time slots
     */
    public function blockSlot(BlockTimeSlotRequest $request): JsonResponse
    {
        try {
            $validated = $request->validated();

            DB::beginTransaction();

            $blockedSlots = [];
            $conflicts = [];
            $errors = [];

            // Handle single slot or multiple slots
            $slotsToBlock = isset($validated['slots']) ? $validated['slots'] : [$validated];

            foreach ($slotsToBlock as $slotData) {
                try {
                    // Check for existing blocked slot at the same time
                    $existingBlock = BlockedTimeSlot::where('date', $slotData['date'])
                        ->where('start_time', $slotData['start_time'])
                        ->where('end_time', $slotData['end_time'])
                        ->first();

                    if ($existingBlock) {
                        $errors[] = "Time slot {$slotData['start_time']} - {$slotData['end_time']} on {$slotData['date']} is already blocked";
                        continue;
                    }

                    // Check for conflicts with existing visit requests
                    $conflictingVisits = BlockedTimeSlot::getConflictingWithVisitRequests(
                        $slotData['date'],
                        $slotData['start_time'],
                        $slotData['end_time']
                    );

                    if (!empty($conflictingVisits)) {
                        $conflicts[] = [
                            'slot' => $slotData,
                            'conflicting_visits' => $conflictingVisits
                        ];
                        // Continue to create the blocked slot even with conflicts (admin decision)
                    }

                    // Create the blocked slot
                    $blockedSlot = BlockedTimeSlot::create([
                        'date' => $slotData['date'],
                        'start_time' => $slotData['start_time'],
                        'end_time' => $slotData['end_time'],
                        'reason' => $slotData['reason'] ?? null,
                        'created_by' => $request->user()->id,
                    ]);

                    $blockedSlots[] = $blockedSlot;

                } catch (\Exception $e) {
                    $errors[] = "Failed to block slot {$slotData['start_time']} - {$slotData['end_time']}: " . $e->getMessage();
                }
            }

            // If no slots were successfully blocked, rollback and return error
            if (empty($blockedSlots)) {
                DB::rollBack();
                return ApiResponse::error(
                    'No time slots were blocked',
                    400,
                    ['errors' => $errors]
                );
            }

            DB::commit();

            Log::info('Time slots blocked by admin', [
                'admin_user_id' => $request->user()->id,
                'blocked_slots_count' => count($blockedSlots),
                'conflicts_count' => count($conflicts),
                'errors_count' => count($errors)
            ]);

            $responseData = [
                'blocked_slots' => $blockedSlots->map(function ($slot) {
                    return [
                        'id' => $slot->id,
                        'date' => $slot->date->format('Y-m-d'),
                        'start_time' => $slot->start_time,
                        'end_time' => $slot->end_time,
                        'time_range' => $slot->time_range,
                        'reason' => $slot->reason,
                        'created_by' => $slot->creator->name ?? 'Admin',
                        'created_at' => $slot->created_at->format('Y-m-d H:i:s'),
                    ];
                })->toArray(),
                'conflicts' => $conflicts,
                'errors' => $errors,
                'summary' => [
                    'total_requested' => count($slotsToBlock),
                    'successfully_blocked' => count($blockedSlots),
                    'conflicts_detected' => count($conflicts),
                    'errors_occurred' => count($errors),
                ]
            ];

            return ApiResponse::success(
                $responseData,
                count($blockedSlots) === 1 
                    ? 'Time slot blocked successfully'
                    : count($blockedSlots) . ' time slots blocked successfully'
            );

        } catch (\Illuminate\Validation\ValidationException $e) {
            return ApiResponse::validationError($e->errors());

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Failed to block time slots', [
                'error' => $e->getMessage(),
                'admin_user_id' => $request->user()->id,
                'request_data' => $validated ?? []
            ]);

            return ApiResponse::error(
                'Failed to block time slots',
                500,
                null,
                $e->getMessage()
            );
        }
    }

    /**
     * Remove blocked time slot (unblock)
     */
    public function unblockSlot(Request $request, BlockedTimeSlot $blockedTimeSlot): JsonResponse
    {
        try {
            // Check if the blocked slot can be removed (not in the past)
            if (!$blockedTimeSlot->canBeRemoved()) {
                return ApiResponse::error(
                    'Cannot remove blocked slots from the past',
                    422
                );
            }

            $slotInfo = [
                'id' => $blockedTimeSlot->id,
                'date' => $blockedTimeSlot->date->format('Y-m-d'),
                'time_range' => $blockedTimeSlot->time_range,
                'reason' => $blockedTimeSlot->reason,
            ];

            $blockedTimeSlot->delete();

            Log::info('Time slot unblocked by admin', [
                'admin_user_id' => $request->user()->id,
                'unblocked_slot' => $slotInfo
            ]);

            return ApiResponse::success(
                $slotInfo,
                'Time slot unblocked successfully'
            );

        } catch (\Exception $e) {
            Log::error('Failed to unblock time slot', [
                'blocked_slot_id' => $blockedTimeSlot->id,
                'error' => $e->getMessage(),
                'admin_user_id' => $request->user()->id
            ]);

            return ApiResponse::error(
                'Failed to unblock time slot',
                500,
                null,
                $e->getMessage()
            );
        }
    }

    /**
     * Get month view with availability summary
     */
    public function monthView(Request $request, int $year, int $month): JsonResponse
    {
        try {
            // Validate year and month
            if ($year < 2020 || $year > 2030) {
                return ApiResponse::error('Year must be between 2020 and 2030', 400);
            }

            if ($month < 1 || $month > 12) {
                return ApiResponse::error('Month must be between 1 and 12', 400);
            }

            // Get month summary
            $monthSummary = $this->visitSlotsService->getMonthSummary($year, $month);

            // Calculate overall statistics
            $totalDays = count($monthSummary);
            $totalSlots = array_sum(array_column($monthSummary, 'total_slots'));
            $totalAvailable = array_sum(array_column($monthSummary, 'available_slots'));
            $totalBooked = array_sum(array_column($monthSummary, 'booked_slots'));
            $totalBlocked = array_sum(array_column($monthSummary, 'blocked_slots'));

            $overallStats = [
                'month' => $month,
                'year' => $year,
                'month_name' => Carbon::create($year, $month)->format('F Y'),
                'total_working_days' => $totalDays,
                'total_slots' => $totalSlots,
                'total_available_slots' => $totalAvailable,
                'total_booked_slots' => $totalBooked,
                'total_blocked_slots' => $totalBlocked,
                'overall_availability_percentage' => $totalSlots > 0 ? round(($totalAvailable / $totalSlots) * 100, 1) : 0,
                'booking_rate' => $totalSlots > 0 ? round(($totalBooked / $totalSlots) * 100, 1) : 0,
                'blocked_rate' => $totalSlots > 0 ? round(($totalBlocked / $totalSlots) * 100, 1) : 0,
            ];

            // Find busiest and quietest days
            if (!empty($monthSummary)) {
                $busiestDay = collect($monthSummary)->sortByDesc('booked_slots')->first();
                $quietestDay = collect($monthSummary)->sortBy('booked_slots')->first();

                $overallStats['busiest_day'] = [
                    'date' => $busiestDay['date'],
                    'formatted_date' => $busiestDay['formatted_date'],
                    'booked_slots' => $busiestDay['booked_slots']
                ];

                $overallStats['quietest_day'] = [
                    'date' => $quietestDay['date'],
                    'formatted_date' => $quietestDay['formatted_date'],
                    'booked_slots' => $quietestDay['booked_slots']
                ];
            }

            Log::info('Calendar month view accessed', [
                'admin_user_id' => $request->user()->id,
                'year' => $year,
                'month' => $month,
                'stats' => $overallStats
            ]);

            return ApiResponse::success([
                'overall_stats' => $overallStats,
                'daily_summary' => $monthSummary,
            ], 'Calendar month view retrieved successfully');

        } catch (\Exception $e) {
            Log::error('Failed to retrieve calendar month view', [
                'error' => $e->getMessage(),
                'admin_user_id' => $request->user()->id,
                'year' => $year,
                'month' => $month
            ]);

            return ApiResponse::error(
                'Failed to retrieve calendar month view',
                500,
                null,
                $e->getMessage()
            );
        }
    }

    /**
     * Get list of all blocked slots with filtering
     */
    public function blockedSlotsList(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'date_from' => 'nullable|date',
                'date_to' => 'nullable|date|after_or_equal:date_from',
                'include_past' => 'nullable|boolean',
                'per_page' => 'nullable|integer|min:1|max:100'
            ]);

            $query = BlockedTimeSlot::with('creator');

            // Apply date filters
            if ($validated['date_from'] ?? null) {
                $query->where('date', '>=', $validated['date_from']);
            }

            if ($validated['date_to'] ?? null) {
                $query->where('date', '<=', $validated['date_to']);
            }

            // Exclude past slots unless specifically requested
            if (!($validated['include_past'] ?? false)) {
                $query->future();
            }

            // Order by date and time
            $query->orderBy('date')->orderBy('start_time');

            $perPage = min($validated['per_page'] ?? 15, 100);
            $blockedSlots = $query->paginate($perPage);

            return ApiResponse::paginated(
                $blockedSlots,
                \App\Http\Resources\Admin\BlockedTimeSlotResource::class,
                'Blocked time slots retrieved successfully',
                [
                    'filters' => [
                        'date_from' => $validated['date_from'] ?? null,
                        'date_to' => $validated['date_to'] ?? null,
                        'include_past' => $validated['include_past'] ?? false,
                    ]
                ]
            );

        } catch (\Illuminate\Validation\ValidationException $e) {
            return ApiResponse::validationError($e->errors());

        } catch (\Exception $e) {
            Log::error('Failed to retrieve blocked slots list', [
                'error' => $e->getMessage(),
                'admin_user_id' => $request->user()->id
            ]);

            return ApiResponse::error(
                'Failed to retrieve blocked slots list',
                500,
                null,
                $e->getMessage()
            );
        }
    }

    /**
     * Bulk unblock multiple time slots
     */
    public function bulkUnblock(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'blocked_slot_ids' => 'required|array|min:1|max:50',
                'blocked_slot_ids.*' => 'required|integer|exists:blocked_time_slots,id'
            ]);

            DB::beginTransaction();

            $blockedSlots = BlockedTimeSlot::whereIn('id', $validated['blocked_slot_ids'])->get();
            $unblocked = [];
            $errors = [];

            foreach ($blockedSlots as $blockedSlot) {
                try {
                    if (!$blockedSlot->canBeRemoved()) {
                        $errors[] = "Cannot remove blocked slot {$blockedSlot->time_range} on {$blockedSlot->formatted_date} (in the past)";
                        continue;
                    }

                    $slotInfo = [
                        'id' => $blockedSlot->id,
                        'date' => $blockedSlot->date->format('Y-m-d'),
                        'time_range' => $blockedSlot->time_range,
                    ];

                    $blockedSlot->delete();
                    $unblocked[] = $slotInfo;

                } catch (\Exception $e) {
                    $errors[] = "Failed to unblock slot {$blockedSlot->id}: " . $e->getMessage();
                }
            }

            if (empty($unblocked)) {
                DB::rollBack();
                return ApiResponse::error(
                    'No time slots were unblocked',
                    400,
                    ['errors' => $errors]
                );
            }

            DB::commit();

            Log::info('Bulk unblock operation completed', [
                'admin_user_id' => $request->user()->id,
                'unblocked_count' => count($unblocked),
                'errors_count' => count($errors)
            ]);

            return ApiResponse::success([
                'unblocked_slots' => $unblocked,
                'errors' => $errors,
                'summary' => [
                    'total_requested' => count($validated['blocked_slot_ids']),
                    'successfully_unblocked' => count($unblocked),
                    'errors_occurred' => count($errors),
                ]
            ], count($unblocked) . ' time slots unblocked successfully');

        } catch (\Illuminate\Validation\ValidationException $e) {
            return ApiResponse::validationError($e->errors());

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Bulk unblock operation failed', [
                'error' => $e->getMessage(),
                'admin_user_id' => $request->user()->id
            ]);

            return ApiResponse::error(
                'Bulk unblock operation failed',
                500,
                null,
                $e->getMessage()
            );
        }
    }
}