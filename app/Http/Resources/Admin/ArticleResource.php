<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ArticleResource extends JsonResource
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
            'title' => $this->title,
            'slug' => $this->slug,
            'excerpt' => $this->excerpt,
            'content' => $this->content,
            'featured_image_path' => $this->featured_image_path,
            'featured_image_url' => $this->featured_image_url,
            'author_name' => $this->author_name,
            'category' => $this->category,
            'category_label' => $this->category_label,
            'tags' => $this->tags ?? [],
            'is_published' => $this->is_published,
            'is_featured' => $this->is_featured,
            'published_at' => $this->published_at?->toISOString(),
            'published_at_formatted' => $this->published_at?->format('Y-m-d H:i:s'),
            'published_by' => $this->published_by,
            'publisher' => $this->whenLoaded('publisher', function () {
                return [
                    'id' => $this->publisher->id,
                    'name' => $this->publisher->name,
                    'email' => $this->publisher->email,
                ];
            }),
            'views_count' => $this->views_count,
            'reading_time' => $this->reading_time,
            'status' => $this->is_published ? 'Published' : 'Draft',
            'status_badge' => $this->is_published 
                ? ['text' => 'Published', 'color' => 'success'] 
                : ['text' => 'Draft', 'color' => 'warning'],
            'created_at' => $this->created_at->toISOString(),
            'updated_at' => $this->updated_at->toISOString(),
            'created_at_formatted' => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at_formatted' => $this->updated_at->format('Y-m-d H:i:s'),
            'created_at_human' => $this->created_at->diffForHumans(),
            'updated_at_human' => $this->updated_at->diffForHumans(),
            'word_count' => str_word_count(strip_tags($this->content)),
            'character_count' => strlen($this->content),
            'has_featured_image' => !empty($this->featured_image_path),
            'tag_count' => count($this->tags ?? []),
            'is_recent' => $this->created_at->gt(now()->subDays(7)),
            'can_edit' => $this->userCanManage($request),
            'can_delete' => $this->userCanManage($request),
            'can_publish' => $this->userCanManage($request),
        ];
    }

    /**
     * Determine if the request user can manage article resources.
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