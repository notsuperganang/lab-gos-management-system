<?php

namespace App\Http\Controllers\Api\Public;

use App\Http\Controllers\Controller;
use App\Models\Article;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ArticleController extends Controller
{
    /**
     * Get paginated list of published articles
     * 
     * Supports filtering by category, search queries, and tags.
     * Returns articles ordered by latest published date.
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = Article::published()->latest();
            
            // Apply filters
            if ($request->filled('category')) {
                $query->byCategory($request->get('category'));
            }
            
            if ($request->filled('tag')) {
                $query->withTag($request->get('tag'));
            }
            
            if ($request->filled('search')) {
                $query->search($request->get('search'));
            }
            
            // Pagination
            $perPage = min($request->get('per_page', 10), 50); // Max 50 items per page
            $articles = $query->paginate($perPage);
            
            // Transform data
            $articlesData = $articles->getCollection()->map(function ($article) {
                return [
                    'id' => $article->id,
                    'title' => $article->title,
                    'slug' => $article->slug,
                    'excerpt' => $article->excerpt,
                    'featured_image_url' => $article->featured_image_url,
                    'author_name' => $article->author_name,
                    'category' => $article->category,
                    'category_label' => $article->category_label,
                    'tags' => $article->tags ?? [],
                    'published_at' => $article->published_at?->format('Y-m-d H:i:s'),
                    'views_count' => $article->views_count,
                    'reading_time' => $article->reading_time,
                ];
            });
            
            return response()->json([
                'success' => true,
                'message' => 'Articles retrieved successfully',
                'data' => $articlesData,
                'meta' => [
                    'pagination' => [
                        'current_page' => $articles->currentPage(),
                        'last_page' => $articles->lastPage(),
                        'per_page' => $articles->perPage(),
                        'total' => $articles->total(),
                        'from' => $articles->firstItem(),
                        'to' => $articles->lastItem(),
                    ],
                    'filters' => [
                        'category' => $request->get('category'),
                        'tag' => $request->get('tag'),
                        'search' => $request->get('search'),
                    ],
                    'available_categories' => Article::getCategories(),
                ]
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve articles',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }
    
    /**
     * Get specific article by slug
     * 
     * Increments view count when article is accessed.
     */
    public function show(Article $article): JsonResponse
    {
        try {
            // Check if article is published
            if (!$article->is_published || !$article->published_at || $article->published_at > now()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Article not found'
                ], 404);
            }
            
            // Increment view count
            $article->incrementViews();
            
            $articleData = [
                'id' => $article->id,
                'title' => $article->title,
                'slug' => $article->slug,
                'excerpt' => $article->excerpt,
                'content' => $article->content,
                'featured_image_url' => $article->featured_image_url,
                'author_name' => $article->author_name,
                'category' => $article->category,
                'category_label' => $article->category_label,
                'tags' => $article->tags ?? [],
                'published_at' => $article->published_at?->format('Y-m-d H:i:s'),
                'views_count' => $article->views_count,
                'reading_time' => $article->reading_time,
                'publisher' => $article->publisher ? [
                    'id' => $article->publisher->id,
                    'name' => $article->publisher->name,
                ] : null,
            ];
            
            return response()->json([
                'success' => true,
                'message' => 'Article retrieved successfully',
                'data' => $articleData
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve article',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }
}