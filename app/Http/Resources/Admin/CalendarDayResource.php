<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CalendarDayResource extends JsonResource
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
                'date' => $this->resource['summary']['date'],
                'formatted_date' => $this->resource['summary']['formatted_date'],
                'day_name' => $this->resource['summary']['day_name'],
                'is_weekend' => $this->resource['summary']['is_weekend'],
                'is_past' => $this->resource['summary']['is_past'],
                'total_slots' => $this->resource['summary']['total_slots'],
                'available_slots' => $this->resource['summary']['available_slots'],
                'booked_slots' => $this->resource['summary']['booked_slots'],
                'blocked_slots' => $this->resource['summary']['blocked_slots'],
                'availability_percentage' => $this->resource['summary']['availability_percentage'],
                'status_overview' => $this->getStatusOverview(),
            ],
            'slots' => collect($this->resource['slots'])->map(function ($slot) {
                return [
                    'start_time' => $slot['start_time'],
                    'end_time' => $slot['end_time'],
                    'time_display' => $slot['start_time'] . ' - ' . $slot['end_time'] . ' WIB',
                    'status' => $slot['status'],
                    'status_indicator' => $this->getSlotStatusIndicator($slot['status']),
                    'visit_request' => $this->formatVisitRequest($slot['visit_request'] ?? null),
                    'blocked_info' => $this->formatBlockedInfo($slot['blocked_info'] ?? null),
                    'is_actionable' => $slot['status'] === 'available' || $slot['status'] === 'blocked',
                ];
            })->toArray(),
        ];
    }

    /**
     * Get status overview for the day.
     */
    private function getStatusOverview(): array
    {
        $summary = $this->resource['summary'];
        $total = $summary['total_slots'];

        if ($total === 0) {
            return [
                'level' => 'unavailable',
                'label' => 'No Operating Hours',
                'color' => 'secondary',
                'description' => 'Laboratory is closed on this day'
            ];
        }

        $availablePercentage = $summary['availability_percentage'];
        $bookedPercentage = ($summary['booked_slots'] / $total) * 100;
        $blockedPercentage = ($summary['blocked_slots'] / $total) * 100;

        if ($availablePercentage >= 80) {
            return [
                'level' => 'excellent',
                'label' => 'High Availability',
                'color' => 'success',
                'description' => 'Most time slots are available for booking'
            ];
        } elseif ($availablePercentage >= 50) {
            return [
                'level' => 'good',
                'label' => 'Moderate Availability',
                'color' => 'primary',
                'description' => 'Good number of slots available'
            ];
        } elseif ($availablePercentage >= 25) {
            return [
                'level' => 'limited',
                'label' => 'Limited Availability',
                'color' => 'warning',
                'description' => 'Few slots remain available'
            ];
        } elseif ($availablePercentage > 0) {
            return [
                'level' => 'scarce',
                'label' => 'Very Limited',
                'color' => 'danger',
                'description' => 'Very few slots available'
            ];
        } else {
            if ($bookedPercentage > $blockedPercentage) {
                return [
                    'level' => 'fully_booked',
                    'label' => 'Fully Booked',
                    'color' => 'info',
                    'description' => 'All slots are booked by visitors'
                ];
            } else {
                return [
                    'level' => 'blocked',
                    'label' => 'Unavailable',
                    'color' => 'warning',
                    'description' => 'Slots blocked by admin or fully booked'
                ];
            }
        }
    }

    /**
     * Get status indicator for individual slot.
     */
    private function getSlotStatusIndicator(string $status): array
    {
        return match($status) {
            'available' => [
                'color' => 'success',
                'label' => 'Available',
                'icon' => 'check-circle',
                'description' => 'Available for booking'
            ],
            'booked' => [
                'color' => 'info',
                'label' => 'Booked',
                'icon' => 'user',
                'description' => 'Reserved by visitor'
            ],
            'blocked' => [
                'color' => 'warning',
                'label' => 'Blocked',
                'icon' => 'block',
                'description' => 'Blocked by admin'
            ],
            default => [
                'color' => 'secondary',
                'label' => 'Unknown',
                'icon' => 'question',
                'description' => 'Status unknown'
            ]
        };
    }

    /**
     * Format visit request information.
     */
    private function formatVisitRequest(?array $visitRequest): ?array
    {
        if (!$visitRequest) {
            return null;
        }

        return [
            'id' => $visitRequest['id'],
            'request_id' => $visitRequest['request_id'],
            'visitor_name' => $visitRequest['visitor_name'],
            'status' => $visitRequest['status'],
            'status_label' => ucfirst($visitRequest['status']),
            'status_color' => match($visitRequest['status']) {
                'pending' => 'warning',
                'approved', 'ready', 'active' => 'success',
                'completed' => 'primary',
                'rejected', 'cancelled' => 'danger',
                default => 'secondary'
            }
        ];
    }

    /**
     * Format blocked slot information.
     */
    private function formatBlockedInfo(?array $blockedInfo): ?array
    {
        if (!$blockedInfo) {
            return null;
        }

        return [
            'id' => $blockedInfo['id'],
            'reason' => $blockedInfo['reason'],
            'reason_display' => $blockedInfo['reason'] ?? 'No reason provided',
            'created_by' => $blockedInfo['created_by'],
            'created_at' => $blockedInfo['created_at'],
            'created_at_human' => $blockedInfo['created_at'] 
                ? \Carbon\Carbon::parse($blockedInfo['created_at'])->diffForHumans()
                : null,
        ];
    }
}