<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\AvailableSlotsRequest;
use App\Models\VisitRequest;
use App\Services\VisitSlotsService;
use App\Http\Resources\ApiResponse;
use Carbon\Carbon;

class VisitSlotsController extends Controller
{
    protected $visitSlotsService;

    public function __construct(VisitSlotsService $visitSlotsService)
    {
        $this->visitSlotsService = $visitSlotsService;
    }

    /**
     * Get available time slots for a specific date and duration
     *
     * @param AvailableSlotsRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAvailableSlots(AvailableSlotsRequest $request)
    {
        try {
            $date = $request->input('date');
            $duration = (int) $request->input('duration');
            
            // Validate that the date is a weekday
            $dateObj = Carbon::parse($date);
            if ($dateObj->isWeekend()) {
                return ApiResponse::error('Kunjungan laboratorium hanya tersedia pada hari kerja (Senin - Jumat)', 422);
            }
            
            // Get existing bookings for the date that might conflict
            $existingBookings = VisitRequest::where('visit_date', $date)
                ->whereIn('status', ['approved', 'ready', 'active'])
                ->get(['start_time', 'end_time'])
                ->toArray();
            
            // Calculate available slots using the service
            $availableSlots = $this->visitSlotsService->calculateAvailableSlots($date, $duration, $existingBookings);
            
            return ApiResponse::success([
                'available_slots' => $availableSlots,
                'date' => $date,
                'duration' => $duration,
                'total_slots' => count($availableSlots)
            ], 'Available time slots retrieved successfully');
            
        } catch (\Exception $e) {
            \Log::error('Error getting available slots: ' . $e->getMessage(), [
                'date' => $request->input('date'),
                'duration' => $request->input('duration'),
                'trace' => $e->getTraceAsString()
            ]);
            
            return ApiResponse::error('Terjadi kesalahan saat memuat waktu tersedia', 500);
        }
    }
}