<?php

namespace App\Http\Resources\Public;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StaffResource extends JsonResource
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
            'position' => $this->position,
            'specialization' => $this->specialization,
            'education' => $this->education,
            'email' => $this->email,
            'phone' => $this->phone,
            'photo_url' => $this->photo_url,
            'bio' => $this->when($request->routeIs('api.staff.show'), $this->bio),
            'research_interests' => $this->when($request->routeIs('api.staff.show'), $this->research_interests),
            'full_contact' => $this->full_contact,
            'sort_order' => $this->when($request->routeIs('api.staff.show'), $this->sort_order),
        ];
    }
}