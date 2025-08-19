<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EquipmentUsageReportResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'summary' => [
                'total_equipment' => $this->resource['summary']['total_equipment'],
                'active_equipment' => $this->resource['summary']['active_equipment'],
                'total_requests_period' => $this->resource['summary']['total_requests_period'],
                'total_equipment_borrowed' => $this->resource['summary']['total_equipment_borrowed'],
                'date_range' => [
                    'from' => $this->resource['summary']['date_range']['from'],
                    'to' => $this->resource['summary']['date_range']['to'],
                    'days' => $this->resource['summary']['date_range']['days']
                ]
            ],
            
            'most_used_equipment' => [
                'title' => 'Most Used Equipment',
                'description' => 'Top 10 equipment by request frequency',
                'chart_type' => 'bar',
                'data' => collect($this->resource['most_used_equipment'])->map(function ($equipment) {
                    return [
                        'id' => $equipment['id'],
                        'name' => $equipment['name'],
                        'model' => $equipment['model'],
                        'request_count' => $equipment['request_count'],
                        'total_quantity_requested' => $equipment['total_quantity_requested'],
                        'utilization_rate' => $equipment['utilization_rate'],
                        'status_indicator' => $this->getUtilizationStatusIndicator($equipment['utilization_rate'])
                    ];
                })->toArray()
            ],

            'least_used_equipment' => [
                'title' => 'Least Used Equipment',
                'description' => 'Equipment with lowest usage or unused',
                'chart_type' => 'table',
                'data' => collect($this->resource['least_used_equipment'])->map(function ($equipment) {
                    return [
                        'id' => $equipment['id'],
                        'name' => $equipment['name'],
                        'model' => $equipment['model'],
                        'request_count' => $equipment['request_count'],
                        'utilization_rate' => $equipment['utilization_rate'],
                        'availability_status' => $equipment['available_quantity'] > 0 ? 'Available' : 'Unavailable',
                        'recommendation' => $this->getUsageRecommendation($equipment)
                    ];
                })->toArray()
            ],

            'usage_by_category' => [
                'title' => 'Equipment Usage by Category',
                'description' => 'Usage statistics grouped by equipment categories',
                'chart_type' => 'doughnut',
                'data' => collect($this->resource['usage_by_category'])->map(function ($category) {
                    return [
                        'category_id' => $category['category_id'],
                        'category_name' => $category['category_name'],
                        'total_equipment' => $category['total_equipment'],
                        'total_requests' => $category['total_requests'],
                        'total_quantity_requested' => $category['total_quantity_requested'],
                        'utilization_rate' => $category['utilization_rate'],
                        'performance_indicator' => $this->getCategoryPerformanceIndicator($category['utilization_rate'])
                    ];
                })->toArray()
            ],

            'availability_trends' => [
                'title' => 'Equipment Request Trends Over Time',
                'description' => 'Weekly equipment request patterns',
                'chart_type' => 'line',
                'data' => collect($this->resource['availability_trends'])->map(function ($period) {
                    return [
                        'period' => $period['period'],
                        'period_label' => $period['period_label'],
                        'requests_count' => $period['requests_count'],
                        'week_start' => $period['week_start'],
                        'week_end' => $period['week_end']
                    ];
                })->toArray()
            ],

            'peak_usage' => [
                'title' => 'Peak Usage Analysis',
                'description' => 'Identify peak usage patterns across different time dimensions',
                'chart_type' => 'mixed',
                'data' => [
                    'peak_days_of_week' => [
                        'title' => 'Busiest Days of Week',
                        'chart_type' => 'radar',
                        'data' => collect($this->resource['peak_usage']['peak_days_of_week'])->map(function ($day) {
                            return [
                                'day_name' => $day['day_name'],
                                'request_count' => $day['request_count'],
                                'percentage_of_total' => 0 // Will be calculated on frontend
                            ];
                        })->toArray()
                    ],
                    'peak_hours' => [
                        'title' => 'Peak Hours',
                        'chart_type' => 'column',
                        'data' => collect($this->resource['peak_usage']['peak_hours'])->map(function ($hour) {
                            return [
                                'hour' => $hour['hour'],
                                'hour_label' => $hour['hour_label'],
                                'request_count' => $hour['request_count'],
                                'is_peak_hour' => $hour['request_count'] > 0
                            ];
                        })->toArray()
                    ],
                    'peak_months' => [
                        'title' => 'Seasonal Trends',
                        'chart_type' => 'column',
                        'data' => collect($this->resource['peak_usage']['peak_months'])->map(function ($month) {
                            return [
                                'month' => $month['month'],
                                'month_name' => $month['month_name'],
                                'year' => $month['year'],
                                'request_count' => $month['request_count'],
                                'period_label' => $month['month_name'] . ' ' . $month['year']
                            ];
                        })->toArray()
                    ]
                ]
            ],

            'utilization_metrics' => [
                'title' => 'Equipment Utilization Summary',
                'description' => 'Overall utilization statistics across all equipment',
                'chart_type' => 'gauge',
                'data' => [
                    'average_utilization_rate' => $this->resource['utilization_metrics']['average_utilization_rate'],
                    'max_utilization_rate' => $this->resource['utilization_metrics']['max_utilization_rate'],
                    'min_utilization_rate' => $this->resource['utilization_metrics']['min_utilization_rate'],
                    'total_equipment_analyzed' => $this->resource['utilization_metrics']['total_equipment_analyzed'],
                    'utilization_grade' => $this->getUtilizationGrade($this->resource['utilization_metrics']['average_utilization_rate']),
                    'recommendations' => $this->getUtilizationRecommendations($this->resource['utilization_metrics'])
                ]
            ]
        ];
    }

    /**
     * Get utilization status indicator
     */
    private function getUtilizationStatusIndicator(float $utilizationRate): array
    {
        if ($utilizationRate >= 80) {
            return [
                'status' => 'high',
                'label' => 'High Usage',
                'color' => 'success',
                'icon' => 'trending-up'
            ];
        } elseif ($utilizationRate >= 50) {
            return [
                'status' => 'moderate',
                'label' => 'Moderate Usage',
                'color' => 'warning',
                'icon' => 'activity'
            ];
        } elseif ($utilizationRate >= 20) {
            return [
                'status' => 'low',
                'label' => 'Low Usage',
                'color' => 'info',
                'icon' => 'trending-down'
            ];
        } else {
            return [
                'status' => 'minimal',
                'label' => 'Minimal Usage',
                'color' => 'secondary',
                'icon' => 'pause'
            ];
        }
    }

    /**
     * Get usage recommendation for equipment
     */
    private function getUsageRecommendation(array $equipment): string
    {
        if ($equipment['request_count'] == 0) {
            return 'Consider promoting this equipment or reviewing its necessity';
        } elseif ($equipment['utilization_rate'] < 10) {
            return 'Very low usage - evaluate demand or maintenance needs';
        } elseif ($equipment['utilization_rate'] < 30) {
            return 'Below average usage - consider marketing to relevant departments';
        } else {
            return 'Normal usage pattern';
        }
    }

    /**
     * Get category performance indicator
     */
    private function getCategoryPerformanceIndicator(float $utilizationRate): array
    {
        if ($utilizationRate >= 70) {
            return [
                'performance' => 'excellent',
                'label' => 'High Demand Category',
                'color' => 'success',
                'action' => 'Consider expanding equipment in this category'
            ];
        } elseif ($utilizationRate >= 40) {
            return [
                'performance' => 'good',
                'label' => 'Steady Demand',
                'color' => 'primary',
                'action' => 'Maintain current equipment levels'
            ];
        } elseif ($utilizationRate >= 20) {
            return [
                'performance' => 'moderate',
                'label' => 'Moderate Demand',
                'color' => 'warning',
                'action' => 'Monitor usage trends'
            ];
        } else {
            return [
                'performance' => 'low',
                'label' => 'Low Demand Category',
                'color' => 'danger',
                'action' => 'Review category necessity or improve promotion'
            ];
        }
    }

    /**
     * Get overall utilization grade
     */
    private function getUtilizationGrade(float $avgUtilization): array
    {
        if ($avgUtilization >= 80) {
            return [
                'grade' => 'A+',
                'label' => 'Excellent Utilization',
                'color' => 'success',
                'description' => 'Equipment is being used very efficiently'
            ];
        } elseif ($avgUtilization >= 70) {
            return [
                'grade' => 'A',
                'label' => 'Very Good Utilization',
                'color' => 'success',
                'description' => 'Strong equipment usage across the lab'
            ];
        } elseif ($avgUtilization >= 60) {
            return [
                'grade' => 'B+',
                'label' => 'Good Utilization',
                'color' => 'primary',
                'description' => 'Solid equipment usage with room for improvement'
            ];
        } elseif ($avgUtilization >= 50) {
            return [
                'grade' => 'B',
                'label' => 'Fair Utilization',
                'color' => 'info',
                'description' => 'Average usage levels - opportunities for optimization'
            ];
        } elseif ($avgUtilization >= 40) {
            return [
                'grade' => 'C+',
                'label' => 'Below Average',
                'color' => 'warning',
                'description' => 'Equipment underutilized - review strategies needed'
            ];
        } elseif ($avgUtilization >= 30) {
            return [
                'grade' => 'C',
                'label' => 'Poor Utilization',
                'color' => 'warning',
                'description' => 'Significant underutilization - immediate attention required'
            ];
        } else {
            return [
                'grade' => 'D',
                'label' => 'Very Poor Utilization',
                'color' => 'danger',
                'description' => 'Critical underutilization - comprehensive review needed'
            ];
        }
    }

    /**
     * Get utilization recommendations
     */
    private function getUtilizationRecommendations(array $metrics): array
    {
        $recommendations = [];
        $avgUtilization = $metrics['average_utilization_rate'];
        $maxUtilization = $metrics['max_utilization_rate'];

        if ($avgUtilization < 50) {
            $recommendations[] = [
                'type' => 'improvement',
                'priority' => 'high',
                'title' => 'Increase Equipment Promotion',
                'description' => 'Average utilization is below 50%. Consider increasing awareness of available equipment through training sessions and documentation.',
                'action_items' => [
                    'Organize equipment demonstration sessions',
                    'Update equipment catalog with better descriptions',
                    'Send periodic newsletters highlighting underused equipment'
                ]
            ];
        }

        if ($maxUtilization > 90) {
            $recommendations[] = [
                'type' => 'capacity',
                'priority' => 'medium',
                'title' => 'Consider Equipment Expansion',
                'description' => 'Some equipment shows very high utilization (>90%). Consider acquiring additional units for high-demand equipment.',
                'action_items' => [
                    'Identify equipment with >90% utilization',
                    'Analyze request patterns for peak demand periods',
                    'Budget for additional units of popular equipment'
                ]
            ];
        }

        if ($metrics['total_equipment_analyzed'] > 0) {
            $recommendations[] = [
                'type' => 'optimization',
                'priority' => 'low',
                'title' => 'Regular Usage Review',
                'description' => 'Implement monthly equipment usage reviews to identify trends and optimization opportunities.',
                'action_items' => [
                    'Schedule monthly equipment usage meetings',
                    'Create automated usage reports',
                    'Establish equipment performance KPIs'
                ]
            ];
        }

        return $recommendations;
    }
}