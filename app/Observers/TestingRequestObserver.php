<?php

namespace App\Observers;

use App\Models\TestingRequest;
use App\Services\NotificationService;
use Illuminate\Support\Facades\Log;

class TestingRequestObserver
{
    private $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Handle the TestingRequest "created" event.
     */
    public function created(TestingRequest $testingRequest): void
    {
        try {
            // Send submission notification
            $this->notificationService->sendTestingRequestNotifications($testingRequest, 'submitted');
            
            Log::info('Testing request created, notifications sent', [
                'request_id' => $testingRequest->request_id,
                'client' => $testingRequest->client_name,
                'organization' => $testingRequest->client_organization,
                'testing_type' => $testingRequest->testing_type
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to send testing request creation notifications', [
                'request_id' => $testingRequest->request_id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Handle the TestingRequest "updated" event.
     */
    public function updated(TestingRequest $testingRequest): void
    {
        try {
            // Check if status changed
            if ($testingRequest->wasChanged('status')) {
                $oldStatus = $testingRequest->getOriginal('status');
                $newStatus = $testingRequest->status;

                Log::info('Testing request status changed', [
                    'request_id' => $testingRequest->request_id,
                    'from' => $oldStatus,
                    'to' => $newStatus
                ]);

                // Send appropriate notification based on new status
                switch ($newStatus) {
                    case 'approved':
                        $this->notificationService->sendTestingRequestNotifications($testingRequest, 'approved');
                        break;
                        
                    case 'rejected':
                        $this->notificationService->sendTestingRequestNotifications($testingRequest, 'rejected');
                        break;
                        
                    case 'in_progress':
                        $this->notificationService->sendTestingRequestNotifications($testingRequest, 'in_progress');
                        break;
                        
                    case 'completed':
                        $this->notificationService->sendTestingRequestNotifications($testingRequest, 'completed');
                        break;
                }
            }

        } catch (\Exception $e) {
            Log::error('Failed to handle testing request update notifications', [
                'request_id' => $testingRequest->request_id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Handle the TestingRequest "deleted" event.
     */
    public function deleted(TestingRequest $testingRequest): void
    {
        Log::info('Testing request deleted', [
            'request_id' => $testingRequest->request_id,
            'client' => $testingRequest->client_name
        ]);
    }

    /**
     * Handle the TestingRequest "restored" event.
     */
    public function restored(TestingRequest $testingRequest): void
    {
        Log::info('Testing request restored', [
            'request_id' => $testingRequest->request_id
        ]);
    }

    /**
     * Handle the TestingRequest "force deleted" event.
     */
    public function forceDeleted(TestingRequest $testingRequest): void
    {
        Log::info('Testing request force deleted', [
            'request_id' => $testingRequest->request_id
        ]);
    }
}