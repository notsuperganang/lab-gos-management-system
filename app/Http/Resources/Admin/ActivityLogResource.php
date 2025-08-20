<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ActivityLogResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'log_name' => $this->log_name,
            'description' => $this->description,
            'event' => $this->event,
            'subject_type' => $this->subject_type,
            'subject_id' => $this->subject_id,
            'causer_type' => $this->causer_type,
            'causer_id' => $this->causer_id,
            'causer' => $this->when($this->causer, [
                'id' => $this->causer?->id,
                'name' => $this->causer?->name,
                'email' => $this->causer?->email,
                'role' => $this->causer?->role,
            ]),
            'subject' => $this->when($this->subject, function () {
                // Provide basic subject information based on type
                $subject = $this->subject;
                $baseInfo = [
                    'id' => $subject->id,
                ];

                // Add type-specific fields
                if (method_exists($subject, 'name')) {
                    $baseInfo['name'] = $subject->name;
                }
                
                if (method_exists($subject, 'title')) {
                    $baseInfo['title'] = $subject->title;
                }

                if (method_exists($subject, 'request_id')) {
                    $baseInfo['request_id'] = $subject->request_id;
                }

                return $baseInfo;
            }),
            'properties' => $this->properties,
            'batch_uuid' => $this->batch_uuid,
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'created_at_human' => $this->created_at->diffForHumans(),
            'created_at_iso' => $this->created_at->toISOString(),
            'category' => $this->getLogCategory(),
            'importance' => $this->getLogImportance(),
            'icon' => $this->getLogIcon(),
            'color' => $this->getLogColor(),
        ];
    }

    /**
     * Get the log category based on subject type and event
     */
    private function getLogCategory(): string
    {
        if (str_contains($this->subject_type, 'BorrowRequest')) {
            return 'borrow';
        }
        
        if (str_contains($this->subject_type, 'VisitRequest')) {
            return 'visit';
        }
        
        if (str_contains($this->subject_type, 'TestingRequest')) {
            return 'testing';
        }
        
        if (str_contains($this->subject_type, 'Equipment')) {
            return 'equipment';
        }
        
        if (str_contains($this->subject_type, 'User')) {
            return 'user';
        }

        return 'system';
    }

    /**
     * Get the log importance level
     */
    private function getLogImportance(): string
    {
        $criticalEvents = ['deleted', 'rejected', 'cancelled'];
        $highEvents = ['approved', 'completed'];
        $mediumEvents = ['updated', 'created'];

        if (in_array($this->event, $criticalEvents)) {
            return 'critical';
        }

        if (in_array($this->event, $highEvents)) {
            return 'high';
        }

        if (in_array($this->event, $mediumEvents)) {
            return 'medium';
        }

        return 'low';
    }

    /**
     * Get appropriate icon for the log entry
     */
    private function getLogIcon(): string
    {
        return match($this->event) {
            'created' => 'plus-circle',
            'updated' => 'pencil-alt',
            'deleted' => 'trash',
            'approved' => 'check-circle',
            'rejected' => 'x-circle',
            'cancelled' => 'ban',
            'completed' => 'badge-check',
            default => 'information-circle',
        };
    }

    /**
     * Get appropriate color class for the log entry
     */
    private function getLogColor(): string
    {
        return match($this->event) {
            'created' => 'blue',
            'updated' => 'yellow',
            'deleted' => 'red',
            'approved' => 'green',
            'rejected' => 'red',
            'cancelled' => 'gray',
            'completed' => 'purple',
            default => 'blue',
        };
    }
}