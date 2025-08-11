<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GalleryResource extends JsonResource
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
            'title' => $this->title,
            'description' => $this->description,
            'image_path' => $this->image_path,
            'image_url' => $this->image_url,
            'alt_text' => $this->alt_text,
            'category' => $this->category,
            'category_label' => $this->category_label,
            'sort_order' => $this->sort_order,
            'is_active' => $this->is_active,
            'status' => $this->is_active ? 'Active' : 'Inactive',
            'status_badge' => $this->is_active 
                ? ['text' => 'Active', 'color' => 'success'] 
                : ['text' => 'Inactive', 'color' => 'secondary'],
            'display_order' => $this->sort_order ?? 999,
            'has_description' => !empty($this->description),
            'has_custom_alt_text' => !empty($this->alt_text) && $this->alt_text !== $this->title,
            'image_info' => $this->getImageInfo(),
            'created_at' => $this->created_at->toISOString(),
            'updated_at' => $this->updated_at->toISOString(),
            'created_at_formatted' => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at_formatted' => $this->updated_at->format('Y-m-d H:i:s'),
            'created_at_human' => $this->created_at->diffForHumans(),
            'updated_at_human' => $this->updated_at->diffForHumans(),
            'is_recent' => $this->created_at->gt(now()->subDays(7)),
            'can_edit' => $request->user()->hasRole(['admin', 'superadmin']),
            'can_delete' => $request->user()->hasRole(['admin', 'superadmin']),
            'can_manage_status' => $request->user()->hasRole(['admin', 'superadmin']),
        ];
    }

    /**
     * Get image information if file exists.
     *
     * @return array|null
     */
    private function getImageInfo(): ?array
    {
        if (!$this->image_path) {
            return null;
        }

        $fullPath = storage_path('app/public/' . $this->image_path);
        
        if (!file_exists($fullPath)) {
            return ['error' => 'Image file not found'];
        }

        $filesize = filesize($fullPath);
        $imageInfo = getimagesize($fullPath);
        
        if (!$imageInfo) {
            return ['error' => 'Invalid image file'];
        }

        [$width, $height, $type] = $imageInfo;
        
        return [
            'width' => $width,
            'height' => $height,
            'type' => image_type_to_mime_type($type),
            'size' => $filesize,
            'size_human' => $this->formatFileSize($filesize),
            'aspect_ratio' => round($width / $height, 2),
            'is_landscape' => $width > $height,
            'is_portrait' => $height > $width,
            'is_square' => $width === $height,
        ];
    }

    /**
     * Format file size in human-readable format.
     *
     * @param int $bytes
     * @return string
     */
    private function formatFileSize(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        
        $bytes /= (1 << (10 * $pow));
        
        return round($bytes, 2) . ' ' . $units[$pow];
    }
}