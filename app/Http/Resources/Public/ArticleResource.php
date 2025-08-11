<?php

namespace App\Http\Resources\Public;

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
            'content' => $this->when($request->routeIs('api.articles.show'), $this->content),
            'featured_image_url' => $this->featured_image_url,
            'author_name' => $this->author_name,
            'category' => $this->category,
            'category_label' => $this->category_label,
            'tags' => $this->tags ?? [],
            'published_at' => $this->published_at?->format('Y-m-d H:i:s'),
            'views_count' => $this->views_count,
            'reading_time' => $this->reading_time,
            'publisher' => $this->when(
                $request->routeIs('api.articles.show') && $this->relationLoaded('publisher') && $this->publisher,
                function () {
                    return [
                        'id' => $this->publisher->id,
                        'name' => $this->publisher->name,
                    ];
                }
            ),
        ];
    }
}