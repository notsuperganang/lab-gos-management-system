<?php

namespace App\Http\Controllers\Api\Public;

use App\Http\Controllers\Controller;
use App\Http\Requests\Public\BorrowRequestRequest;
use App\Http\Requests\Public\VisitRequestRequest;
use App\Http\Requests\Public\TestingRequestRequest;
use App\Models\BorrowRequest;
use App\Models\BorrowRequestItem;
use App\Models\VisitRequest;
use App\Models\TestingRequest;
use App\Models\Equipment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class RequestController extends Controller
{
    /**
     * Submit equipment borrow request
     * 
     * Allows students/guests to submit equipment borrowing requests without authentication.
     * Creates a borrow request with associated equipment items.
     */
    public function submitBorrowRequest(BorrowRequestRequest $request): JsonResponse
    {
        try {
            $validated = $request->validated();
            
            DB::beginTransaction();
            
            // Create borrow request
            $borrowRequest = BorrowRequest::create([
                'status' => 'pending',
                'members' => $validated['members'],
                'supervisor_name' => $validated['supervisor_name'],
                'supervisor_nip' => $validated['supervisor_nip'],
                'supervisor_email' => $validated['supervisor_email'],
                'supervisor_phone' => $validated['supervisor_phone'],
                'purpose' => $validated['purpose'],
                'borrow_date' => $validated['borrow_date'],
                'return_date' => $validated['return_date'],
                'start_time' => $validated['start_time'] ?? null,
                'end_time' => $validated['end_time'] ?? null,
                'submitted_at' => now(),
            ]);
            
            // Create borrow request items
            foreach ($validated['equipment_items'] as $item) {
                BorrowRequestItem::create([
                    'borrow_request_id' => $borrowRequest->id,
                    'equipment_id' => $item['equipment_id'],
                    'quantity_requested' => $item['quantity_requested'],
                    'notes' => $item['notes'] ?? null,
                ]);
            }
            
            DB::commit();
            
            // Load relationships for response
            $borrowRequest->load('borrowRequestItems.equipment');
            
            Log::info('Borrow request submitted', [
                'request_id' => $borrowRequest->request_id,
                'supervisor_email' => $borrowRequest->supervisor_email
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Equipment borrow request submitted successfully',
                'data' => [
                    'request_id' => $borrowRequest->request_id,
                    'status' => $borrowRequest->status,
                    'submitted_at' => $borrowRequest->submitted_at->format('Y-m-d H:i:s'),
                    'tracking_url' => route('api.tracking.borrow', $borrowRequest->request_id),
                ]
            ], 201);
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Failed to submit borrow request', [
                'error' => $e->getMessage(),
                'request_data' => $request->all()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to submit borrow request',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }
    
    /**
     * Submit lab visit request
     * 
     * Allows students/guests to submit laboratory visit requests.
     */
    public function submitVisitRequest(VisitRequestRequest $request): JsonResponse
    {
        try {
            $validated = $request->validated();
            
            DB::beginTransaction();
            
            $requestLetterPath = null;
            if ($request->hasFile('request_letter')) {
                $requestLetterPath = $request->file('request_letter')->store('visit-requests/letters', 'public');
            }
            
            // Create visit request - observer will handle notifications automatically
            $visitRequest = VisitRequest::create([
                'request_id' => VisitRequest::generateRequestId(),
                'status' => 'pending',
                'visitor_name' => $validated['visitor_name'],
                'visitor_email' => $validated['visitor_email'],
                'visitor_phone' => $validated['visitor_phone'],
                'institution' => $validated['institution'],
                'visit_purpose' => $validated['visit_purpose'],
                'visit_date' => $validated['visit_date'],
                'start_time' => $validated['start_time'],
                'end_time' => $validated['end_time'],
                'group_size' => $validated['group_size'],
                'purpose_description' => $validated['purpose_description'] ?? null,
                'special_requirements' => $validated['special_requirements'] ?? null,
                'equipment_needed' => $validated['equipment_needed'] ?? null,
                'request_letter_path' => $requestLetterPath,
                'submitted_at' => now(),
            ]);
            
            // Commit the database transaction first
            DB::commit();
            
            Log::info('Visit request submitted successfully', [
                'request_id' => $visitRequest->request_id,
                'visitor_email' => $visitRequest->visitor_email,
                'visitor_name' => $visitRequest->visitor_name
            ]);
            
            // Try to send notifications after successful creation (non-blocking)
            // Temporarily disabled for testing
            // try {
            //     $notificationService = app(\App\Services\NotificationService::class);
            //     $notificationService->sendVisitRequestNotifications($visitRequest, 'submitted');
            // } catch (\Exception $notificationError) {
            //     Log::warning('Failed to send visit request notifications (non-critical)', [
            //         'request_id' => $visitRequest->request_id,
            //         'error' => $notificationError->getMessage()
            //     ]);
            // }
            
            return response()->json([
                'success' => true,
                'message' => 'Lab visit request submitted successfully',
                'data' => [
                    'request_id' => $visitRequest->request_id,
                    'status' => $visitRequest->status,
                    'submitted_at' => $visitRequest->submitted_at->format('Y-m-d H:i:s'),
                    'tracking_url' => route('api.tracking.visit', $visitRequest->request_id),
                ]
            ], 201);
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            // Clean up uploaded file if exists
            if (isset($requestLetterPath) && Storage::disk('public')->exists($requestLetterPath)) {
                Storage::disk('public')->delete($requestLetterPath);
            }
            
            Log::error('Failed to submit visit request', [
                'error' => $e->getMessage(),
                'request_data' => $request->except(['request_letter'])
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to submit visit request',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }
    
    /**
     * Submit sample testing request
     * 
     * Allows students/guests to submit sample testing requests.
     */
    public function submitTestingRequest(TestingRequestRequest $request): JsonResponse
    {
        try {
            $validated = $request->validated();
            
            // Get testing type configuration for auto-computation
            $testingConfig = TestingRequest::getTestingTypeConfig();
            $config = $testingConfig[$validated['testing_type']] ?? $testingConfig['custom'];
            
            DB::beginTransaction();
            
            // Create testing request
            $testingRequest = TestingRequest::create([
                'status' => 'pending',
                'client_name' => $validated['client_name'],
                'client_organization' => $validated['client_organization'],
                'client_email' => $validated['client_email'],
                'client_phone' => $validated['client_phone'],
                'client_address' => $validated['client_address'],
                'sample_name' => $validated['sample_name'],
                'sample_description' => $validated['sample_description'],
                'sample_quantity' => $validated['sample_quantity'],
                'testing_type' => $validated['testing_type'],
                'testing_parameters' => $validated['testing_parameters'] ?? [],
                'urgent_request' => $validated['urgent_request'] ?? false,
                'sample_delivery_schedule' => $validated['sample_delivery_schedule'],
                'estimated_duration' => $config['duration_days'],
                'cost' => $config['cost'],
                'submitted_at' => now(),
            ]);
            
            DB::commit();
            
            Log::info('Testing request submitted', [
                'request_id' => $testingRequest->request_id,
                'client_email' => $testingRequest->client_email
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Sample testing request submitted successfully',
                'data' => [
                    'request_id' => $testingRequest->request_id,
                    'status' => $testingRequest->status,
                    'submitted_at' => $testingRequest->submitted_at->format('Y-m-d H:i:s'),
                    'tracking_url' => route('api.tracking.testing', $testingRequest->request_id),
                ]
            ], 201);
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Failed to submit testing request', [
                'error' => $e->getMessage(),
                'request_data' => $request->all()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to submit testing request',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }
}