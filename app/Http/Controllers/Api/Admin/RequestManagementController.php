<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\ApiResponse;
use App\Http\Resources\Public\BorrowRequestTrackingResource;
use App\Models\BorrowRequest;
use App\Models\TestingRequest;
use App\Models\VisitRequest;
use App\Services\BorrowLetterService;
use App\Services\VisitLetterService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RequestManagementController extends Controller
{
    protected BorrowLetterService $borrowLetterService;
    protected VisitLetterService $visitLetterService;

    public function __construct(BorrowLetterService $borrowLetterService, VisitLetterService $visitLetterService)
    {
        $this->borrowLetterService = $borrowLetterService;
        $this->visitLetterService = $visitLetterService;
    }

    /**
     * Get paginated list of borrow requests with filtering
     */
    public function borrowRequests(Request $request): JsonResponse
    {
        try {
            // Handle summary request
            if ($request->boolean('summary')) {
                return $this->getBorrowRequestSummary();
            }

            $query = BorrowRequest::with(['borrowRequestItems.equipment.category', 'reviewer'])
                ->orderBy('created_at', 'desc');

            // Apply filters
            if ($request->filled('status')) {
                $query->where('status', $request->get('status'));
            }

            if ($request->filled('date_from')) {
                $query->whereDate('borrow_date', '>=', $request->get('date_from'));
            }

            if ($request->filled('date_to')) {
                $query->whereDate('borrow_date', '<=', $request->get('date_to'));
            }

            if ($request->filled('search')) {
                $search = $request->get('search');
                $query->where(function ($q) use ($search) {
                    // Search in supervisor name and email
                    $q->where('supervisor_name', 'like', "%{$search}%")
                        ->orWhere('supervisor_email', 'like', "%{$search}%")
                        // Search in members JSON field (for member names)
                        ->orWhere('members', 'like', "%{$search}%");
                });
            }

            $perPage = min($request->get('per_page', 15), 100);
            $borrowRequests = $query->paginate($perPage);

            return ApiResponse::paginated(
                $borrowRequests,
                \App\Http\Resources\Public\BorrowRequestTrackingResource::class,
                'Borrow requests retrieved successfully',
                [
                    'filters' => [
                        'status' => $request->get('status'),
                        'date_from' => $request->get('date_from'),
                        'date_to' => $request->get('date_to'),
                        'search' => $request->get('search'),
                    ],
                    'statuses' => BorrowRequest::getStatuses(),
                ]
            );

        } catch (\Exception $e) {
            Log::error('Failed to retrieve borrow requests', [
                'error' => $e->getMessage(),
                'admin_user_id' => $request->user()->id,
            ]);

            return ApiResponse::error(
                'Failed to retrieve borrow requests',
                500,
                null,
                $e->getMessage()
            );
        }
    }

    /**
     * Get borrow requests summary statistics
     */
    private function getBorrowRequestSummary(): JsonResponse
    {
        try {
            $totalRequests = BorrowRequest::count();
            $pendingRequests = BorrowRequest::where('status', 'pending')->count();
            $approvedRequests = BorrowRequest::where('status', 'approved')->count();
            $activeRequests = BorrowRequest::where('status', 'active')->count();
            $completedRequests = BorrowRequest::where('status', 'completed')->count();
            $rejectedRequests = BorrowRequest::where('status', 'rejected')->count();
            $cancelledRequests = BorrowRequest::where('status', 'cancelled')->count();

            return ApiResponse::success([
                'total_requests' => $totalRequests,
                'pending_requests' => $pendingRequests,
                'approved_requests' => $approvedRequests,
                'active_requests' => $activeRequests,
                'completed_requests' => $completedRequests,
                'rejected_requests' => $rejectedRequests,
                'cancelled_requests' => $cancelledRequests,
                'status_distribution' => [
                    'pending' => $pendingRequests,
                    'approved' => $approvedRequests,
                    'active' => $activeRequests,
                    'completed' => $completedRequests,
                    'rejected' => $rejectedRequests,
                    'cancelled' => $cancelledRequests,
                ],
            ], 'Borrow requests summary retrieved successfully');

        } catch (\Exception $e) {
            Log::error('Failed to retrieve borrow requests summary', [
                'error' => $e->getMessage(),
            ]);

            return ApiResponse::error(
                'Failed to retrieve borrow requests summary',
                500,
                null,
                $e->getMessage()
            );
        }
    }

    /**
     * Approve borrow request
     */
    public function approveBorrowRequest(Request $request, BorrowRequest $borrowRequest): JsonResponse
    {
        try {
            if (! $borrowRequest->canBeApproved()) {
                return ApiResponse::error('Request cannot be approved in its current status', 400);
            }

            $validated = $request->validate([
                'approval_notes' => 'nullable|string|max:1000',
                'equipment_adjustments' => 'nullable|array',
                'equipment_adjustments.*.borrow_request_item_id' => 'required|exists:borrow_request_items,id',
                'equipment_adjustments.*.quantity_approved' => 'required|integer|min:0',
            ]);

            DB::beginTransaction();

            // First, validate equipment availability and reserve stock
            $equipmentReservations = [];
            $borrowRequestItems = $borrowRequest->borrowRequestItems()->with('equipment')->get();

            foreach ($borrowRequestItems as $item) {
                $equipment = $item->equipment;
                $quantityToApprove = $item->quantity_requested;

                // Check for custom quantity adjustments
                if (isset($validated['equipment_adjustments'])) {
                    $adjustment = collect($validated['equipment_adjustments'])
                        ->firstWhere('borrow_request_item_id', $item->id);
                    if ($adjustment) {
                        $quantityToApprove = $adjustment['quantity_approved'];
                    }
                }

                // Skip if no quantity to approve
                if ($quantityToApprove <= 0) {
                    continue;
                }

                // Check equipment availability
                if (! $equipment->isAvailable($quantityToApprove)) {
                    throw new \Exception(
                        "Equipment '{$equipment->name}' tidak tersedia dalam jumlah {$quantityToApprove}. ".
                        "Tersedia: {$equipment->available_quantity} unit"
                    );
                }

                // Reserve the equipment
                if (! $equipment->reserveQuantity($quantityToApprove)) {
                    throw new \Exception("Failed to reserve {$equipment->name}");
                }

                $equipmentReservations[] = [
                    'equipment' => $equipment,
                    'quantity' => $quantityToApprove,
                    'item_id' => $item->id,
                ];
            }

            // Update request status
            $borrowRequest->update([
                'status' => 'approved',
                'reviewed_at' => now(),
                'reviewed_by' => $request->user()->id,
                'approval_notes' => $validated['approval_notes'] ?? null,
            ]);

            // Update approved quantities in borrow request items
            foreach ($equipmentReservations as $reservation) {
                $borrowRequest->borrowRequestItems()
                    ->where('id', $reservation['item_id'])
                    ->update([
                        'quantity_approved' => $reservation['quantity'],
                    ]);
            }

            // If no equipment adjustments provided, approve all requested quantities
            if (empty($equipmentReservations) && ! isset($validated['equipment_adjustments'])) {
                foreach ($borrowRequestItems as $item) {
                    $item->update(['quantity_approved' => $item->quantity_requested]);
                }
            }

            DB::commit();

            // Generate PDF letter after successful approval
            try {
                $letterUrl = $this->borrowLetterService->generate($borrowRequest);
                Log::info('Borrow request letter generated', [
                    'request_id' => $borrowRequest->request_id,
                    'letter_url' => $letterUrl,
                ]);
            } catch (\Exception $e) {
                // Log the error but don't fail the approval process
                Log::error('Failed to generate borrow request letter', [
                    'request_id' => $borrowRequest->request_id,
                    'error' => $e->getMessage(),
                ]);
            }

            Log::info('Borrow request approved', [
                'request_id' => $borrowRequest->request_id,
                'admin_user_id' => $request->user()->id,
            ]);

            return ApiResponse::success(
                ['request_id' => $borrowRequest->request_id],
                'Borrow request approved successfully'
            );

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Failed to approve borrow request', [
                'request_id' => $borrowRequest->request_id,
                'error' => $e->getMessage(),
                'admin_user_id' => $request->user()->id,
            ]);

            return ApiResponse::error(
                'Failed to approve borrow request',
                500,
                null,
                $e->getMessage()
            );
        }
    }

    /**
     * Reject borrow request
     */
    public function rejectBorrowRequest(Request $request, BorrowRequest $borrowRequest): JsonResponse
    {
        try {
            if (! $borrowRequest->canBeRejected()) {
                return ApiResponse::error('Request cannot be rejected in its current status', 400);
            }

            $validated = $request->validate([
                'approval_notes' => 'required|string|max:1000',
            ]);

            $borrowRequest->update([
                'status' => 'rejected',
                'reviewed_at' => now(),
                'reviewed_by' => $request->user()->id,
                'approval_notes' => $validated['approval_notes'],
            ]);

            Log::info('Borrow request rejected', [
                'request_id' => $borrowRequest->request_id,
                'admin_user_id' => $request->user()->id,
            ]);

            return ApiResponse::success(
                ['request_id' => $borrowRequest->request_id],
                'Borrow request rejected'
            );

        } catch (\Exception $e) {
            Log::error('Failed to reject borrow request', [
                'request_id' => $borrowRequest->request_id,
                'error' => $e->getMessage(),
                'admin_user_id' => $request->user()->id,
            ]);

            return ApiResponse::error(
                'Failed to reject borrow request',
                500,
                null,
                $e->getMessage()
            );
        }
    }

    /**
     * Get single borrow request details
     */
    public function showBorrowRequest(BorrowRequest $borrowRequest): JsonResponse
    {
        try {
            $borrowRequest->load(['borrowRequestItems.equipment.category', 'reviewer']);

            return ApiResponse::success(new BorrowRequestTrackingResource($borrowRequest), 'Borrow request retrieved successfully');

        } catch (\Exception $e) {
            Log::error('Failed to get borrow request details', [
                'request_id' => $borrowRequest->request_id,
                'error' => $e->getMessage(),
            ]);

            return ApiResponse::error('Failed to get borrow request details', 500);
        }
    }

    /**
     * Update borrow request (for status changes like active -> completed)
     */
    public function updateBorrowRequest(Request $request, BorrowRequest $borrowRequest): JsonResponse
    {
        try {
            $validated = $request->validate([
                'status' => 'required|in:active,completed,cancelled',
                'admin_notes' => 'nullable|string|max:1000',
                'equipment_conditions' => 'nullable|array',
                'equipment_conditions.*.borrow_request_item_id' => 'required|exists:borrow_request_items,id',
                'equipment_conditions.*.condition_after' => 'required|in:excellent,good,fair,poor,damaged',
                'equipment_conditions.*.return_notes' => 'nullable|string|max:500',
            ]);

            DB::beginTransaction();

            $oldStatus = $borrowRequest->status;

            // Update request status
            $borrowRequest->update([
                'status' => $validated['status'],
                'reviewed_at' => now(),
                'reviewed_by' => $request->user()->id,
                'approval_notes' => $validated['admin_notes'] ?? $borrowRequest->approval_notes,
            ]);

            // Handle equipment return and stock release for completed requests
            if ($validated['status'] === 'completed' && $oldStatus !== 'completed') {
                $this->handleEquipmentReturn($borrowRequest, $validated['equipment_conditions'] ?? []);
            }

            // Handle cancellation - release reserved stock
            if ($validated['status'] === 'cancelled' && in_array($oldStatus, ['approved', 'active'])) {
                $this->releaseEquipmentStock($borrowRequest);
            }

            DB::commit();

            Log::info('Borrow request updated', [
                'request_id' => $borrowRequest->request_id,
                'status_change' => "{$oldStatus} -> {$validated['status']}",
                'admin_user_id' => $request->user()->id,
            ]);

            return ApiResponse::success(
                ['request_id' => $borrowRequest->request_id, 'status' => $borrowRequest->status],
                'Borrow request updated successfully'
            );

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Failed to update borrow request', [
                'request_id' => $borrowRequest->request_id,
                'error' => $e->getMessage(),
                'admin_user_id' => $request->user()->id,
            ]);

            return ApiResponse::error(
                'Failed to update borrow request',
                500,
                null,
                $e->getMessage()
            );
        }
    }

    /**
     * Get or generate borrow request letter PDF
     */
    public function getBorrowRequestLetter(BorrowRequest $borrowRequest): JsonResponse
    {
        try {
            $letterUrl = $this->borrowLetterService->getOrGenerate($borrowRequest);

            if (! $letterUrl) {
                return ApiResponse::error(
                    'Letter not available. Request must be approved to generate letter.',
                    404
                );
            }

            return ApiResponse::success([
                'letter_url' => $letterUrl,
                'request_id' => $borrowRequest->request_id,
                'status' => $borrowRequest->status,
            ], 'Letter retrieved successfully');

        } catch (\Exception $e) {
            Log::error('Failed to get borrow request letter', [
                'request_id' => $borrowRequest->request_id,
                'error' => $e->getMessage(),
            ]);

            return ApiResponse::error(
                'Failed to retrieve letter',
                500,
                null,
                $e->getMessage()
            );
        }
    }

    /**
     * Regenerate borrow request letter PDF
     */
    public function regenerateBorrowRequestLetter(BorrowRequest $borrowRequest): JsonResponse
    {
        try {
            // Only allow regeneration for approved/active/completed requests
            if (! in_array($borrowRequest->status, ['approved', 'active', 'completed'])) {
                return ApiResponse::error(
                    'Letter can only be regenerated for approved requests',
                    400
                );
            }

            $letterUrl = $this->borrowLetterService->regenerate($borrowRequest);

            return ApiResponse::success([
                'letter_url' => $letterUrl,
                'request_id' => $borrowRequest->request_id,
                'regenerated_at' => now()->toISOString(),
            ], 'Letter regenerated successfully');

        } catch (\Exception $e) {
            Log::error('Failed to regenerate borrow request letter', [
                'request_id' => $borrowRequest->request_id,
                'error' => $e->getMessage(),
            ]);

            return ApiResponse::error(
                'Failed to regenerate letter',
                500,
                null,
                $e->getMessage()
            );
        }
    }

    /**
     * Get or generate visit request letter PDF
     */
    public function getVisitRequestLetter(VisitRequest $visitRequest)
    {
        try {
            // Only allow letter download for approved/completed requests
            if (! in_array($visitRequest->status, ['approved', 'completed'])) {
                return ApiResponse::error(
                    'Letter not available. Request must be approved to generate letter.',
                    404
                );
            }

            $letterUrl = $this->visitLetterService->getOrGenerate($visitRequest);

            if (! $letterUrl) {
                return ApiResponse::error(
                    'Letter not available. Request must be approved to generate letter.',
                    404
                );
            }

            // Extract the file path from the URL
            $filePath = str_replace(asset('storage/'), '', $letterUrl);
            $fullPath = storage_path('app/public/' . $filePath);

            // Check if file exists
            if (! file_exists($fullPath)) {
                Log::warning('Visit request letter file not found', [
                    'request_id' => $visitRequest->request_id,
                    'file_path' => $fullPath,
                ]);

                return ApiResponse::error(
                    'Letter file not found. Please regenerate the letter.',
                    404
                );
            }

            // Return the PDF file for direct download
            return response()->file($fullPath, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="surat-kunjungan-' . $visitRequest->request_id . '.pdf"',
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to get visit request letter', [
                'request_id' => $visitRequest->request_id,
                'error' => $e->getMessage(),
            ]);

            return ApiResponse::error(
                'Failed to retrieve letter',
                500,
                null,
                $e->getMessage()
            );
        }
    }

    /**
     * Regenerate visit request letter PDF
     */
    public function regenerateVisitRequestLetter(VisitRequest $visitRequest): JsonResponse
    {
        try {
            // Only allow regeneration for approved/completed requests
            if (! in_array($visitRequest->status, ['approved', 'completed'])) {
                return ApiResponse::error(
                    'Letter can only be regenerated for approved requests',
                    400
                );
            }

            $letterUrl = $this->visitLetterService->regenerate($visitRequest);

            return ApiResponse::success([
                'letter_url' => $letterUrl,
                'request_id' => $visitRequest->request_id,
                'regenerated_at' => now()->toISOString(),
            ], 'Letter regenerated successfully');

        } catch (\Exception $e) {
            Log::error('Failed to regenerate visit request letter', [
                'request_id' => $visitRequest->request_id,
                'error' => $e->getMessage(),
            ]);

            return ApiResponse::error(
                'Failed to regenerate letter',
                500,
                null,
                $e->getMessage()
            );
        }
    }


    /**
     * Handle equipment return and stock release
     */
    private function handleEquipmentReturn(BorrowRequest $borrowRequest, array $equipmentConditions = []): void
    {
        $borrowRequestItems = $borrowRequest->borrowRequestItems()->with('equipment')->get();

        foreach ($borrowRequestItems as $item) {
            $equipment = $item->equipment;
            $quantityToRelease = $item->quantity_approved ?: $item->quantity_requested;

            // Release the reserved stock
            $equipment->releaseQuantity($quantityToRelease);

            // Update item condition if provided
            $conditionData = collect($equipmentConditions)
                ->firstWhere('borrow_request_item_id', $item->id);

            if ($conditionData) {
                $item->update([
                    'condition_after' => $conditionData['condition_after'],
                    'return_notes' => $conditionData['return_notes'] ?? null,
                ]);

                // Log if equipment was returned damaged
                if ($conditionData['condition_after'] === 'damaged') {
                    Log::warning('Equipment returned damaged', [
                        'request_id' => $borrowRequest->request_id,
                        'equipment_id' => $equipment->id,
                        'equipment_name' => $equipment->name,
                        'notes' => $conditionData['return_notes'] ?? 'No notes provided',
                    ]);
                }
            }
        }
    }

    /**
     * Release equipment stock for cancelled requests
     */
    private function releaseEquipmentStock(BorrowRequest $borrowRequest): void
    {
        $borrowRequestItems = $borrowRequest->borrowRequestItems()->with('equipment')->get();

        foreach ($borrowRequestItems as $item) {
            $equipment = $item->equipment;
            $quantityToRelease = $item->quantity_approved ?: 0;

            if ($quantityToRelease > 0) {
                $equipment->releaseQuantity($quantityToRelease);

                Log::info('Equipment stock released due to cancellation', [
                    'request_id' => $borrowRequest->request_id,
                    'equipment_id' => $equipment->id,
                    'equipment_name' => $equipment->name,
                    'quantity_released' => $quantityToRelease,
                ]);
            }
        }
    }

    /**
     * Get visit requests with filtering
     */
    public function visitRequests(Request $request): JsonResponse
    {
        try {
            // Handle summary request
            if ($request->boolean('summary')) {
                return $this->getVisitRequestSummary();
            }

            $query = VisitRequest::with(['reviewer' => function($query) {
                $query->select('id', 'name', 'email');
            }])->orderBy('created_at', 'desc');

            // Apply filters
            if ($request->filled('status')) {
                $query->where('status', $request->get('status'));
            }

            if ($request->filled('visit_purpose')) {
                $query->where('visit_purpose', $request->get('visit_purpose'));
            }

            if ($request->filled('date_from')) {
                $query->whereDate('visit_date', '>=', $request->get('date_from'));
            }

            if ($request->filled('date_to')) {
                $query->whereDate('visit_date', '<=', $request->get('date_to'));
            }

            if ($request->filled('search')) {
                $search = $request->get('search');
                $query->where(function ($q) use ($search) {
                    $q->where('request_id', 'like', "%{$search}%")
                        ->orWhere('visitor_name', 'like', "%{$search}%")
                        ->orWhere('visitor_email', 'like', "%{$search}%")
                        ->orWhere('institution', 'like', "%{$search}%");
                });
            }

            $perPage = min($request->get('per_page', 15), 100);
            $visitRequests = $query->paginate($perPage);

            return ApiResponse::paginated(
                $visitRequests,
                \App\Http\Resources\Public\VisitRequestTrackingResource::class,
                'Visit requests retrieved successfully',
                [
                    'filters' => [
                        'status' => $request->get('status'),
                        'visit_purpose' => $request->get('visit_purpose'),
                        'date_from' => $request->get('date_from'),
                        'date_to' => $request->get('date_to'),
                        'search' => $request->get('search'),
                    ],
                    'statuses' => VisitRequest::getStatuses(),
                    'purposes' => VisitRequest::getPurposes(),
                ]
            );

        } catch (\Exception $e) {
            Log::error('Failed to retrieve visit requests', [
                'error' => $e->getMessage(),
                'admin_user_id' => $request->user()->id,
            ]);

            return ApiResponse::error(
                'Failed to retrieve visit requests',
                500,
                null,
                $e->getMessage()
            );
        }
    }

    /**
     * Update visit request
     */
    public function updateVisitRequest(Request $request, VisitRequest $visitRequest): JsonResponse
    {
        try {
            $validated = $request->validate([
                'status' => 'required|string|in:pending,approved,rejected,completed,cancelled',
                'approval_notes' => 'nullable|string|max:1000',
            ]);

            // Check if status transition is valid
            if (!$visitRequest->canTransitionTo($validated['status'])) {
                return ApiResponse::error(
                    'Invalid status transition',
                    400,
                    null,
                    "Cannot transition from {$visitRequest->status} to {$validated['status']}"
                );
            }

            $visitRequest->update([
                'status' => $validated['status'],
                'approval_notes' => $validated['approval_notes'] ?? $visitRequest->approval_notes,
                'reviewed_at' => in_array($validated['status'], ['approved', 'rejected']) ? now() : $visitRequest->reviewed_at,
                'reviewed_by' => in_array($validated['status'], ['approved', 'rejected']) ? $request->user()->id : $visitRequest->reviewed_by,
            ]);

            Log::info('Visit request updated', [
                'request_id' => $visitRequest->request_id,
                'status' => $validated['status'],
                'admin_user_id' => $request->user()->id,
            ]);

            return ApiResponse::success(
                new \App\Http\Resources\Public\VisitRequestTrackingResource($visitRequest->fresh()),
                'Visit request updated successfully'
            );

        } catch (\Illuminate\Validation\ValidationException $e) {
            return ApiResponse::validationError($e->errors());

        } catch (\Exception $e) {
            Log::error('Failed to update visit request', [
                'request_id' => $visitRequest->request_id,
                'error' => $e->getMessage(),
                'admin_user_id' => $request->user()->id,
            ]);

            return ApiResponse::error(
                'Failed to update visit request',
                500,
                null,
                $e->getMessage()
            );
        }
    }

    /**
     * Get single visit request details
     */
    public function showVisitRequest(VisitRequest $visitRequest): JsonResponse
    {
        try {
            $visitRequest->load(['reviewer']);

            return ApiResponse::success(
                new \App\Http\Resources\Public\VisitRequestTrackingResource($visitRequest),
                'Visit request retrieved successfully'
            );

        } catch (\Exception $e) {
            Log::error('Failed to get visit request details', [
                'request_id' => $visitRequest->request_id,
                'error' => $e->getMessage(),
            ]);

            return ApiResponse::error('Failed to get visit request details', 500);
        }
    }

    /**
     * Get visit requests summary statistics
     */
    private function getVisitRequestSummary(): JsonResponse
    {
        try {
            $totalRequests = VisitRequest::count();
            $pendingRequests = VisitRequest::where('status', 'pending')->count();
            $approvedRequests = VisitRequest::where('status', 'approved')->count();
            $completedRequests = VisitRequest::where('status', 'completed')->count();
            $rejectedRequests = VisitRequest::where('status', 'rejected')->count();
            $cancelledRequests = VisitRequest::where('status', 'cancelled')->count();

            return ApiResponse::success([
                'total_requests' => $totalRequests,
                'pending_requests' => $pendingRequests,
                'approved_requests' => $approvedRequests,
                'completed_requests' => $completedRequests,
                'rejected_requests' => $rejectedRequests,
                'cancelled_requests' => $cancelledRequests,
                'status_distribution' => [
                    'pending' => $pendingRequests,
                    'approved' => $approvedRequests,
                    'completed' => $completedRequests,
                    'rejected' => $rejectedRequests,
                    'cancelled' => $cancelledRequests,
                ],
            ], 'Visit requests summary retrieved successfully');

        } catch (\Exception $e) {
            Log::error('Failed to retrieve visit requests summary', [
                'error' => $e->getMessage(),
            ]);

            return ApiResponse::error(
                'Failed to retrieve visit requests summary',
                500,
                null,
                $e->getMessage()
            );
        }
    }

    /**
     * Approve visit request
     */
    public function approveVisitRequest(Request $request, VisitRequest $visitRequest): JsonResponse
    {
        try {
            if (! $visitRequest->canBeApproved()) {
                return ApiResponse::error('Request cannot be approved in its current status', 400);
            }

            $validated = $request->validate([
                'approval_notes' => 'nullable|string|max:1000',
            ]);

            $visitRequest->update([
                'status' => 'approved',
                'reviewed_at' => now(),
                'reviewed_by' => $request->user()->id,
                'approval_notes' => $validated['approval_notes'] ?? null,
            ]);

            Log::info('Visit request approved', [
                'request_id' => $visitRequest->request_id,
                'admin_user_id' => $request->user()->id,
            ]);

            return ApiResponse::success(
                ['request_id' => $visitRequest->request_id],
                'Visit request approved successfully'
            );

        } catch (\Exception $e) {
            Log::error('Failed to approve visit request', [
                'request_id' => $visitRequest->request_id,
                'error' => $e->getMessage(),
                'admin_user_id' => $request->user()->id,
            ]);

            return ApiResponse::error(
                'Failed to approve visit request',
                500,
                null,
                $e->getMessage()
            );
        }
    }

    /**
     * Reject visit request
     */
    public function rejectVisitRequest(Request $request, VisitRequest $visitRequest): JsonResponse
    {
        try {
            if (! $visitRequest->canBeRejected()) {
                return ApiResponse::error('Request cannot be rejected in its current status', 400);
            }

            $validated = $request->validate([
                'rejection_reason' => 'required|string|max:1000',
            ]);

            $visitRequest->update([
                'status' => 'rejected',
                'reviewed_at' => now(),
                'reviewed_by' => $request->user()->id,
                'approval_notes' => $validated['rejection_reason'],
            ]);

            Log::info('Visit request rejected', [
                'request_id' => $visitRequest->request_id,
                'admin_user_id' => $request->user()->id,
            ]);

            return ApiResponse::success(
                ['request_id' => $visitRequest->request_id],
                'Visit request rejected'
            );

        } catch (\Exception $e) {
            Log::error('Failed to reject visit request', [
                'request_id' => $visitRequest->request_id,
                'error' => $e->getMessage(),
                'admin_user_id' => $request->user()->id,
            ]);

            return ApiResponse::error(
                'Failed to reject visit request',
                500,
                null,
                $e->getMessage()
            );
        }
    }

    /**
     * Get testing requests summary
     */
    private function getTestingRequestSummary(): JsonResponse
    {
        try {
            $totalRequests = TestingRequest::count();
            $pendingRequests = TestingRequest::where('status', 'pending')->count();
            $approvedRequests = TestingRequest::where('status', 'approved')->count();
            $sampleReceivedRequests = TestingRequest::where('status', 'sample_received')->count();
            $inProgressRequests = TestingRequest::where('status', 'in_progress')->count();
            $completedRequests = TestingRequest::where('status', 'completed')->count();
            $rejectedRequests = TestingRequest::where('status', 'rejected')->count();
            $cancelledRequests = TestingRequest::where('status', 'cancelled')->count();

            return ApiResponse::success([
                'total_requests' => $totalRequests,
                'pending_requests' => $pendingRequests,
                'approved_requests' => $approvedRequests,
                'sample_received_requests' => $sampleReceivedRequests,
                'in_progress_requests' => $inProgressRequests,
                'completed_requests' => $completedRequests,
                'rejected_requests' => $rejectedRequests,
                'cancelled_requests' => $cancelledRequests,
                'status_distribution' => [
                    'pending' => $pendingRequests,
                    'approved' => $approvedRequests,
                    'sample_received' => $sampleReceivedRequests,
                    'in_progress' => $inProgressRequests,
                    'completed' => $completedRequests,
                    'rejected' => $rejectedRequests,
                    'cancelled' => $cancelledRequests,
                ],
            ], 'Testing requests summary retrieved successfully');

        } catch (\Exception $e) {
            Log::error('Failed to retrieve testing requests summary', [
                'error' => $e->getMessage(),
            ]);

            return ApiResponse::error(
                'Failed to retrieve testing requests summary',
                500,
                null,
                $e->getMessage()
            );
        }
    }

    /**
     * Get testing requests with filtering
     */
    public function testingRequests(Request $request): JsonResponse
    {
        try {
            // Handle summary request
            if ($request->boolean('summary')) {
                return $this->getTestingRequestSummary();
            }

            $query = TestingRequest::with(['reviewer', 'assignedUser'])
                ->orderBy('created_at', 'desc');

            // Apply filters
            if ($request->filled('status')) {
                $query->where('status', $request->get('status'));
            }

            if ($request->filled('testing_type')) {
                $query->where('testing_type', $request->get('testing_type'));
            }

            if ($request->filled('urgent_request')) {
                $query->where('urgent_request', $request->boolean('urgent_request'));
            }

            if ($request->filled('date_from')) {
                $query->whereDate('preferred_date', '>=', $request->get('date_from'));
            }

            if ($request->filled('date_to')) {
                $query->whereDate('preferred_date', '<=', $request->get('date_to'));
            }

            if ($request->filled('search')) {
                $search = $request->get('search');
                $query->where(function ($q) use ($search) {
                    $q->where('request_id', 'like', "%{$search}%")
                        ->orWhere('client_name', 'like', "%{$search}%")
                        ->orWhere('client_organization', 'like', "%{$search}%")
                        ->orWhere('client_email', 'like', "%{$search}%")
                        ->orWhere('sample_name', 'like', "%{$search}%")
                        ->orWhere('testing_type', 'like', "%{$search}%");
                });
            }

            $perPage = min($request->get('per_page', 15), 100);
            $testingRequests = $query->paginate($perPage);

            // Transform data manually to include all necessary fields
            $testingData = $testingRequests->getCollection()->map(function ($request) {
                return [
                    'id' => $request->id,
                    'request_id' => $request->request_id,
                    'status' => $request->status,
                    'status_label' => $request->status_label,
                    'client_name' => $request->client_name,
                    'client_organization' => $request->client_organization,
                    'client_email' => $request->client_email,
                    'client_phone' => $request->client_phone,
                    'client_address' => $request->client_address,
                    'sample_name' => $request->sample_name,
                    'sample_description' => $request->sample_description,
                    'sample_quantity' => $request->sample_quantity,
                    'sample_delivery_schedule' => $request->sample_delivery_schedule?->format('Y-m-d'),
                    'testing_type' => $request->testing_type,
                    'testing_type_label' => $request->testing_type_label,
                    'testing_parameters' => $request->testing_parameters,
                    'urgent_request' => $request->urgent_request,
                    'estimated_duration' => $request->estimated_duration,
                    'completion_date' => $request->completion_date?->format('Y-m-d'),
                    'result_summary' => $request->result_summary,
                    'result_files_path' => $request->result_files_path,
                    'cost' => $request->cost,
                    'submitted_at' => $request->submitted_at->format('Y-m-d H:i:s'),
                    'reviewed_at' => $request->reviewed_at?->format('Y-m-d H:i:s'),
                    'approval_notes' => $request->approval_notes,
                    'reviewer' => $request->reviewer ? [
                        'id' => $request->reviewer->id,
                        'name' => $request->reviewer->name,
                    ] : null,
                    'assigned_user' => $request->assignedUser ? [
                        'id' => $request->assignedUser->id,
                        'name' => $request->assignedUser->name,
                    ] : null,
                ];
            });

            return response()->json([
                'success' => true,
                'message' => 'Testing requests retrieved successfully',
                'data' => $testingData,
                'meta' => [
                    'pagination' => [
                        'current_page' => $testingRequests->currentPage(),
                        'last_page' => $testingRequests->lastPage(),
                        'per_page' => $testingRequests->perPage(),
                        'total' => $testingRequests->total(),
                        'from' => $testingRequests->firstItem(),
                        'to' => $testingRequests->lastItem(),
                    ],
                    'filters' => [
                        'status' => $request->get('status'),
                        'testing_type' => $request->get('testing_type'),
                        'urgent_request' => $request->get('urgent_request'),
                        'date_from' => $request->get('date_from'),
                        'date_to' => $request->get('date_to'),
                        'search' => $request->get('search'),
                    ],
                    'statuses' => TestingRequest::getStatuses(),
                    'testing_types' => TestingRequest::getTestingTypes(),
                ],
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to retrieve testing requests', [
                'error' => $e->getMessage(),
                'admin_user_id' => $request->user()->id,
            ]);

            return ApiResponse::error(
                'Failed to retrieve testing requests',
                500,
                null,
                $e->getMessage()
            );
        }
    }

    /**
     * Show testing request details
     */
    public function showTestingRequest(TestingRequest $testingRequest): JsonResponse
    {
        try {
            $testingRequest->load(['reviewer', 'assignedUser']);

            return ApiResponse::success(
                new \App\Http\Resources\Public\TestingRequestTrackingResource($testingRequest),
                'Testing request retrieved successfully'
            );

        } catch (\Exception $e) {
            Log::error('Failed to retrieve testing request', [
                'request_id' => $testingRequest->request_id,
                'error' => $e->getMessage(),
            ]);

            return ApiResponse::error(
                'Failed to retrieve testing request',
                500,
                null,
                $e->getMessage()
            );
        }
    }

    /**
     * Update testing request
     */
    public function updateTestingRequest(Request $request, TestingRequest $testingRequest): JsonResponse
    {
        try {

            $validated = $request->validate([
                'status' => 'sometimes|string|in:pending,approved,sample_received,in_progress,completed,rejected,cancelled',
                'testing_type' => 'sometimes|string|in:'.implode(',', array_keys(TestingRequest::getTestingTypes())),
                'testing_parameters' => 'sometimes|array',
                'preferred_date' => 'sometimes|date|after:today',
                'estimated_duration' => 'sometimes|integer|min:1|max:30',
                'cost' => 'sometimes|numeric|min:0',
                'assigned_to' => 'sometimes|nullable|exists:users,id',
                'approval_notes' => 'sometimes|nullable|string|max:1000',
                'result_summary' => 'sometimes|nullable|string',
                'result_files' => 'sometimes|array|max:10',
                'result_files.*' => 'file|mimes:pdf,doc,docx,xlsx,xls,png,jpg,jpeg|max:10240', // 10MB max per file
            ]);

            // If status is being changed, add timestamp and user info
            if (isset($validated['status']) && $validated['status'] !== $testingRequest->status) {
                $validated['reviewed_at'] = now();
                $validated['reviewed_by'] = $request->user()->id;

                // Auto-set completion date when status changes to completed
                if ($validated['status'] === 'completed') {
                    $validated['completion_date'] = now();
                }
            }

            // Handle result file uploads for completed status
            if ($request->hasFile('result_files')) {
                $uploadedFiles = [];
                $files = $request->file('result_files');

                foreach ($files as $index => $file) {
                    if ($file && $file->isValid()) {
                        // Generate unique filename: {request_id}_result_{timestamp}_{index}_{original_name}
                        $timestamp = now()->format('YmdHis');
                        $originalName = $file->getClientOriginalName();
                        $extension = $file->getClientOriginalExtension();
                        $baseName = pathinfo($originalName, PATHINFO_FILENAME);
                        $fileName = "{$testingRequest->request_id}_result_{$timestamp}_{$index}_{$baseName}.{$extension}";

                        // Store file in testing-requests directory
                        $filePath = $file->storeAs('testing-requests', $fileName, 'public');

                        $uploadedFiles[] = [
                            'original_name' => $originalName,
                            'stored_name' => $fileName,
                            'path' => $filePath,
                            'size' => $file->getSize(),
                            'mime_type' => $file->getMimeType(),
                            'uploaded_at' => now()->toISOString(),
                            'uploaded_by' => $request->user()->id,
                        ];
                    }
                }

                if (!empty($uploadedFiles)) {
                    // Merge with existing files if any
                    $existingFiles = $testingRequest->result_files_path ? json_decode($testingRequest->result_files_path, true) : [];
                    $allFiles = array_merge($existingFiles, $uploadedFiles);
                    $validated['result_files_path'] = json_encode($allFiles);
                }
            }

            $testingRequest->update($validated);

            Log::info('Testing request updated', [
                'request_id' => $testingRequest->request_id,
                'admin_user_id' => $request->user()->id,
                'changes' => array_keys($validated),
                'files_uploaded' => $request->hasFile('result_files') ? count($request->file('result_files')) : 0,
            ]);

            return ApiResponse::success(
                new \App\Http\Resources\Public\TestingRequestTrackingResource($testingRequest->fresh(['reviewer', 'assignedUser'])),
                'Testing request updated successfully'
            );

        } catch (\Illuminate\Validation\ValidationException $e) {
            return ApiResponse::validationError($e->errors());

        } catch (\Exception $e) {
            Log::error('Failed to update testing request', [
                'request_id' => $testingRequest->request_id,
                'error' => $e->getMessage(),
                'admin_user_id' => $request->user()->id,
            ]);

            return ApiResponse::error(
                'Failed to update testing request',
                500,
                null,
                $e->getMessage()
            );
        }
    }

    /**
     * Approve testing request
     */
    public function approveTestingRequest(Request $request, TestingRequest $testingRequest): JsonResponse
    {
        try {
            if (! $testingRequest->canBeApproved()) {
                return ApiResponse::error('Request cannot be approved in its current status', 400);
            }

            $validated = $request->validate([
                'approval_notes' => 'nullable|string|max:1000',
                'cost' => 'nullable|numeric|min:0',
                'estimated_duration' => 'nullable|integer|min:1|max:30',
                'assigned_to' => 'nullable|exists:users,id',
            ]);

            DB::beginTransaction();

            $updateData = [
                'status' => 'approved',
                'reviewed_at' => now(),
                'reviewed_by' => $request->user()->id,
                'approval_notes' => $validated['approval_notes'] ?? null,
            ];

            // Add optional fields if provided
            if (isset($validated['cost'])) {
                $updateData['cost'] = $validated['cost'];
            }

            if (isset($validated['estimated_duration'])) {
                $updateData['estimated_duration'] = $validated['estimated_duration'];
            }

            if (isset($validated['assigned_to'])) {
                $updateData['assigned_to'] = $validated['assigned_to'];
            }

            $testingRequest->update($updateData);

            DB::commit();

            Log::info('Testing request approved', [
                'request_id' => $testingRequest->request_id,
                'admin_user_id' => $request->user()->id,
                'assigned_to' => $validated['assigned_to'] ?? null,
            ]);

            return ApiResponse::success(
                ['request_id' => $testingRequest->request_id],
                'Testing request approved successfully'
            );

        } catch (\Illuminate\Validation\ValidationException $e) {
            return ApiResponse::validationError($e->errors());

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Failed to approve testing request', [
                'request_id' => $testingRequest->request_id,
                'error' => $e->getMessage(),
                'admin_user_id' => $request->user()->id,
            ]);

            return ApiResponse::error(
                'Failed to approve testing request',
                500,
                null,
                $e->getMessage()
            );
        }
    }

    /**
     * Reject testing request
     */
    public function rejectTestingRequest(Request $request, TestingRequest $testingRequest): JsonResponse
    {
        try {
            if (! $testingRequest->canBeRejected()) {
                return ApiResponse::error('Request cannot be rejected in its current status', 400);
            }

            $validated = $request->validate([
                'approval_notes' => 'required|string|max:1000',
            ]);

            $testingRequest->update([
                'status' => 'rejected',
                'reviewed_at' => now(),
                'reviewed_by' => $request->user()->id,
                'approval_notes' => $validated['approval_notes'],
            ]);

            Log::info('Testing request rejected', [
                'request_id' => $testingRequest->request_id,
                'admin_user_id' => $request->user()->id,
            ]);

            return ApiResponse::success(
                ['request_id' => $testingRequest->request_id],
                'Testing request rejected'
            );

        } catch (\Illuminate\Validation\ValidationException $e) {
            return ApiResponse::validationError($e->errors());

        } catch (\Exception $e) {
            Log::error('Failed to reject testing request', [
                'request_id' => $testingRequest->request_id,
                'error' => $e->getMessage(),
                'admin_user_id' => $request->user()->id,
            ]);

            return ApiResponse::error(
                'Failed to reject testing request',
                500,
                null,
                $e->getMessage()
            );
        }
    }

    /**
     * View testing results PDF in browser
     */
    public function downloadTestingResults(TestingRequest $testingRequest)
    {
        try {
            // Only allow viewing for completed requests with files
            if ($testingRequest->status !== 'completed') {
                return ApiResponse::error(
                    'Testing must be completed before viewing results',
                    400
                );
            }

            if (!$testingRequest->result_files_path) {
                return ApiResponse::error(
                    'No result files available',
                    404
                );
            }

            $files = json_decode($testingRequest->result_files_path, true);
            if (!is_array($files) || empty($files)) {
                return ApiResponse::error(
                    'No valid result files found',
                    404
                );
            }

            // Get the first file (should be the PDF result)
            $file = $files[0];
            $filePath = storage_path('app/public/' . $file['path']);

            if (!file_exists($filePath)) {
                Log::warning('Testing result file not found', [
                    'request_id' => $testingRequest->request_id,
                    'file_path' => $filePath,
                ]);

                return ApiResponse::error(
                    'Result file not found. Please contact administrator.',
                    404
                );
            }

            // Ensure it's a PDF file
            if (!str_ends_with(strtolower($file['original_name']), '.pdf')) {
                return ApiResponse::error(
                    'Result file is not a PDF document',
                    400
                );
            }

            Log::info('Testing results PDF viewed', [
                'request_id' => $testingRequest->request_id,
                'file_name' => $file['original_name'],
            ]);

            // Return the PDF file for viewing in browser (inline display)
            return response()->file($filePath, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="' . $file['original_name'] . '"',
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to view testing results', [
                'request_id' => $testingRequest->request_id,
                'error' => $e->getMessage(),
            ]);

            return ApiResponse::error(
                'Failed to view results',
                500,
                null,
                $e->getMessage()
            );
        }
    }
}
