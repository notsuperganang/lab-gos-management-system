<?php

namespace App\Http\Resources\Public;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BorrowRequestTrackingResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'request_id' => $this->request_id,
            'status' => $this->status,
            'status_label' => $this->status_label,
            'status_color' => $this->status_color,
            'members' => $this->members,
            'supervisor' => [
                'name' => $this->supervisor_name,
                'nip' => $this->supervisor_nip,
                'email' => $this->supervisor_email,
                'phone' => $this->supervisor_phone,
            ],
            'purpose' => $this->purpose,
            'borrow_date' => $this->borrow_date->format('Y-m-d'),
            'return_date' => $this->return_date->format('Y-m-d'),
            'start_time' => $this->start_time,
            'end_time' => $this->end_time,
            'duration_days' => $this->duration,
            'equipment_items' => $this->when($this->relationLoaded('borrowRequestItems'), function () {
                return $this->borrowRequestItems->map(function ($item) {
                    return [
                        'equipment' => [
                            'id' => $item->equipment->id,
                            'name' => $item->equipment->name,
                            'model' => $item->equipment->model,
                            'category' => $item->equipment->category?->name,
                        ],
                        'quantity_requested' => $item->quantity_requested,
                        'quantity_approved' => $item->quantity_approved,
                        'condition_before' => $item->condition_before,
                        'condition_after' => $item->condition_after,
                        'notes' => $item->notes,
                    ];
                });
            }),
            'total_requested_quantity' => $this->total_requested_quantity,
            'total_approved_quantity' => $this->total_approved_quantity,
            'submitted_at' => $this->submitted_at->format('Y-m-d H:i:s'),
            'reviewed_at' => $this->reviewed_at?->format('Y-m-d H:i:s'),
            'reviewer' => $this->when($this->relationLoaded('reviewer') && $this->reviewer, [
                'name' => $this->reviewer->name,
            ]),
            'approval_notes' => $this->approval_notes,
            'timeline' => $this->getTimeline(),
        ];
    }

    /**
     * Get request timeline
     */
    private function getTimeline(): array
    {
        $timeline = [
            [
                'status' => 'submitted',
                'label' => 'Request Submitted',
                'date' => $this->submitted_at->format('Y-m-d H:i:s'),
                'active' => true,
            ]
        ];
        
        if ($this->reviewed_at) {
            $timeline[] = [
                'status' => $this->status,
                'label' => ucfirst($this->status),
                'date' => $this->reviewed_at->format('Y-m-d H:i:s'),
                'active' => true,
            ];
        }
        
        return $timeline;
    }
}