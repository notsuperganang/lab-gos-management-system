<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ActivityLogsRequest;
use App\Http\Requests\Admin\DashboardStatsRequest;
use App\Http\Resources\Admin\ActivityLogCollection;
use App\Http\Resources\Admin\DashboardStatsResource;
use App\Http\Resources\ApiResponse;
use App\Services\DashboardService;
use Illuminate\Http\JsonResponse;

class DashboardController extends Controller
{
    public function __construct(
        private readonly DashboardService $dashboardService
    ) {
        // Middleware is handled by routes in web.php
    }

    /**
     * Get comprehensive dashboard statistics
     */
    public function statistics(DashboardStatsRequest $request): JsonResponse
    {
        try {
            $validatedData = $request->validatedWithDefaults();
            
            $stats = $this->dashboardService->getDashboardStats(
                dateFrom: $validatedData['date_from'],
                dateTo: $validatedData['date_to'],
                refreshCache: $validatedData['refresh_cache']
            );

            return ApiResponse::success(
                new DashboardStatsResource($stats),
                'Dashboard statistics retrieved successfully'
            );

        } catch (\Exception $e) {
            \Log::error('Dashboard statistics error: ' . $e->getMessage(), [
                'user_id' => $request->user()?->id,
                'request_data' => $request->validated(),
                'exception' => $e,
            ]);

            return ApiResponse::error(
                'Failed to retrieve dashboard statistics',
                500,
                null,
                config('app.debug') ? $e->getMessage() : null
            );
        }
    }

    /**
     * Get activity logs with filtering and pagination
     */
    public function activityLogs(ActivityLogsRequest $request): JsonResponse
    {
        try {
            $validatedData = $request->validatedWithDefaults();
            
            $activities = $this->dashboardService->getRecentActivities(
                limit: $validatedData['per_page'],
                filters: array_filter([
                    'type' => $validatedData['type'] ?? null,
                    'user_id' => $validatedData['user_id'] ?? null,
                    'search' => $validatedData['search'] ?? null,
                    'date_from' => $validatedData['date_from'] ?? null,
                    'date_to' => $validatedData['date_to'] ?? null,
                ])
            );

            return ApiResponse::success(
                new ActivityLogCollection(collect($activities)),
                'Activity logs retrieved successfully'
            );

        } catch (\Exception $e) {
            \Log::error('Activity logs error: ' . $e->getMessage(), [
                'user_id' => $request->user()?->id,
                'request_data' => $request->validated(),
                'exception' => $e,
            ]);

            return ApiResponse::error(
                'Failed to retrieve activity logs',
                500,
                null,
                config('app.debug') ? $e->getMessage() : null
            );
        }
    }

    /**
     * Get system notifications for admin
     */
    public function notifications(): JsonResponse
    {
        try {
            // For now, return a simple structure
            // This can be expanded later with actual notification logic
            $notifications = [];

            return ApiResponse::success(
                $notifications,
                'Notifications retrieved successfully'
            );

        } catch (\Exception $e) {
            \Log::error('Notifications error: ' . $e->getMessage(), [
                'exception' => $e,
            ]);

            return ApiResponse::error(
                'Failed to retrieve notifications',
                500,
                null,
                config('app.debug') ? $e->getMessage() : null
            );
        }
    }
}