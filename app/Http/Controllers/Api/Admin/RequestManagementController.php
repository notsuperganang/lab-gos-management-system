<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\ApiResponse;
use App\Models\BorrowRequest;
use App\Models\VisitRequest;
use App\Models\TestingRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RequestManagementController extends Controller
{
    /**
     * Get paginated list of borrow requests with filtering
     */
    public function borrowRequests(Request $request): JsonResponse
    {
        try {
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
                    $q->where('request_id', 'like', "%{$search}%")
                      ->orWhere('supervisor_name', 'like', "%{$search}%")
                      ->orWhere('supervisor_email', 'like', "%{$search}%")
                      ->orWhere('purpose', 'like', "%{$search}%");
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
                'admin_user_id' => $request->user()->id
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
     * Approve borrow request
     */
    public function approveBorrowRequest(Request $request, BorrowRequest $borrowRequest): JsonResponse
    {
        try {
            if (!$borrowRequest->canBeApproved()) {
                return ApiResponse::error('Request cannot be approved in its current status', 400);
            }
            
            $validated = $request->validate([
                'approval_notes' => 'nullable|string|max:1000',
                'equipment_adjustments' => 'nullable|array',
                'equipment_adjustments.*.borrow_request_item_id' => 'required|exists:borrow_request_items,id',
                'equipment_adjustments.*.quantity_approved' => 'required|integer|min:0',
            ]);
            
            DB::beginTransaction();
            
            // Update request status
            $borrowRequest->update([
                'status' => 'approved',
                'reviewed_at' => now(),
                'reviewed_by' => $request->user()->id,
                'approval_notes' => $validated['approval_notes'] ?? null,
            ]);
            
            // Update equipment quantities if provided
            if (isset($validated['equipment_adjustments'])) {
                foreach ($validated['equipment_adjustments'] as $adjustment) {
                    $borrowRequest->borrowRequestItems()
                        ->where('id', $adjustment['borrow_request_item_id'])
                        ->update([
                            'quantity_approved' => $adjustment['quantity_approved']
                        ]);
                }
            } else {
                // Default: approve all requested quantities
                $borrowRequest->borrowRequestItems()->update([
                    'quantity_approved' => DB::raw('quantity_requested')
                ]);
            }
            
            DB::commit();
            
            Log::info('Borrow request approved', [
                'request_id' => $borrowRequest->request_id,
                'admin_user_id' => $request->user()->id
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
                'admin_user_id' => $request->user()->id
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
            if (!$borrowRequest->canBeRejected()) {
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
                'admin_user_id' => $request->user()->id
            ]);
            
            return ApiResponse::success(
                ['request_id' => $borrowRequest->request_id],
                'Borrow request rejected'
            );
            
        } catch (\Exception $e) {
            Log::error('Failed to reject borrow request', [
                'request_id' => $borrowRequest->request_id,
                'error' => $e->getMessage(),
                'admin_user_id' => $request->user()->id
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
     * Get visit requests with filtering
     */
    public function visitRequests(Request $request): JsonResponse
    {
        try {
            $query = VisitRequest::with('reviewer')
                ->orderBy('created_at', 'desc');
            
            // Apply filters
            if ($request->filled('status')) {
                $query->where('status', $request->get('status'));
            }
            
            if ($request->filled('purpose')) {
                $query->where('purpose', $request->get('purpose'));
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
                      ->orWhere('full_name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%")
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
                        'purpose' => $request->get('purpose'),
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
                'admin_user_id' => $request->user()->id
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
     * Approve visit request
     */
    public function approveVisitRequest(Request $request, VisitRequest $visitRequest): JsonResponse
    {
        try {
            if (!$visitRequest->canBeApproved()) {
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
                'admin_user_id' => $request->user()->id
            ]);
            
            return ApiResponse::success(
                ['request_id' => $visitRequest->request_id],
                'Visit request approved successfully'
            );
            
        } catch (\Exception $e) {
            Log::error('Failed to approve visit request', [
                'request_id' => $visitRequest->request_id,
                'error' => $e->getMessage(),
                'admin_user_id' => $request->user()->id
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
            if (!$visitRequest->canBeRejected()) {
                return ApiResponse::error('Request cannot be rejected in its current status', 400);
            }
            
            $validated = $request->validate([
                'approval_notes' => 'required|string|max:1000',
            ]);
            
            $visitRequest->update([
                'status' => 'rejected',
                'reviewed_at' => now(),
                'reviewed_by' => $request->user()->id,
                'approval_notes' => $validated['approval_notes'],
            ]);
            
            Log::info('Visit request rejected', [
                'request_id' => $visitRequest->request_id,
                'admin_user_id' => $request->user()->id
            ]);
            
            return ApiResponse::success(
                ['request_id' => $visitRequest->request_id],
                'Visit request rejected'
            );
            
        } catch (\Exception $e) {
            Log::error('Failed to reject visit request', [
                'request_id' => $visitRequest->request_id,
                'error' => $e->getMessage(),
                'admin_user_id' => $request->user()->id
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
     * Get testing requests with filtering
     */
    public function testingRequests(Request $request): JsonResponse
    {
        try {
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
            
            // Transform data manually first to debug
            $testingData = $testingRequests->getCollection()->map(function ($request) {
                return [
                    'id' => $request->id,
                    'request_id' => $request->request_id,
                    'status' => $request->status,
                    'status_label' => $request->status_label,
                    'client_name' => $request->client_name,
                    'client_organization' => $request->client_organization,
                    'sample_name' => $request->sample_name,
                    'testing_type' => $request->testing_type,
                    'testing_type_label' => $request->testing_type_label,
                    'urgent_request' => $request->urgent_request,
                    'preferred_date' => $request->preferred_date?->format('Y-m-d'),
                    'cost_estimate' => $request->cost_estimate,
                    'submitted_at' => $request->submitted_at->format('Y-m-d H:i:s'),
                    'reviewed_at' => $request->reviewed_at?->format('Y-m-d H:i:s'),
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
                ]
            ]);
            
        } catch (\Exception $e) {
            Log::error('Failed to retrieve testing requests', [
                'error' => $e->getMessage(),
                'admin_user_id' => $request->user()->id
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
                'error' => $e->getMessage()
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
                'testing_type' => 'sometimes|string|in:' . implode(',', array_keys(TestingRequest::getTestingTypes())),
                'testing_parameters' => 'sometimes|array',
                'preferred_date' => 'sometimes|date|after:today',
                'estimated_duration_hours' => 'sometimes|integer|min:1|max:168',
                'cost_estimate' => 'sometimes|numeric|min:0',
                'assigned_to' => 'sometimes|nullable|exists:users,id',
                'approval_notes' => 'sometimes|nullable|string|max:1000',
            ]);
            
            $testingRequest->update($validated);
            
            Log::info('Testing request updated', [
                'request_id' => $testingRequest->request_id,
                'admin_user_id' => $request->user()->id,
                'changes' => array_keys($validated)
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
                'admin_user_id' => $request->user()->id
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
            if (!$testingRequest->canBeApproved()) {
                return ApiResponse::error('Request cannot be approved in its current status', 400);
            }
            
            $validated = $request->validate([
                'approval_notes' => 'nullable|string|max:1000',
                'cost_estimate' => 'nullable|numeric|min:0',
                'estimated_duration_hours' => 'nullable|integer|min:1|max:168',
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
            if (isset($validated['cost_estimate'])) {
                $updateData['cost_estimate'] = $validated['cost_estimate'];
            }
            
            if (isset($validated['estimated_duration_hours'])) {
                $updateData['estimated_duration_hours'] = $validated['estimated_duration_hours'];
            }
            
            if (isset($validated['assigned_to'])) {
                $updateData['assigned_to'] = $validated['assigned_to'];
            }
            
            $testingRequest->update($updateData);
            
            DB::commit();
            
            Log::info('Testing request approved', [
                'request_id' => $testingRequest->request_id,
                'admin_user_id' => $request->user()->id,
                'assigned_to' => $validated['assigned_to'] ?? null
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
                'admin_user_id' => $request->user()->id
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
            if (!$testingRequest->canBeRejected()) {
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
                'admin_user_id' => $request->user()->id
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
                'admin_user_id' => $request->user()->id
            ]);
            
            return ApiResponse::error(
                'Failed to reject testing request',
                500,
                null,
                $e->getMessage()
            );
        }
    }
}