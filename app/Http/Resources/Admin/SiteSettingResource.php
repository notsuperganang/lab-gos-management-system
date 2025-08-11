<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SiteSettingResource extends JsonResource
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
            'key' => $this->key,
            'title' => $this->title,
            'content' => $this->content,
            'parsed_content' => $this->parsed_content,
            'type' => $this->type,
            'type_label' => $this->getTypeLabel(),
            'is_active' => $this->is_active,
            'status' => $this->is_active ? 'Active' : 'Inactive',
            'status_badge' => $this->is_active 
                ? ['text' => 'Active', 'color' => 'success'] 
                : ['text' => 'Inactive', 'color' => 'secondary'],
            'is_json' => $this->type === 'json',
            'is_boolean' => $this->type === 'boolean',
            'is_number' => $this->type === 'number',
            'is_text' => in_array($this->type, ['text', 'textarea']),
            'formatted_content' => $this->getFormattedContent(),
            'content_preview' => $this->getContentPreview(),
            'validation_errors' => $this->getValidationErrors(),
            'created_at' => $this->created_at->toISOString(),
            'updated_at' => $this->updated_at->toISOString(),
            'created_at_formatted' => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at_formatted' => $this->updated_at->format('Y-m-d H:i:s'),
            'created_at_human' => $this->created_at->diffForHumans(),
            'updated_at_human' => $this->updated_at->diffForHumans(),
            'is_recent' => $this->updated_at->gt(now()->subDays(7)),
            'can_edit' => $request->user()->hasRole(['admin', 'superadmin']) && !$this->isRestricted(),
            'can_delete' => $request->user()->hasRole(['admin', 'superadmin']) && !$this->isRestricted(),
            'is_restricted' => $this->isRestricted(),
        ];
    }

    /**
     * Get the type label for display.
     *
     * @return string
     */
    private function getTypeLabel(): string
    {
        return match($this->type) {
            'text' => 'Text',
            'textarea' => 'Long Text',
            'json' => 'JSON',
            'boolean' => 'True/False',
            'number' => 'Number',
            default => ucfirst($this->type),
        };
    }

    /**
     * Get formatted content for display.
     *
     * @return mixed
     */
    private function getFormattedContent()
    {
        return match($this->type) {
            'json' => $this->parsed_content,
            'boolean' => $this->content ? 'Yes' : 'No',
            'number' => is_numeric($this->content) ? (float) $this->content : $this->content,
            default => $this->content,
        };
    }

    /**
     * Get content preview for tables/lists.
     *
     * @return string
     */
    private function getContentPreview(): string
    {
        $content = $this->content ?? '';
        
        if ($this->type === 'json') {
            return json_encode($this->parsed_content, JSON_PRETTY_PRINT);
        }
        
        if ($this->type === 'boolean') {
            return $content ? 'Yes' : 'No';
        }
        
        return strlen($content) > 100 
            ? substr($content, 0, 100) . '...' 
            : $content;
    }

    /**
     * Get validation errors for the current content.
     *
     * @return array
     */
    private function getValidationErrors(): array
    {
        $errors = [];
        
        if ($this->type === 'json' && !empty($this->content)) {
            json_decode($this->content);
            if (json_last_error() !== JSON_ERROR_NONE) {
                $errors[] = 'Invalid JSON format: ' . json_last_error_msg();
            }
        }
        
        if ($this->type === 'number' && !empty($this->content) && !is_numeric($this->content)) {
            $errors[] = 'Content is not a valid number';
        }
        
        return $errors;
    }

    /**
     * Check if this setting is restricted from modification.
     *
     * @return bool
     */
    private function isRestricted(): bool
    {
        $restrictedKeys = [
            'app.key',
            'app.env',
            'database.default',
            'database.connections',
            'cache.default',
            'session.driver',
            'queue.default',
        ];
        
        return in_array($this->key, $restrictedKeys);
    }
}