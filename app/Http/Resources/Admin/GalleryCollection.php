<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class GalleryCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'data' => $this->collection,
            'meta' => [
                'total_items' => $this->collection->count(),
                'active_items' => $this->collection->where('is_active', true)->count(),
                'inactive_items' => $this->collection->where('is_active', false)->count(),
                'recent_additions' => $this->collection->filter(function ($item) {
                    return $item->created_at->gt(now()->subDays(30));
                })->count(),
                'category_breakdown' => $this->collection->groupBy('category')->map(function ($items) {
                    return $items->count();
                }),
                'items_with_descriptions' => $this->collection->filter(function ($item) {
                    return !empty($item->description);
                })->count(),
                'items_with_custom_alt_text' => $this->collection->filter(function ($item) {
                    return !empty($item->alt_text) && $item->alt_text !== $item->title;
                })->count(),
                'total_file_size' => $this->collection->sum(function ($item) {
                    if (!$item->image_path) return 0;
                    $fullPath = storage_path('app/public/' . $item->image_path);
                    return file_exists($fullPath) ? filesize($fullPath) : 0;
                }),
                'total_file_size_human' => $this->formatFileSize($this->collection->sum(function ($item) {
                    if (!$item->image_path) return 0;
                    $fullPath = storage_path('app/public/' . $item->image_path);
                    return file_exists($fullPath) ? filesize($fullPath) : 0;
                })),
                'image_types' => $this->collection->map(function ($item) {
                    if (!$item->image_path) return null;
                    $fullPath = storage_path('app/public/' . $item->image_path);
                    if (!file_exists($fullPath)) return null;
                    $imageInfo = getimagesize($fullPath);
                    return $imageInfo ? image_type_to_mime_type($imageInfo[2]) : null;
                })->filter()->countBy()->toArray(),
                'missing_files' => $this->collection->filter(function ($item) {
                    if (!$item->image_path) return false;
                    $fullPath = storage_path('app/public/' . $item->image_path);
                    return !file_exists($fullPath);
                })->count(),
            ],
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