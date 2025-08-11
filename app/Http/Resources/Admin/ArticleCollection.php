<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class ArticleCollection extends ResourceCollection
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
                'total_articles' => $this->collection->count(),
                'published_articles' => $this->collection->where('is_published', true)->count(),
                'draft_articles' => $this->collection->where('is_published', false)->count(),
                'recent_articles' => $this->collection->filter(function ($article) {
                    return $article->created_at->gt(now()->subDays(7));
                })->count(),
                'category_breakdown' => $this->collection->groupBy('category')->map(function ($articles) {
                    return $articles->count();
                }),
                'total_views' => $this->collection->sum('views_count'),
                'average_reading_time' => round($this->collection->avg('reading_time'), 1),
                'tagged_articles' => $this->collection->filter(function ($article) {
                    return !empty($article->tags);
                })->count(),
                'featured_articles' => $this->collection->filter(function ($article) {
                    return !empty($article->featured_image_path);
                })->count(),
            ],
        ];
    }
}