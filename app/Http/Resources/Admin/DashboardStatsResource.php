<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DashboardStatsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'summary' => $this->resource['summary'] ?? [],
            'equipment_analytics' => $this->resource['equipment_analytics'] ?? [
                'total_count' => 0,
                'availability' => [
                    'available' => 0,
                    'fully_utilized' => 0,
                    'low_stock' => 0,
                ],
                'status_distribution' => [
                    'active' => 0,
                    'maintenance' => 0,
                    'retired' => 0,
                ],
            ],
            'request_analytics' => $this->resource['request_analytics'] ?? [
                'period_summary' => [
                    'total_requests' => 0,
                    'borrow_requests' => 0,
                    'visit_requests' => 0,
                    'testing_requests' => 0,
                ],
            ],
            'trend_data' => $this->resource['trend_data'] ?? [
                'daily_trends' => [],
            ],
            'quick_insights' => $this->resource['quick_insights'] ?? [
                'most_requested_equipment' => [],
            ],
            'alerts' => $this->resource['alerts'] ?? [],
        ];
    }

    /**
     * Get additional data that should be returned with the resource array.
     */
    public function with(Request $request): array
    {
        return [
            'meta' => [
                'timestamp' => now()->format('Y-m-d H:i:s'),
                'timezone' => config('app.timezone'),
                'cache_duration' => 300, // 5 minutes
            ],
        ];
    }
}