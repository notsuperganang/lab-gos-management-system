<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CalendarMonthResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'overall_stats' => [
                'month' => $this->resource['overall_stats']['month'],
                'year' => $this->resource['overall_stats']['year'],
                'month_name' => $this->resource['overall_stats']['month_name'],
                'total_working_days' => $this->resource['overall_stats']['total_working_days'],
                'total_slots' => $this->resource['overall_stats']['total_slots'],
                'total_available_slots' => $this->resource['overall_stats']['total_available_slots'],
                'total_booked_slots' => $this->resource['overall_stats']['total_booked_slots'],
                'total_blocked_slots' => $this->resource['overall_stats']['total_blocked_slots'],
                'overall_availability_percentage' => $this->resource['overall_stats']['overall_availability_percentage'],
                'booking_rate' => $this->resource['overall_stats']['booking_rate'],
                'blocked_rate' => $this->resource['overall_stats']['blocked_rate'],
                'performance_indicator' => $this->getMonthPerformanceIndicator(),
                'busiest_day' => $this->resource['overall_stats']['busiest_day'] ?? null,
                'quietest_day' => $this->resource['overall_stats']['quietest_day'] ?? null,
            ],
            'daily_summary' => collect($this->resource['daily_summary'])->map(function ($day) {
                return [
                    'date' => $day['date'],
                    'formatted_date' => $day['formatted_date'],
                    'day_name' => $day['day_name'],
                    'total_slots' => $day['total_slots'],
                    'available_slots' => $day['available_slots'],
                    'booked_slots' => $day['booked_slots'],
                    'blocked_slots' => $day['blocked_slots'],
                    'availability_percentage' => $day['availability_percentage'],
                    'day_status' => $this->getDayStatus($day),
                    'utilization_level' => $this->getUtilizationLevel($day),
                ];
            })->toArray(),
            'insights' => $this->generateMonthInsights(),
            'trends' => $this->generateTrendAnalysis(),
        ];
    }

    /**
     * Get overall month performance indicator.
     */
    private function getMonthPerformanceIndicator(): array
    {
        $stats = $this->resource['overall_stats'];
        $availability = $stats['overall_availability_percentage'];
        $bookingRate = $stats['booking_rate'];
        $blockedRate = $stats['blocked_rate'];

        if ($availability >= 70) {
            return [
                'level' => 'excellent',
                'label' => 'Excellent Availability',
                'color' => 'success',
                'score' => 'A+',
                'description' => 'High availability with good booking balance'
            ];
        } elseif ($availability >= 50) {
            return [
                'level' => 'good',
                'label' => 'Good Availability',
                'color' => 'primary',
                'score' => 'A',
                'description' => 'Solid availability with room for optimization'
            ];
        } elseif ($availability >= 30) {
            return [
                'level' => 'moderate',
                'label' => 'Moderate Availability',
                'color' => 'warning',
                'score' => 'B+',
                'description' => 'Decent availability but could be improved'
            ];
        } elseif ($availability >= 15) {
            return [
                'level' => 'limited',
                'label' => 'Limited Availability',
                'color' => 'warning',
                'score' => 'B',
                'description' => 'Limited slots available for new bookings'
            ];
        } else {
            if ($bookingRate > $blockedRate) {
                return [
                    'level' => 'high_demand',
                    'label' => 'High Demand',
                    'color' => 'info',
                    'score' => 'B-',
                    'description' => 'Very high booking demand, consider expansion'
                ];
            } else {
                return [
                    'level' => 'heavily_blocked',
                    'label' => 'Heavily Restricted',
                    'color' => 'danger',
                    'score' => 'C',
                    'description' => 'Many slots blocked, review blocking policies'
                ];
            }
        }
    }

    /**
     * Get day status indicator.
     */
    private function getDayStatus(array $day): array
    {
        $availability = $day['availability_percentage'];
        $booked = $day['booked_slots'];
        $blocked = $day['blocked_slots'];
        $total = $day['total_slots'];

        if ($total === 0) {
            return [
                'status' => 'closed',
                'label' => 'Closed',
                'color' => 'secondary',
                'priority' => 0
            ];
        }

        if ($availability >= 75) {
            return [
                'status' => 'available',
                'label' => 'Highly Available',
                'color' => 'success',
                'priority' => 1
            ];
        } elseif ($availability >= 50) {
            return [
                'status' => 'moderate',
                'label' => 'Moderately Available',
                'color' => 'primary',
                'priority' => 2
            ];
        } elseif ($availability >= 25) {
            return [
                'status' => 'limited',
                'label' => 'Limited Availability',
                'color' => 'warning',
                'priority' => 3
            ];
        } elseif ($availability > 0) {
            return [
                'status' => 'scarce',
                'label' => 'Very Limited',
                'color' => 'danger',
                'priority' => 4
            ];
        } else {
            if ($booked > $blocked) {
                return [
                    'status' => 'fully_booked',
                    'label' => 'Fully Booked',
                    'color' => 'info',
                    'priority' => 5
                ];
            } else {
                return [
                    'status' => 'blocked',
                    'label' => 'Unavailable',
                    'color' => 'warning',
                    'priority' => 4
                ];
            }
        }
    }

    /**
     * Get utilization level for a day.
     */
    private function getUtilizationLevel(array $day): array
    {
        if ($day['total_slots'] === 0) {
            return [
                'level' => 'none',
                'label' => 'No Operation',
                'percentage' => 0,
                'color' => 'secondary'
            ];
        }

        $utilization = (($day['booked_slots'] + $day['blocked_slots']) / $day['total_slots']) * 100;

        if ($utilization >= 90) {
            return [
                'level' => 'very_high',
                'label' => 'Very High Utilization',
                'percentage' => round($utilization, 1),
                'color' => 'danger'
            ];
        } elseif ($utilization >= 70) {
            return [
                'level' => 'high',
                'label' => 'High Utilization',
                'percentage' => round($utilization, 1),
                'color' => 'warning'
            ];
        } elseif ($utilization >= 50) {
            return [
                'level' => 'moderate',
                'label' => 'Moderate Utilization',
                'percentage' => round($utilization, 1),
                'color' => 'info'
            ];
        } elseif ($utilization >= 25) {
            return [
                'level' => 'low',
                'label' => 'Low Utilization',
                'percentage' => round($utilization, 1),
                'color' => 'primary'
            ];
        } else {
            return [
                'level' => 'very_low',
                'label' => 'Very Low Utilization',
                'percentage' => round($utilization, 1),
                'color' => 'success'
            ];
        }
    }

    /**
     * Generate insights for the month.
     */
    private function generateMonthInsights(): array
    {
        $stats = $this->resource['overall_stats'];
        $dailySummary = $this->resource['daily_summary'];
        $insights = [];

        // Availability insights
        if ($stats['overall_availability_percentage'] < 30) {
            $insights[] = [
                'type' => 'warning',
                'category' => 'availability',
                'title' => 'Low Availability Alert',
                'message' => 'Overall availability is below 30%. Consider reviewing blocking policies or increasing capacity.',
                'priority' => 'high',
                'actionable' => true
            ];
        }

        // Blocking insights
        if ($stats['blocked_rate'] > 40) {
            $insights[] = [
                'type' => 'info',
                'category' => 'blocking',
                'title' => 'High Blocking Rate',
                'message' => "Over 40% of time slots are blocked. Review if all blocked slots are necessary.",
                'priority' => 'medium',
                'actionable' => true
            ];
        }

        // Booking patterns
        if ($stats['booking_rate'] > 60) {
            $insights[] = [
                'type' => 'success',
                'category' => 'demand',
                'title' => 'High Demand Month',
                'message' => 'Strong booking demand indicates good laboratory utilization.',
                'priority' => 'info',
                'actionable' => false
            ];
        }

        // Weekend analysis
        $weekendSlots = collect($dailySummary)->filter(function ($day) {
            return in_array($day['day_name'], ['Saturday', 'Sunday']);
        });

        if ($weekendSlots->isNotEmpty()) {
            $insights[] = [
                'type' => 'warning',
                'category' => 'operations',
                'title' => 'Weekend Operations Detected',
                'message' => 'Some weekend days have slot data. Verify if this is intended.',
                'priority' => 'low',
                'actionable' => true
            ];
        }

        return $insights;
    }

    /**
     * Generate trend analysis for the month.
     */
    private function generateTrendAnalysis(): array
    {
        $dailySummary = $this->resource['daily_summary'];
        
        if (empty($dailySummary)) {
            return ['message' => 'Insufficient data for trend analysis'];
        }

        // Group by week
        $weeklyData = [];
        foreach ($dailySummary as $day) {
            $week = \Carbon\Carbon::parse($day['date'])->weekOfYear;
            
            if (!isset($weeklyData[$week])) {
                $weeklyData[$week] = [
                    'week' => $week,
                    'days' => 0,
                    'total_slots' => 0,
                    'booked_slots' => 0,
                    'blocked_slots' => 0,
                    'available_slots' => 0,
                ];
            }
            
            $weeklyData[$week]['days']++;
            $weeklyData[$week]['total_slots'] += $day['total_slots'];
            $weeklyData[$week]['booked_slots'] += $day['booked_slots'];
            $weeklyData[$week]['blocked_slots'] += $day['blocked_slots'];
            $weeklyData[$week]['available_slots'] += $day['available_slots'];
        }

        // Calculate weekly averages and trends
        $weeks = array_values($weeklyData);
        $trends = [];

        if (count($weeks) >= 2) {
            $firstWeek = $weeks[0];
            $lastWeek = end($weeks);

            // Booking trend
            $bookingTrend = $lastWeek['booked_slots'] - $firstWeek['booked_slots'];
            $trends['booking_trend'] = [
                'direction' => $bookingTrend > 0 ? 'increasing' : ($bookingTrend < 0 ? 'decreasing' : 'stable'),
                'change' => $bookingTrend,
                'percentage_change' => $firstWeek['booked_slots'] > 0 
                    ? round(($bookingTrend / $firstWeek['booked_slots']) * 100, 1)
                    : 0,
                'interpretation' => $this->interpretTrend('booking', $bookingTrend)
            ];

            // Availability trend
            $availabilityTrend = $lastWeek['available_slots'] - $firstWeek['available_slots'];
            $trends['availability_trend'] = [
                'direction' => $availabilityTrend > 0 ? 'increasing' : ($availabilityTrend < 0 ? 'decreasing' : 'stable'),
                'change' => $availabilityTrend,
                'percentage_change' => $firstWeek['available_slots'] > 0 
                    ? round(($availabilityTrend / $firstWeek['available_slots']) * 100, 1)
                    : 0,
                'interpretation' => $this->interpretTrend('availability', $availabilityTrend)
            ];
        }

        return [
            'weekly_data' => $weeks,
            'trends' => $trends,
            'analysis_period' => [
                'weeks_analyzed' => count($weeks),
                'days_analyzed' => count($dailySummary),
                'has_sufficient_data' => count($weeks) >= 2
            ]
        ];
    }

    /**
     * Interpret trend direction.
     */
    private function interpretTrend(string $type, int $change): string
    {
        if ($type === 'booking') {
            if ($change > 5) {
                return 'Significant increase in bookings - positive demand growth';
            } elseif ($change > 0) {
                return 'Slight increase in booking activity';
            } elseif ($change < -5) {
                return 'Notable decrease in bookings - may need attention';
            } elseif ($change < 0) {
                return 'Slight decrease in booking activity';
            } else {
                return 'Stable booking levels';
            }
        } elseif ($type === 'availability') {
            if ($change > 5) {
                return 'Increased availability - good for new bookings';
            } elseif ($change > 0) {
                return 'Slightly more slots available';
            } elseif ($change < -5) {
                return 'Reduced availability - monitor capacity';
            } elseif ($change < 0) {
                return 'Slightly fewer slots available';
            } else {
                return 'Consistent availability levels';
            }
        }

        return 'Trend analysis not available';
    }
}