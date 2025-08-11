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
}