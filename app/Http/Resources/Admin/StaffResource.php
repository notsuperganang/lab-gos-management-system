<?php

namespace App\Http\Resources\Admin;

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
            'staff_type' => $this->staff_type?->value,
            'staff_type_label' => $this->staff_type?->label(),
            'specialization' => $this->specialization,
            'education' => $this->education,
            'email' => $this->email,
            'phone' => $this->phone,
            'photo_path' => $this->photo_path,
            'photo_url' => $this->photo_url,
            'bio' => $this->bio,
            'research_interests' => $this->research_interests,
            'sort_order' => $this->sort_order,
            'is_active' => $this->is_active,
            'status' => $this->is_active ? 'Active' : 'Inactive',
            'status_badge' => $this->is_active
                ? ['text' => 'Active', 'color' => 'success']
                : ['text' => 'Inactive', 'color' => 'secondary'],
            'full_contact' => $this->full_contact,
            'has_photo' => $this->resource->hasValidPhoto(),
            'has_bio' => !empty($this->bio),
            'has_research_interests' => $this->hasResearchInterests(),
            'has_contact_info' => !empty($this->email) || !empty($this->phone),
            'display_order' => $this->sort_order ?? 999,
            'created_at' => $this->created_at->toISOString(),
            'updated_at' => $this->updated_at->toISOString(),
            'created_at_formatted' => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at_formatted' => $this->updated_at->format('Y-m-d H:i:s'),
            'created_at_human' => $this->created_at->diffForHumans(),
            'updated_at_human' => $this->updated_at->diffForHumans(),
            'is_recent' => $this->created_at->gt(now()->subDays(7)),
            'profile_completeness' => $this->calculateProfileCompleteness(),
            'can_edit' => $this->userCanManage($request),
            'can_delete' => $this->userCanManage($request),
            'can_manage_status' => $this->userCanManage($request),
        ];
    }

    /**
     * Calculate profile completeness percentage.
     *
     * @return int
     */
    private function calculateProfileCompleteness(): int
    {
        $fields = [
            'name' => !empty($this->name),
            'position' => !empty($this->position),
            'specialization' => !empty($this->specialization),
            'education' => !empty($this->education),
            'email' => !empty($this->email),
            'phone' => !empty($this->phone),
            'photo_path' => !empty($this->photo_path),
            'bio' => !empty($this->bio),
            'research_interests' => !empty($this->research_interests),
        ];

        $completedFields = array_filter($fields);

        return round((count($completedFields) / count($fields)) * 100);
    }

    /**
     * Determine if the request user can manage staff resources.
     */
    private function userCanManage(Request $request): bool
    {
        $user = $request->user();
        if (!$user) {
            return false;
        }

        $role = strtolower($user->role ?? '');
        if (in_array($role, ['admin', 'super_admin'])) {
            return true;
        }

        if (method_exists($user, 'hasRole') && $user->hasRole(['admin', 'super_admin', 'superadmin'])) {
            return true;
        }

        return false;
    }
}
