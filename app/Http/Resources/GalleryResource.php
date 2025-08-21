<?php

// app/Http/Resources/GalleryResource.php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class GalleryResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'image_url' => $this->image_url,
            'alt_text' => $this->alt_text,
            'category' => $this->category,
            'category_label' => $this->getCategoryLabel(),
            'sort_order' => $this->sort_order,
            'is_active' => $this->is_active,
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s')
        ];
    }

    private function getCategoryLabel(): string
    {
        return match($this->category) {
            'lab_facilities' => 'Fasilitas Lab',
            'equipment' => 'Peralatan',
            'activities' => 'Kegiatan',
            'events' => 'Acara',
            default => $this->category
        };
    }
}
