<?php

namespace App\Http\Resources\Public;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EquipmentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $isDetailView = $request->routeIs('api.equipment.show');
        
        return [
            'id' => $this->id,
            'name' => $this->name,
            'model' => $this->model,
            'manufacturer' => $this->manufacturer,
            'specifications' => $this->when($isDetailView, $this->specifications),
            'total_quantity' => $this->total_quantity,
            'available_quantity' => $this->available_quantity,
            'status' => $this->status,
            'condition_status' => $this->condition_status,
            'purchase_date' => $this->when($isDetailView, $this->purchase_date?->format('Y-m-d')),
            'location' => $this->location,
            'image_url' => $this->image_url,
            'manual_url' => $this->when($isDetailView, $this->manual_url),
            'notes' => $this->when($isDetailView, $this->notes),
            'last_maintenance_date' => $this->when($isDetailView, $this->last_maintenance_date?->format('Y-m-d')),
            'next_maintenance_date' => $this->when($isDetailView, $this->next_maintenance_date?->format('Y-m-d')),
            'category' => $this->when($this->relationLoaded('category') && $this->category, [
                'id' => $this->category->id,
                'name' => $this->category->name,
            ]),
            'is_available' => $this->isAvailable(),
            'needs_maintenance' => $this->when($isDetailView, $this->needsMaintenance()),
            'status_color' => $this->status_color,
            'condition_color' => $this->condition_color,
        ];
    }
}