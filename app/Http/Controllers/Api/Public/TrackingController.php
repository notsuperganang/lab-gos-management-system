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
                        'category' => [
                            'name' => $item->equipment->category?->name,
                        ],
                        'specifications' => $item->equipment->specifications,
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
                    'visitor_name' => $visitRequest->visitor_name,
                    'visitor_email' => $visitRequest->visitor_email,
                    'visitor_phone' => $visitRequest->visitor_phone,
                    'institution' => $visitRequest->institution,
                ],
                'visit_purpose' => $visitRequest->visit_purpose,
                'purpose_label' => $visitRequest->purpose_label,
                'visit_date' => $visitRequest->visit_date->format('Y-m-d'),
                'visit_time' => [
                    'start_time' => $visitRequest->start_time,
                    'end_time' => $visitRequest->end_time,
                    'display' => $visitRequest->start_time && $visitRequest->end_time
                        ? $visitRequest->start_time . ' - ' . $visitRequest->end_time . ' WIB'
                        : null
                ],
                'group_size' => $visitRequest->group_size,
                'purpose_description' => $visitRequest->purpose_description,
                'special_requirements' => $visitRequest->special_requirements,
                'equipment_needed' => $visitRequest->equipment_needed,
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
                    'sample_delivery_schedule' => $testingRequest->sample_delivery_schedule?->format('Y-m-d'),
                    'estimated_duration' => $testingRequest->estimated_duration,
                    'estimated_completion_date' => $testingRequest->estimated_completion_date?->format('Y-m-d'),
                    'completion_date' => $testingRequest->completion_date?->format('Y-m-d'),
                ],
                'cost' => [
                    'cost' => $testingRequest->cost,
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

    /**
     * Cancel a borrow request
     */
    public function cancelBorrowRequest(string $requestId): JsonResponse
    {
        try {
            $borrowRequest = BorrowRequest::where('request_id', $requestId)->first();

            if (!$borrowRequest) {
                return response()->json([
                    'success' => false,
                    'message' => 'Request not found. Please check your request ID.'
                ], 404);
            }

            // Check if request can be canceled
            if (in_array($borrowRequest->status, ['approved', 'active', 'completed'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Permohonan yang sudah disetujui atau sedang berlangsung tidak dapat dibatalkan.'
                ], 400);
            }

            if ($borrowRequest->status === 'cancelled') {
                return response()->json([
                    'success' => false,
                    'message' => 'Permohonan sudah dibatalkan sebelumnya.'
                ], 400);
            }

            // Update status to cancelled
            $borrowRequest->update([
                'status' => 'cancelled',
                'approval_notes' => 'Dibatalkan oleh pemohon pada ' . now()->format('d/m/Y H:i'),
                'reviewed_at' => now(),
            ]);

            // Log activity
            activity()
                ->causedBy($borrowRequest->user)
                ->performedOn($borrowRequest)
                ->withProperties([
                    'request_id' => $borrowRequest->request_id,
                    'reason' => 'Dibatalkan oleh pemohon'
                ])
                ->log('Borrow request cancelled');

            return response()->json([
                'success' => true,
                'message' => 'Permohonan peminjaman berhasil dibatalkan.',
                'data' => [
                    'request_id' => $borrowRequest->request_id,
                    'status' => 'cancelled'
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to cancel request',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Cancel a visit request
     */
    public function cancelVisitRequest(string $requestId): JsonResponse
    {
        try {
            $visitRequest = VisitRequest::where('request_id', $requestId)->first();

            if (!$visitRequest) {
                return response()->json([
                    'success' => false,
                    'message' => 'Request not found. Please check your request ID.'
                ], 404);
            }

            // Check if request can be canceled
            if (in_array($visitRequest->status, ['approved', 'ready', 'completed'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Permohonan yang sudah disetujui atau sedang berlangsung tidak dapat dibatalkan.'
                ], 400);
            }

            if ($visitRequest->status === 'cancelled') {
                return response()->json([
                    'success' => false,
                    'message' => 'Permohonan sudah dibatalkan sebelumnya.'
                ], 400);
            }

            // Update status to cancelled
            $visitRequest->update([
                'status' => 'cancelled',
                'approval_notes' => 'Dibatalkan oleh pemohon pada ' . now()->format('d/m/Y H:i'),
                'reviewed_at' => now(),
            ]);

            // Log activity
            activity()
                ->causedBy($visitRequest->user)
                ->performedOn($visitRequest)
                ->withProperties([
                    'request_id' => $visitRequest->request_id,
                    'reason' => 'Dibatalkan oleh pemohon'
                ])
                ->log('Visit request cancelled');

            return response()->json([
                'success' => true,
                'message' => 'Permohonan kunjungan berhasil dibatalkan.',
                'data' => [
                    'request_id' => $visitRequest->request_id,
                    'status' => 'cancelled'
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to cancel visit request',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Cancel a testing request
     */
    public function cancelTestingRequest(string $requestId): JsonResponse
    {
        try {
            $testingRequest = TestingRequest::where('request_id', $requestId)->first();

            if (!$testingRequest) {
                return response()->json([
                    'success' => false,
                    'message' => 'Request not found. Please check your request ID.'
                ], 404);
            }

            // Check if request can be canceled
            if (in_array($testingRequest->status, ['approved', 'in_progress', 'completed'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Permohonan yang sudah disetujui atau sedang berlangsung tidak dapat dibatalkan.'
                ], 400);
            }

            if ($testingRequest->status === 'cancelled') {
                return response()->json([
                    'success' => false,
                    'message' => 'Permohonan sudah dibatalkan sebelumnya.'
                ], 400);
            }

            // Update status to cancelled
            $testingRequest->update([
                'status' => 'cancelled',
                'approval_notes' => 'Dibatalkan oleh pemohon pada ' . now()->format('d/m/Y H:i'),
                'reviewed_at' => now(),
            ]);

            // Log activity
            activity()
                ->causedBy($testingRequest->user ?? null)
                ->performedOn($testingRequest)
                ->withProperties([
                    'request_id' => $testingRequest->request_id,
                    'reason' => 'Dibatalkan oleh pemohon'
                ])
                ->log('Testing request cancelled');

            return response()->json([
                'success' => true,
                'message' => 'Permohonan pengujian berhasil dibatalkan.',
                'data' => [
                    'request_id' => $testingRequest->request_id,
                    'status' => 'cancelled'
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to cancel testing request',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }
}
