<?php

namespace App\Http\Controllers\Api\Public;

use App\Http\Controllers\Controller;
use App\Models\Gallery;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GalleryController extends Controller
{
    /**
     * Get paginated list of active gallery items
     * 
     * Supports filtering by category and provides category metadata.
     * Returns gallery items with proper image URLs for frontend consumption.
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = Gallery::active()->ordered();
            
            // Apply category filter if provided
            if ($request->filled('category')) {
                $query->byCategory($request->get('category'));
            }
            
            // Pagination
            $perPage = min($request->get('per_page', 12), 50); // Max 50 items per page
            $gallery = $query->paginate($perPage);
            
            // Transform data
            $galleryData = $gallery->getCollection()->map(function ($item) {
                return [
                    'id' => $item->id,
                    'title' => $item->title,
                    'description' => $item->description,
                    'image_url' => $item->image_url,
                    'alt_text' => $item->image_alt,
                    'category' => $item->category,
                    'category_label' => $item->category_label,
                    'sort_order' => $item->sort_order,
                ];
            });
            
            return response()->json([
                'success' => true,
                'message' => 'Gallery items retrieved successfully',
                'data' => $galleryData,
                'meta' => [
                    'pagination' => [
                        'current_page' => $gallery->currentPage(),
                        'last_page' => $gallery->lastPage(),
                        'per_page' => $gallery->perPage(),
                        'total' => $gallery->total(),
                        'from' => $gallery->firstItem(),
                        'to' => $gallery->lastItem(),
                    ],
                    'categories' => Gallery::getCategories(),
                    'current_filter' => [
                        'category' => $request->get('category'),
                    ]
                ]
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve gallery items',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }
    
    /**
     * Get specific gallery item details
     */
    public function show(Gallery $gallery): JsonResponse
    {
        try {
            // Check if gallery item is active
            if (!$gallery->is_active) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gallery item not available'
                ], 404);
            }
            
            $galleryData = [
                'id' => $gallery->id,
                'title' => $gallery->title,
                'description' => $gallery->description,
                'image_url' => $gallery->image_url,
                'alt_text' => $gallery->image_alt,
                'category' => $gallery->category,
                'category_label' => $gallery->category_label,
                'sort_order' => $gallery->sort_order,
                'created_at' => $gallery->created_at->format('Y-m-d H:i:s'),
            ];
            
            return response()->json([
                'success' => true,
                'message' => 'Gallery item retrieved successfully',
                'data' => $galleryData
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve gallery item',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }
}