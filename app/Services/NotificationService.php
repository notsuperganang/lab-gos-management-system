<?php

namespace App\Services;

use App\Models\BorrowRequest;
use App\Models\VisitRequest;
use App\Models\TestingRequest;
use Illuminate\Support\Facades\Log;

class NotificationService
{
    private $whatsAppService;

    public function __construct(WhatsAppService $whatsAppService)
    {
        $this->whatsAppService = $whatsAppService;
    }

    /**
     * Send borrow request notifications
     */
    public function sendBorrowRequestNotifications(BorrowRequest $borrowRequest, string $event): void
    {
        try {
            $variables = $this->getBorrowRequestVariables($borrowRequest);

            switch ($event) {
                case 'submitted':
                    // Notify user
                    $this->whatsAppService->sendTemplateMessage(
                        $borrowRequest->supervisor_phone,
                        'borrow-request-submitted',
                        $variables
                    );

                    // Notify admins
                    $this->whatsAppService->sendToAdmins(
                        'admin-new-borrow-request',
                        array_merge($variables, [
                            'admin_dashboard_url' => url('/admin/requests/borrow/' . $borrowRequest->id)
                        ])
                    );
                    break;

                case 'approved':
                    $this->whatsAppService->sendTemplateMessage(
                        $borrowRequest->supervisor_phone,
                        'borrow-request-approved',
                        array_merge($variables, [
                            'approved_by' => $borrowRequest->reviewer->name ?? 'Admin',
                            'approved_at' => $borrowRequest->reviewed_at?->format('d M Y H:i'),
                            'approval_notes' => $borrowRequest->approval_notes ?? 'Tidak ada catatan khusus',
                            'equipment_list' => $this->formatEquipmentList($borrowRequest->borrowRequestItems)
                        ])
                    );
                    break;

                case 'rejected':
                    $this->whatsAppService->sendTemplateMessage(
                        $borrowRequest->supervisor_phone,
                        'borrow-request-rejected',
                        array_merge($variables, [
                            'reviewed_by' => $borrowRequest->reviewer->name ?? 'Admin',
                            'reviewed_at' => $borrowRequest->reviewed_at?->format('d M Y H:i'),
                            'rejection_reason' => $borrowRequest->approval_notes ?? 'Tidak disebutkan'
                        ])
                    );
                    break;

                case 'completed':
                    // Can add completion notification if needed
                    break;
            }

            Log::info('Borrow request notification sent', [
                'request_id' => $borrowRequest->request_id,
                'event' => $event,
                'phone' => substr($borrowRequest->supervisor_phone, 0, 3) . '***'
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to send borrow request notification', [
                'request_id' => $borrowRequest->request_id,
                'event' => $event,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Send visit request notifications
     */
    public function sendVisitRequestNotifications(VisitRequest $visitRequest, string $event): void
    {
        try {
            $variables = $this->getVisitRequestVariables($visitRequest);

            switch ($event) {
                case 'submitted':
                    // Notify user
                    $this->whatsAppService->sendTemplateMessage(
                        $visitRequest->phone,
                        'visit-request-submitted',
                        $variables
                    );

                    // Notify admins
                    $this->whatsAppService->sendToAdmins(
                        'admin-new-visit-request',
                        array_merge($variables, [
                            'admin_dashboard_url' => url('/admin/requests/visit/' . $visitRequest->id)
                        ])
                    );
                    break;

                case 'approved':
                    $this->whatsAppService->sendTemplateMessage(
                        $visitRequest->phone,
                        'visit-request-approved',
                        array_merge($variables, [
                            'approved_by' => $visitRequest->reviewer->name ?? 'Admin',
                            'approved_at' => $visitRequest->reviewed_at?->format('d M Y H:i')
                        ])
                    );
                    break;

                case 'rejected':
                    // Can add rejection template if needed
                    break;
            }

            Log::info('Visit request notification sent', [
                'request_id' => $visitRequest->request_id,
                'event' => $event,
                'phone' => substr($visitRequest->phone, 0, 3) . '***'
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to send visit request notification', [
                'request_id' => $visitRequest->request_id,
                'event' => $event,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Send testing request notifications
     */
    public function sendTestingRequestNotifications(TestingRequest $testingRequest, string $event): void
    {
        try {
            $variables = $this->getTestingRequestVariables($testingRequest);

            switch ($event) {
                case 'submitted':
                    // Notify client
                    $this->whatsAppService->sendTemplateMessage(
                        $testingRequest->client_phone,
                        'testing-request-submitted',
                        $variables
                    );

                    // Notify admins
                    $this->whatsAppService->sendToAdmins(
                        'admin-new-testing-request',
                        array_merge($variables, [
                            'admin_dashboard_url' => url('/admin/requests/testing/' . $testingRequest->id)
                        ])
                    );
                    break;

                case 'approved':
                    $this->whatsAppService->sendTemplateMessage(
                        $testingRequest->client_phone,
                        'testing-request-approved',
                        array_merge($variables, [
                            'approved_by' => $testingRequest->reviewer->name ?? 'Admin',
                            'approved_at' => $testingRequest->reviewed_at?->format('d M Y H:i'),
                            'scheduled_date' => $testingRequest->scheduled_date?->format('d M Y'),
                            'estimated_completion' => $testingRequest->estimated_completion?->format('d M Y'),
                            'total_cost' => number_format($testingRequest->total_cost ?? 0, 0, ',', '.'),
                            'delivery_time' => $testingRequest->estimated_delivery_days ?? 7,
                            'lab_manager_phone' => config('whatsapp.lab_phone')
                        ])
                    );
                    break;

                case 'in_progress':
                    // Can add in-progress notification
                    break;

                case 'completed':
                    // Can add completion notification with results
                    break;
            }

            Log::info('Testing request notification sent', [
                'request_id' => $testingRequest->request_id,
                'event' => $event,
                'phone' => substr($testingRequest->client_phone, 0, 3) . '***'
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to send testing request notification', [
                'request_id' => $testingRequest->request_id,
                'event' => $event,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Get borrow request variables for templates
     */
    private function getBorrowRequestVariables(BorrowRequest $borrowRequest): array
    {
        return [
            'request_id' => $borrowRequest->request_id,
            'supervisor_name' => $borrowRequest->supervisor_name,
            'supervisor_email' => $borrowRequest->supervisor_email,
            'supervisor_phone' => $borrowRequest->supervisor_phone,
            'purpose' => $borrowRequest->purpose,
            'borrow_date' => $borrowRequest->borrow_date->format('d M Y'),
            'return_date' => $borrowRequest->return_date->format('d M Y'),
            'start_time' => $borrowRequest->start_time?->format('H:i'),
            'end_time' => $borrowRequest->end_time?->format('H:i'),
            'duration' => $borrowRequest->borrow_date->diffInDays($borrowRequest->return_date) + 1,
            'member_count' => count($borrowRequest->members ?? []),
            'submitted_at' => $borrowRequest->submitted_at?->format('d M Y H:i'),
            'tracking_url' => route('api.tracking.borrow', $borrowRequest->request_id),
            'equipment_summary' => $this->getEquipmentSummary($borrowRequest->borrowRequestItems),
        ];
    }

    /**
     * Get visit request variables for templates
     */
    private function getVisitRequestVariables(VisitRequest $visitRequest): array
    {
        return [
            'request_id' => $visitRequest->request_id,
            'full_name' => $visitRequest->full_name,
            'email' => $visitRequest->email,
            'phone' => $visitRequest->phone,
            'institution' => $visitRequest->institution,
            'purpose' => $visitRequest->purpose,
            'visit_date' => $visitRequest->visit_date?->format('d M Y'),
            'visit_time' => $visitRequest->visit_time,
            'participants' => $visitRequest->participants,
            'additional_notes' => $visitRequest->additional_notes ?: 'Tidak ada',
            'submitted_at' => $visitRequest->submitted_at?->format('d M Y H:i'),
            'tracking_url' => route('api.tracking.visit', $visitRequest->request_id),
        ];
    }

    /**
     * Get testing request variables for templates
     */
    private function getTestingRequestVariables(TestingRequest $testingRequest): array
    {
        return [
            'request_id' => $testingRequest->request_id,
            'client_name' => $testingRequest->client_name,
            'client_organization' => $testingRequest->client_organization,
            'client_email' => $testingRequest->client_email,
            'client_phone' => $testingRequest->client_phone,
            'sample_name' => $testingRequest->sample_name,
            'sample_description' => $testingRequest->sample_description,
            'sample_quantity' => $testingRequest->sample_quantity,
            'testing_type' => $testingRequest->testing_type,
            'testing_parameters' => is_array($testingRequest->testing_parameters) 
                ? implode(', ', $testingRequest->testing_parameters)
                : $testingRequest->testing_parameters,
            'preferred_date' => $testingRequest->preferred_date?->format('d M Y'),
            'estimated_duration_hours' => $testingRequest->estimated_duration_hours,
            'urgent_status' => $testingRequest->urgent_request ? 'Ya' : 'Tidak',
            'submitted_at' => $testingRequest->submitted_at?->format('d M Y H:i'),
            'tracking_url' => route('api.tracking.testing', $testingRequest->request_id),
        ];
    }

    /**
     * Get equipment summary for borrow requests
     */
    private function getEquipmentSummary($borrowRequestItems): string
    {
        if (!$borrowRequestItems || $borrowRequestItems->isEmpty()) {
            return 'Tidak ada alat';
        }

        $summary = [];
        foreach ($borrowRequestItems as $item) {
            $equipment = $item->equipment;
            $summary[] = "• {$equipment->name} ({$item->quantity_requested} unit)";
        }

        return implode("\n", $summary);
    }

    /**
     * Format equipment list for approval message
     */
    private function formatEquipmentList($borrowRequestItems): string
    {
        if (!$borrowRequestItems || $borrowRequestItems->isEmpty()) {
            return 'Tidak ada alat';
        }

        $list = [];
        foreach ($borrowRequestItems as $item) {
            $equipment = $item->equipment;
            $approved = $item->quantity_approved ?: $item->quantity_requested;
            $list[] = "• {$equipment->name}: {$approved} unit";
        }

        return implode("\n", $list);
    }
}