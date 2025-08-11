<?php

namespace App\Observers;

use App\Models\BorrowRequest;
use App\Services\NotificationService;
use Illuminate\Support\Facades\Log;

class BorrowRequestObserver
{
    private $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Handle the BorrowRequest "created" event.
     */
    public function created(BorrowRequest $borrowRequest): void
    {
        try {
            // Send submission notification
            $this->notificationService->sendBorrowRequestNotifications($borrowRequest, 'submitted');
            
            Log::info('Borrow request created, notifications sent', [
                'request_id' => $borrowRequest->request_id,
                'supervisor' => $borrowRequest->supervisor_name
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to send borrow request creation notifications', [
                'request_id' => $borrowRequest->request_id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Handle the BorrowRequest "updated" event.
     */
    public function updated(BorrowRequest $borrowRequest): void
    {
        try {
            // Check if status changed
            if ($borrowRequest->wasChanged('status')) {
                $oldStatus = $borrowRequest->getOriginal('status');
                $newStatus = $borrowRequest->status;

                Log::info('Borrow request status changed', [
                    'request_id' => $borrowRequest->request_id,
                    'from' => $oldStatus,
                    'to' => $newStatus
                ]);

                // Send appropriate notification based on new status
                switch ($newStatus) {
                    case 'approved':
                        $this->notificationService->sendBorrowRequestNotifications($borrowRequest, 'approved');
                        break;
                        
                    case 'rejected':
                        $this->notificationService->sendBorrowRequestNotifications($borrowRequest, 'rejected');
                        break;
                        
                    case 'completed':
                        $this->notificationService->sendBorrowRequestNotifications($borrowRequest, 'completed');
                        break;
                        
                    case 'active':
                        // Can add notification for when equipment is ready for pickup
                        break;
                }
            }

        } catch (\Exception $e) {
            Log::error('Failed to handle borrow request update notifications', [
                'request_id' => $borrowRequest->request_id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Handle the BorrowRequest "deleted" event.
     */
    public function deleted(BorrowRequest $borrowRequest): void
    {
        Log::info('Borrow request deleted', [
            'request_id' => $borrowRequest->request_id,
            'supervisor' => $borrowRequest->supervisor_name
        ]);
    }

    /**
     * Handle the BorrowRequest "restored" event.
     */
    public function restored(BorrowRequest $borrowRequest): void
    {
        Log::info('Borrow request restored', [
            'request_id' => $borrowRequest->request_id
        ]);
    }

    /**
     * Handle the BorrowRequest "force deleted" event.
     */
    public function forceDeleted(BorrowRequest $borrowRequest): void
    {
        Log::info('Borrow request force deleted', [
            'request_id' => $borrowRequest->request_id
        ]);
    }
}