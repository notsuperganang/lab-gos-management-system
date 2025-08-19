<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RequestAnalyticsReportResource extends JsonResource
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
                'total_requests' => $this->resource['summary']['total_requests'],
                'date_range' => [
                    'from' => $this->resource['summary']['date_range']['from'],
                    'to' => $this->resource['summary']['date_range']['to'],
                    'days' => $this->resource['summary']['date_range']['days']
                ],
                'average_processing_time_hours' => $this->resource['summary']['average_processing_time_hours'],
                'grouping' => $this->resource['summary']['grouping'],
                'daily_average' => $this->resource['summary']['total_requests'] > 0 && $this->resource['summary']['date_range']['days'] > 0
                    ? round($this->resource['summary']['total_requests'] / $this->resource['summary']['date_range']['days'], 2)
                    : 0
            ],

            'volume_trends' => [
                'title' => 'Request Volume Trends',
                'description' => 'Track request volume over time by type',
                'chart_type' => 'line',
                'data' => collect($this->resource['volume_trends'])->map(function ($trend) {
                    return [
                        'period' => $trend['period'],
                        'period_label' => $trend['period_label'],
                        'borrow_requests' => $trend['borrow_requests'],
                        'visit_requests' => $trend['visit_requests'],
                        'testing_requests' => $trend['testing_requests'],
                        'total_requests' => $trend['total_requests'],
                        'trend_indicator' => $this->getTrendIndicator($trend)
                    ];
                })->toArray(),
                'insights' => $this->getVolumeTrendInsights($this->resource['volume_trends'])
            ],

            'type_distribution' => [
                'title' => 'Request Type Distribution',
                'description' => 'Breakdown of requests by service type',
                'chart_type' => 'pie',
                'data' => collect($this->resource['type_distribution'])->map(function ($type) {
                    return [
                        'type' => $type['type'],
                        'label' => $type['label'],
                        'count' => $type['count'],
                        'percentage' => $type['percentage'],
                        'status_indicator' => $this->getTypeStatusIndicator($type['type'], $type['percentage'])
                    ];
                })->toArray(),
                'total_requests' => collect($this->resource['type_distribution'])->sum('count')
            ],

            'processing_metrics' => [
                'title' => 'Request Processing Performance',
                'description' => 'Analysis of request processing times and efficiency',
                'chart_type' => 'mixed',
                'data' => [
                    'overall_metrics' => [
                        'average_processing_time' => $this->resource['processing_metrics']['average_processing_time'],
                        'min_processing_time' => $this->resource['processing_metrics']['min_processing_time'],
                        'max_processing_time' => $this->resource['processing_metrics']['max_processing_time'],
                        'total_processed_requests' => $this->resource['processing_metrics']['total_processed_requests'],
                        'performance_grade' => $this->getProcessingPerformanceGrade($this->resource['processing_metrics']['average_processing_time'])
                    ],
                    'by_request_type' => collect($this->resource['processing_metrics']['by_request_type'])->map(function ($typeMetric) {
                        return [
                            'type' => $typeMetric['type'],
                            'type_label' => ucfirst($typeMetric['type']) . ' Requests',
                            'count' => $typeMetric['count'],
                            'avg_processing_time' => $typeMetric['avg_processing_time'],
                            'min_processing_time' => $typeMetric['min_processing_time'],
                            'max_processing_time' => $typeMetric['max_processing_time'],
                            'efficiency_rating' => $this->getEfficiencyRating($typeMetric['avg_processing_time'])
                        ];
                    })->toArray()
                ],
                'recommendations' => $this->getProcessingRecommendations($this->resource['processing_metrics'])
            ],

            'success_rates' => [
                'title' => 'Request Success & Completion Rates',
                'description' => 'Analysis of approval, rejection, and completion rates by request type',
                'chart_type' => 'stacked_bar',
                'data' => collect($this->resource['success_rates'])->map(function ($rate) {
                    return [
                        'request_type' => $rate['request_type'],
                        'type_label' => ucfirst($rate['request_type']) . ' Requests',
                        'total_requests' => $rate['total_requests'],
                        'approved_count' => $rate['approved_count'],
                        'rejected_count' => $rate['rejected_count'],
                        'completed_count' => $rate['completed_count'],
                        'approval_rate' => $rate['approval_rate'],
                        'rejection_rate' => $rate['rejection_rate'],
                        'completion_rate' => $rate['completion_rate'],
                        'success_indicator' => $this->getSuccessIndicator($rate),
                        'performance_metrics' => [
                            'approval_status' => $this->getRateStatus($rate['approval_rate'], 'approval'),
                            'completion_status' => $this->getRateStatus($rate['completion_rate'], 'completion')
                        ]
                    ];
                })->toArray(),
                'overall_performance' => $this->getOverallSuccessPerformance($this->resource['success_rates'])
            ],

            'popular_equipment' => [
                'title' => 'Most Requested Equipment',
                'description' => 'Equipment with highest demand across all request types',
                'chart_type' => 'horizontal_bar',
                'data' => collect($this->resource['popular_equipment'])->map(function ($equipment) {
                    return [
                        'equipment_id' => $equipment['equipment_id'],
                        'equipment_name' => $equipment['equipment_name'],
                        'equipment_model' => $equipment['equipment_model'],
                        'category_name' => $equipment['category_name'],
                        'request_count' => $equipment['request_count'],
                        'total_quantity_requested' => $equipment['total_quantity_requested'],
                        'popularity_score' => $this->calculatePopularityScore($equipment),
                        'demand_level' => $this->getDemandLevel($equipment['request_count'])
                    ];
                })->toArray(),
                'insights' => $this->getPopularEquipmentInsights($this->resource['popular_equipment'])
            ],

            'peak_periods' => [
                'title' => 'Peak Request Periods',
                'description' => 'Identify optimal and busy times for laboratory services',
                'chart_type' => 'heatmap',
                'data' => [
                    'hourly_patterns' => [
                        'title' => 'Peak Hours Analysis',
                        'chart_type' => 'column',
                        'data' => collect($this->resource['peak_periods']['hourly_peaks'])->map(function ($hourlyPeak) {
                            return [
                                'hour' => $hourlyPeak['hour'],
                                'hour_label' => $hourlyPeak['hour_label'],
                                'request_count' => $hourlyPeak['request_count'],
                                'peak_level' => $this->getPeakLevel($hourlyPeak['request_count'], $this->resource['peak_periods']['total_requests_analyzed']),
                                'business_hours' => $this->isBusinessHours($hourlyPeak['hour'])
                            ];
                        })->toArray()
                    ],
                    'weekly_patterns' => [
                        'title' => 'Weekly Request Patterns',
                        'chart_type' => 'radar',
                        'data' => collect($this->resource['peak_periods']['weekly_peaks'])->map(function ($weeklyPeak) {
                            return [
                                'day_of_week' => $weeklyPeak['day_of_week'],
                                'day_name' => $weeklyPeak['day_name'],
                                'request_count' => $weeklyPeak['request_count'],
                                'peak_level' => $this->getPeakLevel($weeklyPeak['request_count'], $this->resource['peak_periods']['total_requests_analyzed']),
                                'is_weekend' => in_array($weeklyPeak['day_of_week'], [0, 6]) // Sunday = 0, Saturday = 6
                            ];
                        })->toArray()
                    ]
                ],
                'total_analyzed' => $this->resource['peak_periods']['total_requests_analyzed'],
                'operational_insights' => $this->getOperationalInsights($this->resource['peak_periods'])
            ]
        ];
    }

    /**
     * Get trend indicator for volume data
     */
    private function getTrendIndicator(array $trend): array
    {
        $total = $trend['total_requests'];
        
        if ($total == 0) {
            return ['status' => 'none', 'label' => 'No Activity', 'color' => 'muted'];
        } elseif ($total >= 20) {
            return ['status' => 'high', 'label' => 'High Activity', 'color' => 'danger'];
        } elseif ($total >= 10) {
            return ['status' => 'moderate', 'label' => 'Moderate Activity', 'color' => 'warning'];
        } else {
            return ['status' => 'low', 'label' => 'Low Activity', 'color' => 'info'];
        }
    }

    /**
     * Get volume trend insights
     */
    private function getVolumeTrendInsights(array $trends): array
    {
        $insights = [];
        $totalPeriods = count($trends);
        
        if ($totalPeriods > 0) {
            $averageRequests = collect($trends)->avg('total_requests');
            $maxRequests = collect($trends)->max('total_requests');
            $peakPeriod = collect($trends)->where('total_requests', $maxRequests)->first();
            
            $insights[] = [
                'type' => 'average',
                'title' => 'Average Daily Volume',
                'value' => round($averageRequests, 1),
                'description' => 'Average requests per period'
            ];
            
            if ($peakPeriod) {
                $insights[] = [
                    'type' => 'peak',
                    'title' => 'Peak Period',
                    'value' => $peakPeriod['period_label'],
                    'description' => "{$maxRequests} requests in peak period"
                ];
            }
        }
        
        return $insights;
    }

    /**
     * Get type status indicator
     */
    private function getTypeStatusIndicator(string $type, float $percentage): array
    {
        return match($type) {
            'borrow' => [
                'expected_range' => '60-80%',
                'status' => $percentage >= 60 ? 'healthy' : 'below_expected',
                'color' => $percentage >= 60 ? 'success' : 'warning'
            ],
            'visit' => [
                'expected_range' => '10-25%',
                'status' => $percentage >= 10 && $percentage <= 25 ? 'healthy' : 'unusual',
                'color' => $percentage >= 10 && $percentage <= 25 ? 'success' : 'info'
            ],
            'testing' => [
                'expected_range' => '5-20%',
                'status' => $percentage >= 5 && $percentage <= 20 ? 'healthy' : 'unusual',
                'color' => $percentage >= 5 && $percentage <= 20 ? 'success' : 'warning'
            ],
            default => [
                'expected_range' => 'Variable',
                'status' => 'normal',
                'color' => 'primary'
            ]
        };
    }

    /**
     * Get processing performance grade
     */
    private function getProcessingPerformanceGrade(float $averageHours): array
    {
        if ($averageHours <= 4) {
            return [
                'grade' => 'A+',
                'label' => 'Excellent Response Time',
                'color' => 'success',
                'description' => 'Very fast processing - same day response'
            ];
        } elseif ($averageHours <= 24) {
            return [
                'grade' => 'A',
                'label' => 'Very Good Response Time',
                'color' => 'success',
                'description' => 'Next day processing'
            ];
        } elseif ($averageHours <= 48) {
            return [
                'grade' => 'B+',
                'label' => 'Good Response Time',
                'color' => 'primary',
                'description' => 'Within 2 business days'
            ];
        } elseif ($averageHours <= 72) {
            return [
                'grade' => 'B',
                'label' => 'Acceptable Response Time',
                'color' => 'info',
                'description' => 'Within 3 business days'
            ];
        } elseif ($averageHours <= 120) {
            return [
                'grade' => 'C',
                'label' => 'Slow Response Time',
                'color' => 'warning',
                'description' => 'Taking up to 5 business days'
            ];
        } else {
            return [
                'grade' => 'D',
                'label' => 'Poor Response Time',
                'color' => 'danger',
                'description' => 'More than 5 business days - needs improvement'
            ];
        }
    }

    /**
     * Get efficiency rating
     */
    private function getEfficiencyRating(float $avgProcessingTime): array
    {
        if ($avgProcessingTime <= 12) {
            return ['rating' => 'excellent', 'label' => 'Excellent', 'color' => 'success'];
        } elseif ($avgProcessingTime <= 24) {
            return ['rating' => 'good', 'label' => 'Good', 'color' => 'primary'];
        } elseif ($avgProcessingTime <= 48) {
            return ['rating' => 'fair', 'label' => 'Fair', 'color' => 'warning'];
        } else {
            return ['rating' => 'poor', 'label' => 'Needs Improvement', 'color' => 'danger'];
        }
    }

    /**
     * Get processing recommendations
     */
    private function getProcessingRecommendations(array $metrics): array
    {
        $recommendations = [];
        $avgTime = $metrics['average_processing_time'];

        if ($avgTime > 48) {
            $recommendations[] = [
                'type' => 'urgency',
                'priority' => 'high',
                'title' => 'Reduce Processing Time',
                'description' => 'Average processing time exceeds 48 hours. Consider streamlining approval processes.',
                'actions' => [
                    'Review current approval workflow',
                    'Consider automated pre-screening',
                    'Add more admin staff during peak periods'
                ]
            ];
        }

        if ($metrics['total_processed_requests'] < 50) {
            $recommendations[] = [
                'type' => 'data_quality',
                'priority' => 'medium',
                'title' => 'Increase Data Sample',
                'description' => 'Limited data may not represent typical performance. Expand reporting period for better insights.',
                'actions' => [
                    'Extend reporting period',
                    'Track processing times consistently',
                    'Implement automated time tracking'
                ]
            ];
        }

        return $recommendations;
    }

    /**
     * Get success indicator
     */
    private function getSuccessIndicator(array $rate): array
    {
        $approvalRate = $rate['approval_rate'];
        $completionRate = $rate['completion_rate'];

        if ($approvalRate >= 80 && $completionRate >= 90) {
            return ['status' => 'excellent', 'label' => 'Excellent Performance', 'color' => 'success'];
        } elseif ($approvalRate >= 70 && $completionRate >= 80) {
            return ['status' => 'good', 'label' => 'Good Performance', 'color' => 'primary'];
        } elseif ($approvalRate >= 60 && $completionRate >= 70) {
            return ['status' => 'fair', 'label' => 'Fair Performance', 'color' => 'warning'];
        } else {
            return ['status' => 'poor', 'label' => 'Needs Improvement', 'color' => 'danger'];
        }
    }

    /**
     * Get rate status
     */
    private function getRateStatus(float $rate, string $type): array
    {
        $thresholds = match($type) {
            'approval' => ['excellent' => 85, 'good' => 75, 'fair' => 65],
            'completion' => ['excellent' => 90, 'good' => 80, 'fair' => 70],
            default => ['excellent' => 80, 'good' => 70, 'fair' => 60]
        };

        if ($rate >= $thresholds['excellent']) {
            return ['status' => 'excellent', 'color' => 'success'];
        } elseif ($rate >= $thresholds['good']) {
            return ['status' => 'good', 'color' => 'primary'];
        } elseif ($rate >= $thresholds['fair']) {
            return ['status' => 'fair', 'color' => 'warning'];
        } else {
            return ['status' => 'poor', 'color' => 'danger'];
        }
    }

    /**
     * Get overall success performance
     */
    private function getOverallSuccessPerformance(array $successRates): array
    {
        if (empty($successRates)) {
            return ['status' => 'no_data', 'message' => 'No data available'];
        }

        $avgApprovalRate = collect($successRates)->avg('approval_rate');
        $avgCompletionRate = collect($successRates)->avg('completion_rate');
        $totalRequests = collect($successRates)->sum('total_requests');

        return [
            'average_approval_rate' => round($avgApprovalRate, 2),
            'average_completion_rate' => round($avgCompletionRate, 2),
            'total_requests_analyzed' => $totalRequests,
            'overall_grade' => $this->getOverallGrade($avgApprovalRate, $avgCompletionRate),
            'performance_summary' => $this->getPerformanceSummary($avgApprovalRate, $avgCompletionRate)
        ];
    }

    /**
     * Calculate popularity score
     */
    private function calculatePopularityScore(array $equipment): float
    {
        // Simple scoring algorithm: weighted by request count and quantity
        $requestWeight = $equipment['request_count'] * 10;
        $quantityWeight = $equipment['total_quantity_requested'] * 2;
        return round(($requestWeight + $quantityWeight) / 12, 1);
    }

    /**
     * Get demand level
     */
    private function getDemandLevel(int $requestCount): array
    {
        if ($requestCount >= 20) {
            return ['level' => 'very_high', 'label' => 'Very High Demand', 'color' => 'danger'];
        } elseif ($requestCount >= 15) {
            return ['level' => 'high', 'label' => 'High Demand', 'color' => 'warning'];
        } elseif ($requestCount >= 10) {
            return ['level' => 'moderate', 'label' => 'Moderate Demand', 'color' => 'info'];
        } elseif ($requestCount >= 5) {
            return ['level' => 'low', 'label' => 'Low Demand', 'color' => 'secondary'];
        } else {
            return ['level' => 'minimal', 'label' => 'Minimal Demand', 'color' => 'light'];
        }
    }

    /**
     * Get popular equipment insights
     */
    private function getPopularEquipmentInsights(array $equipment): array
    {
        if (empty($equipment)) {
            return ['message' => 'No equipment data available for analysis'];
        }

        $totalRequests = collect($equipment)->sum('request_count');
        $topEquipment = collect($equipment)->first();
        $categories = collect($equipment)->groupBy('category_name');

        return [
            'top_equipment' => [
                'name' => $topEquipment['equipment_name'] ?? 'N/A',
                'requests' => $topEquipment['request_count'] ?? 0,
                'market_share' => $totalRequests > 0 ? round(($topEquipment['request_count'] / $totalRequests) * 100, 1) : 0
            ],
            'category_leaders' => $categories->map(function ($items, $category) {
                $leader = $items->sortByDesc('request_count')->first();
                return [
                    'category' => $category,
                    'leader' => $leader['equipment_name'],
                    'requests' => $leader['request_count']
                ];
            })->values()->take(3)->toArray(),
            'total_equipment_analyzed' => count($equipment)
        ];
    }

    /**
     * Get peak level
     */
    private function getPeakLevel(int $count, int $total): array
    {
        if ($total == 0) {
            return ['level' => 'none', 'label' => 'No Activity', 'intensity' => 0];
        }

        $percentage = ($count / $total) * 100;

        if ($percentage >= 15) {
            return ['level' => 'very_high', 'label' => 'Very High', 'intensity' => 4, 'color' => 'danger'];
        } elseif ($percentage >= 10) {
            return ['level' => 'high', 'label' => 'High', 'intensity' => 3, 'color' => 'warning'];
        } elseif ($percentage >= 5) {
            return ['level' => 'moderate', 'label' => 'Moderate', 'intensity' => 2, 'color' => 'info'];
        } elseif ($percentage > 0) {
            return ['level' => 'low', 'label' => 'Low', 'intensity' => 1, 'color' => 'light'];
        } else {
            return ['level' => 'none', 'label' => 'None', 'intensity' => 0, 'color' => 'muted'];
        }
    }

    /**
     * Check if hour is business hours
     */
    private function isBusinessHours(int $hour): bool
    {
        return $hour >= 8 && $hour <= 17; // 8 AM to 5 PM
    }

    /**
     * Get operational insights
     */
    private function getOperationalInsights(array $peakPeriods): array
    {
        $insights = [];
        
        // Find peak hour
        $peakHour = collect($peakPeriods['hourly_peaks'])
            ->sortByDesc('request_count')
            ->first();

        if ($peakHour) {
            $insights[] = [
                'type' => 'peak_hour',
                'title' => 'Peak Hour Analysis',
                'description' => "Most requests occur at {$peakHour['hour_label']} with {$peakHour['request_count']} requests",
                'recommendation' => $this->isBusinessHours($peakHour['hour']) 
                    ? 'Consider additional staff during this peak hour' 
                    : 'Peak occurs outside business hours - consider extended support'
            ];
        }

        // Find peak day
        $peakDay = collect($peakPeriods['weekly_peaks'])
            ->sortByDesc('request_count')
            ->first();

        if ($peakDay) {
            $insights[] = [
                'type' => 'peak_day',
                'title' => 'Peak Day Analysis',
                'description' => "Most requests occur on {$peakDay['day_name']} with {$peakDay['request_count']} requests",
                'recommendation' => in_array($peakDay['day_of_week'], [0, 6])
                    ? 'Peak occurs on weekend - verify operational hours'
                    : 'Schedule adequate staff for peak day operations'
            ];
        }

        return $insights;
    }

    /**
     * Get overall grade
     */
    private function getOverallGrade(float $approvalRate, float $completionRate): string
    {
        $average = ($approvalRate + $completionRate) / 2;

        if ($average >= 90) return 'A+';
        if ($average >= 85) return 'A';
        if ($average >= 80) return 'B+';
        if ($average >= 75) return 'B';
        if ($average >= 70) return 'C+';
        if ($average >= 65) return 'C';
        return 'D';
    }

    /**
     * Get performance summary
     */
    private function getPerformanceSummary(float $approvalRate, float $completionRate): string
    {
        if ($approvalRate >= 85 && $completionRate >= 90) {
            return 'Excellent performance across all metrics. Laboratory operations are running very efficiently.';
        } elseif ($approvalRate >= 75 && $completionRate >= 80) {
            return 'Good performance with minor areas for improvement. Overall operations are satisfactory.';
        } elseif ($approvalRate >= 65 && $completionRate >= 70) {
            return 'Average performance. Some processes may need optimization to improve efficiency.';
        } else {
            return 'Performance below expectations. Consider reviewing processes and resource allocation.';
        }
    }
}