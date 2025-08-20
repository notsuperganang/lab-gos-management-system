<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class ActivityLogCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'data' => ActivityLogResource::collection($this->collection),
        ];
    }

    /**
     * Get additional data that should be returned with the resource array.
     */
    public function with(Request $request): array
    {
        return [
            'meta' => [
                'total' => $this->collection->count(),
                'timestamp' => now()->format('Y-m-d H:i:s'),
                'filters_applied' => $request->only([
                    'type', 'user_id', 'subject_type', 'subject_id', 
                    'search', 'date_from', 'date_to'
                ]),
            ],
        ];
    }
}