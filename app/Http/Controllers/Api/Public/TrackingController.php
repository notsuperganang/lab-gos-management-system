<?php

namespace App\Http\Controllers\Api\Public;

use App\Http\Controllers\Controller;
use App\Models\BorrowRequest;
use App\Models\VisitRequest;
use App\Models\TestingRequest;
use Illuminate\Http\JsonResponse;

class TrackingController extends Controller
{
    /**
     * Track borrow request by request ID
     * 
     * Allows anyone to track the status of a borrow request using the request ID.
     * No authentication required for transparency.
     */
    public function trackBorrowRequest(string $requestId): JsonResponse
    {
        try {
            $borrowRequest = BorrowRequest::with(['borrowRequestItems.equipment.category', 'reviewer'])
                ->where('request_id', $requestId)
                ->first();
            
            if (!$borrowRequest) {
                return response()->json([
                    'success' => false,
                    'message' => 'Request not found. Please check your request ID.'
                ], 404);
            }
            
            // Transform equipment items
            $equipmentItems = $borrowRequest->borrowRequestItems->map(function ($item) {
                return [
                    'equipment' => [
                        'id' => $item->equipment->id,
                        'name' => $item->equipment->name,
                        'model' => $item->equipment->model,
                        'category' => $item->equipment->category?->name,
                    ],
                    'quantity_requested' => $item->quantity_requested,
                    'quantity_approved' => $item->quantity_approved,
                    'condition_before' => $item->condition_before,
                    'condition_after' => $item->condition_after,
                    'notes' => $item->notes,
                ];
            });
            
            $data = [
                'request_id' => $borrowRequest->request_id,
                'status' => $borrowRequest->status,
                'status_label' => $borrowRequest->status_label,
                'status_color' => $borrowRequest->status_color,
                'members' => $borrowRequest->members,
                'supervisor' => [
                    'name' => $borrowRequest->supervisor_name,
                    'nip' => $borrowRequest->supervisor_nip,
                    'email' => $borrowRequest->supervisor_email,
                    'phone' => $borrowRequest->supervisor_phone,
                ],
                'purpose' => $borrowRequest->purpose,
                'borrow_date' => $borrowRequest->borrow_date->format('Y-m-d'),
                'return_date' => $borrowRequest->return_date->format('Y-m-d'),
                'start_time' => $borrowRequest->start_time,
                'end_time' => $borrowRequest->end_time,
                'duration_days' => $borrowRequest->duration,
                'equipment_items' => $equipmentItems,
                'total_requested_quantity' => $borrowRequest->total_requested_quantity,
                'total_approved_quantity' => $borrowRequest->total_approved_quantity,
                'submitted_at' => $borrowRequest->submitted_at->format('Y-m-d H:i:s'),
                'reviewed_at' => $borrowRequest->reviewed_at?->format('Y-m-d H:i:s'),
                'reviewer' => $borrowRequest->reviewer ? [
                    'name' => $borrowRequest->reviewer->name,
                ] : null,
                'approval_notes' => $borrowRequest->approval_notes,
                'timeline' => $this->getBorrowRequestTimeline($borrowRequest),
            ];
            
            return response()->json([
                'success' => true,
                'message' => 'Borrow request details retrieved successfully',
                'data' => $data
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve request details',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }
    
    /**
     * Track visit request by request ID
     */
    public function trackVisitRequest(string $requestId): JsonResponse
    {
        try {
            $visitRequest = VisitRequest::with('reviewer')
                ->where('request_id', $requestId)
                ->first();
            
            if (!$visitRequest) {
                return response()->json([
                    'success' => false,
                    'message' => 'Request not found. Please check your request ID.'
                ], 404);
            }
            
            $data = [
                'request_id' => $visitRequest->request_id,
                'status' => $visitRequest->status,
                'status_label' => $visitRequest->status_label,
                'status_color' => $visitRequest->status_color,
                'applicant' => [
                    'full_name' => $visitRequest->full_name,
                    'email' => $visitRequest->email,
                    'phone' => $visitRequest->phone,
                    'institution' => $visitRequest->institution,
                ],
                'purpose' => $visitRequest->purpose,
                'purpose_label' => $visitRequest->purpose_label,
                'visit_date' => $visitRequest->visit_date->format('Y-m-d'),
                'visit_time' => $visitRequest->visit_time,
                'visit_time_label' => $visitRequest->visit_time_label,
                'participants' => $visitRequest->participants,
                'additional_notes' => $visitRequest->additional_notes,
                'request_letter_url' => $visitRequest->request_letter_url,
                'approval_letter_url' => $visitRequest->approval_letter_url,
                'submitted_at' => $visitRequest->submitted_at->format('Y-m-d H:i:s'),
                'reviewed_at' => $visitRequest->reviewed_at?->format('Y-m-d H:i:s'),
                'reviewer' => $visitRequest->reviewer ? [
                    'name' => $visitRequest->reviewer->name,
                ] : null,
                'approval_notes' => $visitRequest->approval_notes,
                'timeline' => $this->getVisitRequestTimeline($visitRequest),
            ];
            
            return response()->json([
                'success' => true,
                'message' => 'Visit request details retrieved successfully',
                'data' => $data
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve request details',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }
    
    /**
     * Track testing request by request ID
     */
    public function trackTestingRequest(string $requestId): JsonResponse
    {
        try {
            $testingRequest = TestingRequest::with(['reviewer', 'assignedUser'])
                ->where('request_id', $requestId)
                ->first();
            
            if (!$testingRequest) {
                return response()->json([
                    'success' => false,
                    'message' => 'Request not found. Please check your request ID.'
                ], 404);
            }
            
            $data = [
                'request_id' => $testingRequest->request_id,
                'status' => $testingRequest->status,
                'status_label' => $testingRequest->status_label,
                'status_color' => $testingRequest->status_color,
                'progress_percentage' => $testingRequest->progress_percentage,
                'client' => [
                    'name' => $testingRequest->client_name,
                    'organization' => $testingRequest->client_organization,
                    'email' => $testingRequest->client_email,
                    'phone' => $testingRequest->client_phone,
                    'address' => $testingRequest->client_address,
                ],
                'sample' => [
                    'name' => $testingRequest->sample_name,
                    'description' => $testingRequest->sample_description,
                    'quantity' => $testingRequest->sample_quantity,
                ],
                'testing' => [
                    'type' => $testingRequest->testing_type,
                    'type_label' => $testingRequest->testing_type_label,
                    'parameters' => $testingRequest->testing_parameters,
                    'urgent_request' => $testingRequest->urgent_request,
                ],
                'schedule' => [
                    'preferred_date' => $testingRequest->preferred_date?->format('Y-m-d'),
                    'estimated_duration_hours' => $testingRequest->estimated_duration_hours,
                    'actual_start_date' => $testingRequest->actual_start_date?->format('Y-m-d'),
                    'actual_completion_date' => $testingRequest->actual_completion_date?->format('Y-m-d'),
                    'actual_duration_hours' => $testingRequest->actual_duration_hours,
                ],
                'cost' => [
                    'estimate' => $testingRequest->cost_estimate,
                    'final' => $testingRequest->final_cost,
                ],
                'results' => [
                    'summary' => $testingRequest->result_summary,
                    'files' => $testingRequest->result_files_path,
                ],
                'submitted_at' => $testingRequest->submitted_at->format('Y-m-d H:i:s'),
                'reviewed_at' => $testingRequest->reviewed_at?->format('Y-m-d H:i:s'),
                'reviewer' => $testingRequest->reviewer ? [
                    'name' => $testingRequest->reviewer->name,
                ] : null,
                'assigned_to' => $testingRequest->assignedUser ? [
                    'name' => $testingRequest->assignedUser->name,
                ] : null,
                'approval_notes' => $testingRequest->approval_notes,
                'is_overdue' => $testingRequest->isOverdue(),
                'timeline' => $this->getTestingRequestTimeline($testingRequest),
            ];
            
            return response()->json([
                'success' => true,
                'message' => 'Testing request details retrieved successfully',
                'data' => $data
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve request details',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }
    
    /**
     * Get borrow request timeline
     */
    private function getBorrowRequestTimeline(BorrowRequest $request): array
    {
        $timeline = [
            [
                'status' => 'submitted',
                'label' => 'Request Submitted',
                'date' => $request->submitted_at->format('Y-m-d H:i:s'),
                'active' => true,
            ]
        ];
        
        if ($request->reviewed_at) {
            $timeline[] = [
                'status' => $request->status,
                'label' => ucfirst($request->status),
                'date' => $request->reviewed_at->format('Y-m-d H:i:s'),
                'active' => true,
            ];
        }
        
        return $timeline;
    }
    
    /**
     * Get visit request timeline
     */
    private function getVisitRequestTimeline(VisitRequest $request): array
    {
        $timeline = [
            [
                'status' => 'submitted',
                'label' => 'Request Submitted',
                'date' => $request->submitted_at->format('Y-m-d H:i:s'),
                'active' => true,
            ]
        ];
        
        if ($request->reviewed_at) {
            $timeline[] = [
                'status' => $request->status,
                'label' => ucfirst($request->status),
                'date' => $request->reviewed_at->format('Y-m-d H:i:s'),
                'active' => true,
            ];
        }
        
        return $timeline;
    }
    
    /**
     * Get testing request timeline
     */
    private function getTestingRequestTimeline(TestingRequest $request): array
    {
        $timeline = [
            [
                'status' => 'submitted',
                'label' => 'Request Submitted',
                'date' => $request->submitted_at->format('Y-m-d H:i:s'),
                'active' => true,
            ]
        ];
        
        if ($request->reviewed_at) {
            $timeline[] = [
                'status' => $request->status,
                'label' => ucfirst($request->status),
                'date' => $request->reviewed_at->format('Y-m-d H:i:s'),
                'active' => true,
            ];
        }
        
        if ($request->actual_start_date) {
            $timeline[] = [
                'status' => 'started',
                'label' => 'Testing Started',
                'date' => $request->actual_start_date->format('Y-m-d'),
                'active' => true,
            ];
        }
        
        if ($request->actual_completion_date) {
            $timeline[] = [
                'status' => 'completed',
                'label' => 'Testing Completed',
                'date' => $request->actual_completion_date->format('Y-m-d'),
                'active' => true,
            ];
        }
        
        return $timeline;
    }
}