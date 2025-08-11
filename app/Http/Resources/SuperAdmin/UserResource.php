<?php

namespace App\Http\Resources\SuperAdmin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
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
            'email' => $this->email,
            'phone' => $this->phone,
            'position' => $this->position,
            'role' => $this->role,
            'is_active' => $this->is_active,
            'avatar_url' => $this->avatar_path ? asset('storage/' . $this->avatar_path) : null,
            'last_login_at' => $this->last_login_at?->format('Y-m-d H:i:s'),
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at->format('Y-m-d H:i:s'),
            
            // Spatie Permission integration
            'roles' => $this->roles->pluck('name'),
            'permissions' => $this->getAllPermissions()->pluck('name'),
            
            // Computed attributes
            'status_label' => $this->is_active ? 'Active' : 'Inactive',
            'role_label' => match($this->role) {
                'superadmin' => 'Super Administrator',
                'admin' => 'Administrator', 
                'staff' => 'Staff Member',
                default => $this->role
            },
        ];
    }
}