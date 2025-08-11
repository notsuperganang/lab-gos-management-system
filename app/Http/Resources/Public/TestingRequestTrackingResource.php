<?php

namespace App\Http\Resources\Public;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TestingRequestTrackingResource extends JsonResource
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
            'progress_percentage' => $this->progress_percentage,
            'client' => [
                'name' => $this->client_name,
                'organization' => $this->client_organization,
                'email' => $this->client_email,
                'phone' => $this->client_phone,
                'address' => $this->client_address,
            ],
            'sample' => [
                'name' => $this->sample_name,
                'description' => $this->sample_description,
                'quantity' => $this->sample_quantity,
            ],
            'testing' => [
                'type' => $this->testing_type,
                'type_label' => $this->testing_type_label,
                'parameters' => $this->testing_parameters,
                'urgent_request' => $this->urgent_request,
            ],
            'schedule' => [
                'preferred_date' => $this->preferred_date?->format('Y-m-d'),
                'estimated_duration_hours' => $this->estimated_duration_hours,
                'actual_start_date' => $this->actual_start_date?->format('Y-m-d'),
                'actual_completion_date' => $this->actual_completion_date?->format('Y-m-d'),
                'actual_duration_hours' => $this->actual_duration_hours,
            ],
            'cost' => [
                'estimate' => $this->cost_estimate,
                'final' => $this->final_cost,
            ],
            'results' => [
                'summary' => $this->result_summary,
                'files' => $this->result_files_path,
            ],
            'submitted_at' => $this->submitted_at->format('Y-m-d H:i:s'),
            'reviewed_at' => $this->reviewed_at?->format('Y-m-d H:i:s'),
            'reviewer' => $this->when($this->relationLoaded('reviewer') && $this->reviewer, [
                'name' => $this->reviewer->name,
            ]),
            'assigned_to' => $this->when($this->relationLoaded('assignedUser') && $this->assignedUser, [
                'name' => $this->assignedUser->name,
            ]),
            'approval_notes' => $this->approval_notes,
            'is_overdue' => $this->isOverdue(),
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
        
        if ($this->actual_start_date) {
            $timeline[] = [
                'status' => 'started',
                'label' => 'Testing Started',
                'date' => $this->actual_start_date->format('Y-m-d'),
                'active' => true,
            ];
        }
        
        if ($this->actual_completion_date) {
            $timeline[] = [
                'status' => 'completed',
                'label' => 'Testing Completed',
                'date' => $this->actual_completion_date->format('Y-m-d'),
                'active' => true,
            ];
        }
        
        return $timeline;
    }
}