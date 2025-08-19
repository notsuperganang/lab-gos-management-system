<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BlockedTimeSlotResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'date' => $this->date->format('Y-m-d'),
            'formatted_date' => $this->formatted_date,
            'start_time' => $this->start_time,
            'end_time' => $this->end_time,
            'time_range' => $this->time_range,
            'duration_hours' => $this->duration,
            'reason' => $this->reason,
            'created_by' => [
                'id' => $this->creator->id ?? null,
                'name' => $this->creator->name ?? 'Admin',
                'email' => $this->creator->email ?? null,
            ],
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at->format('Y-m-d H:i:s'),
            'created_at_human' => $this->created_at->diffForHumans(),
            'is_past' => $this->isPast(),
            'can_be_removed' => $this->canBeRemoved(),
            'is_within_operating_hours' => $this->isWithinOperatingHours(),
            'status_indicator' => [
                'color' => $this->isPast() ? 'secondary' : 'warning',
                'label' => $this->isPast() ? 'Past' : 'Active',
                'icon' => $this->isPast() ? 'clock' : 'block'
            ]
        ];
    }
}