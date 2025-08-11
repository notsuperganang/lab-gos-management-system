<?php

namespace App\Http\Resources\SuperAdmin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class UserCollection extends ResourceCollection
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
                'total_users' => $this->collection->count(),
                'active_users' => $this->collection->where('is_active', true)->count(),
                'inactive_users' => $this->collection->where('is_active', false)->count(),
                'role_breakdown' => [
                    'superadmins' => $this->collection->where('role', 'superadmin')->count(),
                    'admins' => $this->collection->where('role', 'admin')->count(),
                    'staff' => $this->collection->where('role', 'staff')->count(),
                ],
            ],
        ];
    }
}