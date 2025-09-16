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
            'id' => $this->id, // Add missing ID for admin operations
            'request_id' => $this->request_id,
            'status' => $this->status,
            'status_label' => $this->status_label,
            'status_color' => $this->status_color,
            'progress_percentage' => $this->progress_percentage,

            // Nested format for public use
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
                'sample_delivery_schedule' => $this->sample_delivery_schedule?->format('Y-m-d'),
                'estimated_duration' => $this->estimated_duration,
                'estimated_completion_date' => $this->estimated_completion_date?->format('Y-m-d'),
                'completion_date' => $this->completion_date?->format('Y-m-d'),
            ],
            'cost' => [
                'cost' => $this->cost,
            ],
            'results' => [
                'summary' => $this->result_summary,
                'files' => $this->getResultFilesWithUrls(),
            ],

            // Flat format for admin interface compatibility
            'client_name' => $this->client_name,
            'client_organization' => $this->client_organization,
            'client_email' => $this->client_email,
            'client_phone' => $this->client_phone,
            'client_address' => $this->client_address,
            'sample_name' => $this->sample_name,
            'sample_description' => $this->sample_description,
            'sample_quantity' => $this->sample_quantity,
            'testing_type' => $this->testing_type,
            'testing_type_label' => $this->testing_type_label,
            'testing_parameters' => $this->testing_parameters,
            'urgent_request' => $this->urgent_request,
            'sample_delivery_schedule' => $this->sample_delivery_schedule?->format('Y-m-d'),
            'estimated_duration' => $this->estimated_duration,
            'completion_date' => $this->completion_date?->format('Y-m-d'),
            'result_summary' => $this->result_summary,
            'result_files_path' => $this->result_files_path,
            'result_files' => $this->getResultFilesWithUrls(),
            'cost' => $this->cost,

            'submitted_at' => $this->submitted_at->format('Y-m-d H:i:s'),
            'reviewed_at' => $this->reviewed_at?->format('Y-m-d H:i:s'),
            'reviewer' => $this->when($this->relationLoaded('reviewer') && $this->reviewer, [
                'name' => $this->reviewer?->name,
            ]),
            'assigned_to' => $this->when($this->relationLoaded('assignedUser') && $this->assignedUser, [
                'name' => $this->assignedUser?->name,
            ]),
            'approval_notes' => $this->approval_notes,
            'is_overdue' => $this->isOverdue(),
            'timeline' => $this->getTimeline(),
        ];
    }

    /**
     * Get result files with downloadable URLs
     */
    private function getResultFilesWithUrls(): ?array
    {
        if (!$this->result_files_path) {
            return null;
        }

        $files = json_decode($this->result_files_path, true);
        if (!is_array($files)) {
            return null;
        }

        return array_map(function ($file) {
            return [
                'original_name' => $file['original_name'] ?? 'Unknown',
                'size' => $file['size'] ?? 0,
                'mime_type' => $file['mime_type'] ?? 'application/octet-stream',
                'uploaded_at' => $file['uploaded_at'] ?? null,
                'download_url' => asset('storage/' . $file['path']),
            ];
        }, $files);
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
        
        if ($this->completion_date) {
            $timeline[] = [
                'status' => 'completed',
                'label' => 'Testing Completed',
                'date' => $this->completion_date->format('Y-m-d'),
                'active' => true,
            ];
        }
        
        return $timeline;
    }
}