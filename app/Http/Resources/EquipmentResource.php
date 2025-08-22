<?php

namespace App\Http\Resources;

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
        return [
            'id' => $this->id,
            'name' => $this->name,
            'category_id' => $this->category_id,
            'category' => $this->whenLoaded('category', fn () => [
                'id' => $this->category->id,
                'name' => $this->category->name,
            ]),
            'model' => $this->model,
            'manufacturer' => $this->manufacturer,
            'specifications' => $this->specifications,
            'total_quantity' => $this->total_quantity,
            'available_quantity' => $this->available_quantity,
            'borrowed_quantity' => $this->total_quantity - $this->available_quantity,
            'status' => $this->status,
            'status_label' => match($this->status) {
                'active' => 'Aktif',
                'maintenance' => 'Maintenance',
                'retired' => 'Tidak Aktif',
                default => $this->status,
            },
            'status_color' => $this->status_color,
            'condition_status' => $this->condition_status,
            'condition_label' => match($this->condition_status) {
                'excellent' => 'Sangat Baik',
                'good' => 'Baik',
                'fair' => 'Cukup',
                'poor' => 'Buruk',
                default => $this->condition_status,
            },
            'condition_color' => $this->condition_color,
            'purchase_date' => $this->purchase_date?->format('Y-m-d'),
            'purchase_date_formatted' => $this->purchase_date?->format('d/m/Y'),
            'purchase_price' => $this->purchase_price,
            'purchase_price_formatted' => $this->purchase_price ? 'Rp ' . number_format($this->purchase_price, 0, ',', '.') : null,
            'location' => $this->location,
            'image_path' => $this->image_path,
            'image_url' => $this->getImageUrl(),
            'manual_file_path' => $this->manual_file_path,
            'manual_url' => $this->manual_url,
            'notes' => $this->notes,
            'last_maintenance_date' => $this->last_maintenance_date?->format('Y-m-d'),
            'last_maintenance_date_formatted' => $this->last_maintenance_date?->format('d/m/Y'),
            'next_maintenance_date' => $this->next_maintenance_date?->format('Y-m-d'),
            'next_maintenance_date_formatted' => $this->next_maintenance_date?->format('d/m/Y'),
            'needs_maintenance' => $this->needsMaintenance(),
            'availability_percentage' => $this->getAvailabilityPercentage(),
            'has_low_availability' => $this->hasLowAvailability(),
            'is_available' => $this->isAvailable(),
            'created_at' => $this->created_at,
            'created_at_formatted' => $this->created_at->format('d/m/Y H:i'),
            'created_at_human' => $this->created_at->diffForHumans(),
            'updated_at' => $this->updated_at,
            'updated_at_formatted' => $this->updated_at->format('d/m/Y H:i'),
            'updated_at_human' => $this->updated_at->diffForHumans(),
        ];
    }

    /**
     * Get equipment image URL with fallback.
     */
    private function getImageUrl(): string
    {
        if ($this->image_path) {
            $fullPath = storage_path('app/public/' . $this->image_path);
            if (file_exists($fullPath)) {
                return asset('storage/' . $this->image_path);
            }
        }

        return asset('assets/images/placeholder.svg');
    }
}