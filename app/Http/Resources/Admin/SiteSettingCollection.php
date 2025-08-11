<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class SiteSettingCollection extends ResourceCollection
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
                'total_settings' => $this->collection->count(),
                'active_settings' => $this->collection->where('is_active', true)->count(),
                'inactive_settings' => $this->collection->where('is_active', false)->count(),
                'recently_updated' => $this->collection->filter(function ($setting) {
                    return $setting->updated_at->gt(now()->subDays(7));
                })->count(),
                'type_breakdown' => $this->collection->groupBy('type')->map(function ($settings) {
                    return $settings->count();
                }),
                'restricted_settings' => $this->collection->filter(function ($setting) {
                    $restrictedKeys = [
                        'app.key', 'app.env', 'database.default',
                        'database.connections', 'cache.default',
                        'session.driver', 'queue.default',
                    ];
                    return in_array($setting->key, $restrictedKeys);
                })->count(),
                'json_settings' => $this->collection->where('type', 'json')->count(),
                'boolean_settings' => $this->collection->where('type', 'boolean')->count(),
                'text_settings' => $this->collection->whereIn('type', ['text', 'textarea'])->count(),
                'number_settings' => $this->collection->where('type', 'number')->count(),
                'settings_with_errors' => $this->collection->filter(function ($setting) {
                    if ($setting->type === 'json' && !empty($setting->content)) {
                        json_decode($setting->content);
                        return json_last_error() !== JSON_ERROR_NONE;
                    }
                    return false;
                })->count(),
            ],
        ];
    }
}