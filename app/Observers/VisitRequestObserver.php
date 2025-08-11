<?php

namespace App\Observers;

use App\Models\VisitRequest;
use App\Services\NotificationService;
use Illuminate\Support\Facades\Log;

class VisitRequestObserver
{
    private $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Handle the VisitRequest "created" event.
     */
    public function created(VisitRequest $visitRequest): void
    {
        try {
            // Send submission notification
            $this->notificationService->sendVisitRequestNotifications($visitRequest, 'submitted');
            
            Log::info('Visit request created, notifications sent', [
                'request_id' => $visitRequest->request_id,
                'visitor' => $visitRequest->full_name,
                'institution' => $visitRequest->institution
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to send visit request creation notifications', [
                'request_id' => $visitRequest->request_id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Handle the VisitRequest "updated" event.
     */
    public function updated(VisitRequest $visitRequest): void
    {
        try {
            // Check if status changed
            if ($visitRequest->wasChanged('status')) {
                $oldStatus = $visitRequest->getOriginal('status');
                $newStatus = $visitRequest->status;

                Log::info('Visit request status changed', [
                    'request_id' => $visitRequest->request_id,
                    'from' => $oldStatus,
                    'to' => $newStatus
                ]);

                // Send appropriate notification based on new status
                switch ($newStatus) {
                    case 'approved':
                        $this->notificationService->sendVisitRequestNotifications($visitRequest, 'approved');
                        break;
                        
                    case 'rejected':
                        $this->notificationService->sendVisitRequestNotifications($visitRequest, 'rejected');
                        break;
                        
                    case 'completed':
                        // Can add completion notification
                        break;
                }
            }

        } catch (\Exception $e) {
            Log::error('Failed to handle visit request update notifications', [
                'request_id' => $visitRequest->request_id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Handle the VisitRequest "deleted" event.
     */
    public function deleted(VisitRequest $visitRequest): void
    {
        Log::info('Visit request deleted', [
            'request_id' => $visitRequest->request_id,
            'visitor' => $visitRequest->full_name
        ]);
    }

    /**
     * Handle the VisitRequest "restored" event.
     */
    public function restored(VisitRequest $visitRequest): void
    {
        Log::info('Visit request restored', [
            'request_id' => $visitRequest->request_id
        ]);
    }

    /**
     * Handle the VisitRequest "force deleted" event.
     */
    public function forceDeleted(VisitRequest $visitRequest): void
    {
        Log::info('Visit request force deleted', [
            'request_id' => $visitRequest->request_id
        ]);
    }
}