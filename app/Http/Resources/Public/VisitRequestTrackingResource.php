<?php

namespace App\Http\Resources\Public;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VisitRequestTrackingResource extends JsonResource
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
            'applicant' => [
                'full_name' => $this->full_name,
                'email' => $this->email,
                'phone' => $this->phone,
                'institution' => $this->institution,
            ],
            'purpose' => $this->purpose,
            'purpose_label' => $this->purpose_label,
            'visit_date' => $this->visit_date->format('Y-m-d'),
            'visit_time' => $this->visit_time,
            'visit_time_label' => $this->visit_time_label,
            'participants' => $this->participants,
            'additional_notes' => $this->additional_notes,
            'request_letter_url' => $this->request_letter_url,
            'approval_letter_url' => $this->approval_letter_url,
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