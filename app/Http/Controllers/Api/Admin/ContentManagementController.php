<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\ApiResponse;
use App\Models\Gallery;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ContentManagementController extends Controller
{
    // Article management
    public function articles(Request $request): JsonResponse
    {
        return ApiResponse::success([], 'Articles management - placeholder');
    }
    
    public function storeArticle(Request $request): JsonResponse
    {
        return ApiResponse::success([], 'Article creation - placeholder');
    }
    
    public function showArticle($article): JsonResponse
    {
        return ApiResponse::success([], 'Article details - placeholder');
    }
    
    public function updateArticle(Request $request, $article): JsonResponse
    {
        return ApiResponse::success([], 'Article update - placeholder');
    }
    
    public function destroyArticle($article): JsonResponse
    {
        return ApiResponse::success([], 'Article deletion - placeholder');
    }
    
    // Staff management
    public function staff(Request $request): JsonResponse
    {
        return ApiResponse::success([], 'Staff management - placeholder');
    }
    
    public function storeStaff(Request $request): JsonResponse
    {
        return ApiResponse::success([], 'Staff creation - placeholder');
    }
    
    public function showStaff($staff): JsonResponse
    {
        return ApiResponse::success([], 'Staff details - placeholder');
    }
    
    public function updateStaff(Request $request, $staff): JsonResponse
    {
        return ApiResponse::success([], 'Staff update - placeholder');
    }
    
    public function destroyStaff($staff): JsonResponse
    {
        return ApiResponse::success([], 'Staff deletion - placeholder');
    }
    
    // Site settings
    public function siteSettings(Request $request): JsonResponse
    {
        return ApiResponse::success([], 'Site settings - placeholder');
    }
    
    public function updateSiteSettings(Request $request): JsonResponse
    {
        return ApiResponse::success([], 'Site settings update - placeholder');
    }
    
    // Gallery management
    public function gallery(Request $request): JsonResponse
    {
        try {
            $query = Gallery::orderBy('sort_order')->orderBy('created_at', 'desc');
            
            // Apply filters
            if ($request->filled('category')) {
                $query->byCategory($request->get('category'));
            }
            
            if ($request->filled('is_active')) {
                $query->where('is_active', $request->boolean('is_active'));
            }
            
            if ($request->filled('search')) {
                $search = $request->get('search');
                $query->where(function ($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%");
                });
            }
            
            $perPage = min($request->get('per_page', 15), 100);
            $gallery = $query->paginate($perPage);
            
            return ApiResponse::paginated(
                $gallery,
                \App\Http\Resources\Public\GalleryResource::class,
                'Gallery items retrieved successfully',
                [
                    'filters' => [
                        'category' => $request->get('category'),
                        'is_active' => $request->get('is_active'),
                        'search' => $request->get('search'),
                    ],
                    'categories' => Gallery::getCategories(),
                ]
            );
            
        } catch (\Exception $e) {
            Log::error('Failed to retrieve gallery items', [
                'error' => $e->getMessage(),
                'admin_user_id' => $request->user()->id
            ]);
            
            return ApiResponse::error(
                'Failed to retrieve gallery items',
                500,
                null,
                $e->getMessage()
            );
        }
    }
    
    public function storeGallery(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'nullable|string|max:1000',
                'category' => 'required|string|in:' . implode(',', array_keys(Gallery::getCategories())),
                'alt_text' => 'nullable|string|max:255',
                'sort_order' => 'nullable|integer|min:0',
                'is_active' => 'boolean',
                'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:5120', // 5MB max
            ]);
            
            DB::beginTransaction();
            
            // Handle image upload
            $imagePath = null;
            if ($request->hasFile('image')) {
                $imagePath = $request->file('image')->store('gallery', 'public');
            }
            
            // Create gallery item
            $gallery = Gallery::create([
                'title' => $validated['title'],
                'description' => $validated['description'] ?? null,
                'image_path' => $imagePath,
                'alt_text' => $validated['alt_text'] ?? $validated['title'],
                'category' => $validated['category'],
                'sort_order' => $validated['sort_order'] ?? 0,
                'is_active' => $validated['is_active'] ?? true,
            ]);
            
            DB::commit();
            
            Log::info('Gallery item created', [
                'gallery_id' => $gallery->id,
                'admin_user_id' => $request->user()->id
            ]);
            
            return ApiResponse::success(
                new \App\Http\Resources\Public\GalleryResource($gallery),
                'Gallery item created successfully',
                201
            );
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            return ApiResponse::validationError($e->errors());
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Failed to create gallery item', [
                'error' => $e->getMessage(),
                'admin_user_id' => $request->user()->id
            ]);
            
            return ApiResponse::error(
                'Failed to create gallery item',
                500,
                null,
                $e->getMessage()
            );
        }
    }
    
    public function showGallery(Gallery $gallery): JsonResponse
    {
        try {
            return ApiResponse::success(
                new \App\Http\Resources\Public\GalleryResource($gallery),
                'Gallery item retrieved successfully'
            );
            
        } catch (\Exception $e) {
            Log::error('Failed to retrieve gallery item', [
                'gallery_id' => $gallery->id,
                'error' => $e->getMessage()
            ]);
            
            return ApiResponse::error(
                'Failed to retrieve gallery item',
                500,
                null,
                $e->getMessage()
            );
        }
    }
    
    public function updateGallery(Request $request, Gallery $gallery): JsonResponse
    {
        try {
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'nullable|string|max:1000',
                'category' => 'required|string|in:' . implode(',', array_keys(Gallery::getCategories())),
                'alt_text' => 'nullable|string|max:255',
                'sort_order' => 'nullable|integer|min:0',
                'is_active' => 'boolean',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120', // 5MB max
                'remove_image' => 'boolean',
            ]);
            
            DB::beginTransaction();
            
            $oldImagePath = $gallery->image_path;
            $imagePath = $oldImagePath;
            
            // Handle image removal
            if ($request->boolean('remove_image')) {
                $imagePath = null;
                if ($oldImagePath && Storage::disk('public')->exists($oldImagePath)) {
                    Storage::disk('public')->delete($oldImagePath);
                }
            }
            // Handle new image upload
            elseif ($request->hasFile('image')) {
                $imagePath = $request->file('image')->store('gallery', 'public');
                
                // Delete old image if exists
                if ($oldImagePath && Storage::disk('public')->exists($oldImagePath)) {
                    Storage::disk('public')->delete($oldImagePath);
                }
            }
            
            // Update gallery item
            $gallery->update([
                'title' => $validated['title'],
                'description' => $validated['description'] ?? null,
                'image_path' => $imagePath,
                'alt_text' => $validated['alt_text'] ?? $validated['title'],
                'category' => $validated['category'],
                'sort_order' => $validated['sort_order'] ?? 0,
                'is_active' => $validated['is_active'] ?? true,
            ]);
            
            DB::commit();
            
            Log::info('Gallery item updated', [
                'gallery_id' => $gallery->id,
                'admin_user_id' => $request->user()->id
            ]);
            
            return ApiResponse::success(
                new \App\Http\Resources\Public\GalleryResource($gallery->fresh()),
                'Gallery item updated successfully'
            );
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            return ApiResponse::validationError($e->errors());
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Failed to update gallery item', [
                'gallery_id' => $gallery->id,
                'error' => $e->getMessage(),
                'admin_user_id' => $request->user()->id
            ]);
            
            return ApiResponse::error(
                'Failed to update gallery item',
                500,
                null,
                $e->getMessage()
            );
        }
    }
    
    public function destroyGallery(Gallery $gallery): JsonResponse
    {
        try {
            DB::beginTransaction();
            
            // Delete image file if exists
            if ($gallery->image_path && Storage::disk('public')->exists($gallery->image_path)) {
                Storage::disk('public')->delete($gallery->image_path);
            }
            
            $galleryId = $gallery->id;
            $gallery->delete();
            
            DB::commit();
            
            Log::info('Gallery item deleted', [
                'gallery_id' => $galleryId,
                'admin_user_id' => auth()->id()
            ]);
            
            return ApiResponse::success(
                ['id' => $galleryId],
                'Gallery item deleted successfully'
            );
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Failed to delete gallery item', [
                'gallery_id' => $gallery->id,
                'error' => $e->getMessage()
            ]);
            
            return ApiResponse::error(
                'Failed to delete gallery item',
                500,
                null,
                $e->getMessage()
            );
        }
    }
}