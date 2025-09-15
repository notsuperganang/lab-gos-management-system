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
            'id' => $this->id,
            'request_id' => $this->request_id,
            'status' => $this->status,
            'status_label' => $this->status_label,
            'status_color' => $this->status_color,
            'visitor_name' => $this->visitor_name,
            'visitor_email' => $this->visitor_email,
            'visitor_phone' => $this->visitor_phone,
            'institution' => $this->institution,
            'visit_purpose' => $this->visit_purpose,
            'purpose_label' => $this->purpose_label,
            'visit_date' => $this->visit_date?->format('Y-m-d'),
            'start_time' => $this->start_time,
            'end_time' => $this->end_time,
            'group_size' => $this->group_size,
            'purpose_description' => $this->purpose_description,
            'special_requirements' => $this->special_requirements,
            'equipment_needed' => $this->equipment_needed,
            'request_letter_url' => $this->request_letter_url,
            'approval_letter_url' => $this->approval_letter_url,
            'submitted_at' => $this->submitted_at?->format('Y-m-d H:i:s'),
            'reviewed_at' => $this->reviewed_at?->format('Y-m-d H:i:s'),
            'reviewer' => $this->when($this->relationLoaded('reviewer') && $this->reviewer, function () {
                return [
                    'id' => $this->reviewer?->id,
                    'name' => $this->reviewer?->name,
                    'email' => $this->reviewer?->email,
                ];
            }),
            'approval_notes' => $this->approval_notes,
            'timeline' => $this->getTimeline(),

            // Backward compatibility with old field names
            'applicant' => [
                'full_name' => $this->visitor_name,
                'email' => $this->visitor_email,
                'phone' => $this->visitor_phone,
                'institution' => $this->institution,
            ],
            'purpose' => $this->visit_purpose,
            'visit_time' => $this->start_time && $this->end_time ? $this->start_time . ' - ' . $this->end_time . ' WIB' : null,
            'visit_time_label' => $this->start_time && $this->end_time ? $this->start_time . ' - ' . $this->end_time . ' WIB' : null,
            'participants' => $this->group_size,
            'additional_notes' => $this->purpose_description,
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