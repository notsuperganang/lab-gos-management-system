<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class StaffCollection extends ResourceCollection
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
                'total_staff' => $this->collection->count(),
                'active_staff' => $this->collection->where('is_active', true)->count(),
                'inactive_staff' => $this->collection->where('is_active', false)->count(),
                'recent_additions' => $this->collection->filter(function ($staff) {
                    return $staff->created_at->gt(now()->subDays(30));
                })->count(),
                'staff_with_photos' => $this->collection->filter(function ($staff) {
                    return !empty($staff->photo_path);
                })->count(),
                'staff_with_bios' => $this->collection->filter(function ($staff) {
                    return !empty($staff->bio);
                })->count(),
                'staff_with_research_interests' => $this->collection->filter(function ($staff) {
                    return !empty($staff->research_interests);
                })->count(),
                'staff_with_contact' => $this->collection->filter(function ($staff) {
                    return !empty($staff->email) || !empty($staff->phone);
                })->count(),
                'position_breakdown' => $this->collection->groupBy('position')->map(function ($staff) {
                    return $staff->count();
                }),
                'average_profile_completeness' => round($this->collection->avg(function ($staff) {
                    $fields = [
                        !empty($staff->name),
                        !empty($staff->position),
                        !empty($staff->specialization),
                        !empty($staff->education),
                        !empty($staff->email),
                        !empty($staff->phone),
                        !empty($staff->photo_path),
                        !empty($staff->bio),
                        !empty($staff->research_interests),
                    ];
                    return (array_filter($fields) / count($fields)) * 100;
                }), 1),
            ],
        ];
    }
}