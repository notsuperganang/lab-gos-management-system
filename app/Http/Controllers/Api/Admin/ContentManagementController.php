<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ArticleRequest;
use App\Http\Requests\Admin\StaffRequest;
use App\Http\Requests\Admin\SiteSettingsRequest;
use App\Http\Requests\Admin\GalleryRequest;
use App\Http\Resources\Admin\ArticleResource;
use App\Http\Resources\Admin\StaffResource;
use App\Http\Resources\Admin\SiteSettingResource;
use App\Http\Resources\Admin\GalleryResource;
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
        try {
            $query = \App\Models\Article::with('publisher')->orderBy('created_at', 'desc');

            // Apply filters
            if ($request->filled('category')) {
                $query->byCategory($request->get('category'));
            }

            if ($request->filled('is_published')) {
                $query->where('is_published', $request->boolean('is_published'));
            }

            if ($request->filled('search')) {
                $query->search($request->get('search'));
            }

            if ($request->filled('author')) {
                $query->where('author_name', 'like', '%' . $request->get('author') . '%');
            }

            $perPage = min($request->get('per_page', 15), 100);
            $articles = $query->paginate($perPage);

            return ApiResponse::paginated(
                $articles,
                ArticleResource::class,
                'Articles retrieved successfully',
                [
                    'filters' => [
                        'category' => $request->get('category'),
                        'is_published' => $request->get('is_published'),
                        'search' => $request->get('search'),
                        'author' => $request->get('author'),
                    ],
                    'categories' => \App\Models\Article::getCategories(),
                ]
            );

        } catch (\Exception $e) {
            Log::error('Failed to retrieve articles', [
                'error' => $e->getMessage(),
                'admin_user_id' => $request->user()->id
            ]);

            return ApiResponse::error(
                'Failed to retrieve articles',
                500,
                null,
                $e->getMessage()
            );
        }
    }

    public function storeArticle(ArticleRequest $request): JsonResponse
    {
        try {
            $validated = $request->validated();

            DB::beginTransaction();

            // Handle featured image upload
            $featuredImagePath = null;
            if ($request->hasFile('featured_image')) {
                $featuredImagePath = $request->file('featured_image')->store('articles', 'public');
            }

            // Generate slug from title
            $article = new \App\Models\Article(['title' => $validated['title']]);
            $slug = $article->generateSlug();

            // Handle publication logic
            $isFeatured = $validated['is_featured'] ?? false;
            $isPublished = $validated['is_published'] ?? false;
            $publishedAt = null;
            $publishedBy = null;

            // If featured request, force publish
            if ($isFeatured && !$isPublished) {
                $isPublished = true;
            }

            // Set publication details if publishing
            if ($isPublished) {
                $publishedAt = $validated['published_at'] ? new \DateTime($validated['published_at']) : now();
                $publishedBy = $request->user()->id;
            }

            $article = \App\Models\Article::create([
                'title' => $validated['title'],
                'slug' => $slug,
                'excerpt' => $validated['excerpt'],
                'content' => $validated['content'],
                'featured_image_path' => $featuredImagePath,
                'author_name' => $request->user()->name,
                'category' => $validated['category'],
                'tags' => $validated['tags'] ?? [],
                'is_published' => $isPublished,
                'is_featured' => $isFeatured,
                'published_at' => $publishedAt,
                'published_by' => $publishedBy,
                'views_count' => 0,
            ]);

            DB::commit();

            Log::info('Article created', [
                'article_id' => $article->id,
                'admin_user_id' => $request->user()->id,
                'is_published' => $article->is_published
            ]);

            return ApiResponse::success(
                new ArticleResource($article->load('publisher')),
                'Article created successfully',
                201
            );

        } catch (\Illuminate\Validation\ValidationException $e) {
            return ApiResponse::validationError($e->errors());

        } catch (\Exception $e) {
            DB::rollBack();

            // Handle unique featured constraint violation gracefully
            if (str_contains(strtolower($e->getMessage()), 'uniq_articles_featured_lock')) {
                return ApiResponse::validationError([
                    'is_featured' => ['Hanya boleh ada 1 artikel yang difeature.']
                ]);
            }

            Log::error('Failed to create article', [
                'error' => $e->getMessage(),
                'admin_user_id' => $request->user()->id
            ]);

            return ApiResponse::error(
                'Failed to create article',
                500,
                null,
                $e->getMessage()
            );
        }
    }

    public function showArticle(\App\Models\Article $article): JsonResponse
    {
        try {
            $article->load('publisher');

            return ApiResponse::success(
                new ArticleResource($article),
                'Article retrieved successfully'
            );

        } catch (\Exception $e) {
            Log::error('Failed to retrieve article', [
                'article_id' => $article->id,
                'error' => $e->getMessage()
            ]);

            return ApiResponse::error(
                'Failed to retrieve article',
                500,
                null,
                $e->getMessage()
            );
        }
    }

    public function updateArticle(ArticleRequest $request, \App\Models\Article $article): JsonResponse
    {
        try {
            $validated = $request->validated();

            DB::beginTransaction();

            $oldImagePath = $article->featured_image_path;
            $featuredImagePath = $oldImagePath;

            // Handle featured image removal
            if ($request->boolean('remove_featured_image')) {
                $featuredImagePath = null;
                if ($oldImagePath && Storage::disk('public')->exists($oldImagePath)) {
                    Storage::disk('public')->delete($oldImagePath);
                }
            }
            // Handle new featured image upload
            elseif ($request->hasFile('featured_image')) {
                $featuredImagePath = $request->file('featured_image')->store('articles', 'public');

                // Delete old image if exists
                if ($oldImagePath && Storage::disk('public')->exists($oldImagePath)) {
                    Storage::disk('public')->delete($oldImagePath);
                }
            }

            // Update slug if title changed
            $slug = $article->slug;
            if ($article->title !== $validated['title']) {
                $article->title = $validated['title'];
                $slug = $article->generateSlug();
            }

            // Handle publication logic
            $isPublished = $validated['is_published'] ?? false;
            $isFeatured = $validated['is_featured'] ?? false;
            $publishedAt = $article->published_at;
            $publishedBy = $article->published_by;

            // If featured request, force publish
            if ($isFeatured && !$isPublished) {
                $isPublished = true;
            }

            if ($isPublished) {
                if (!$article->is_published) {
                    // Publishing for the first time - set publication date automatically
                    $publishedAt = $validated['published_at'] ? new \DateTime($validated['published_at']) : now();
                    $publishedBy = $request->user()->id;
                } elseif ($validated['published_at'] && $validated['published_at'] !== $article->published_at?->format('Y-m-d\TH:i')) {
                    // Update published date only if explicitly changed
                    $publishedAt = new \DateTime($validated['published_at']);
                }
            } else {
                // Unpublishing - clear publication details
                $publishedAt = null;
                $publishedBy = null;
            }

            $article->update([
                'title' => $validated['title'],
                'slug' => $slug,
                'excerpt' => $validated['excerpt'],
                'content' => $validated['content'],
                'featured_image_path' => $featuredImagePath,
                'category' => $validated['category'],
                'tags' => $validated['tags'] ?? [],
                'is_published' => $isPublished,
                'is_featured' => $isFeatured,
                'published_at' => $publishedAt,
                'published_by' => $publishedBy,
            ]);

            DB::commit();

            Log::info('Article updated', [
                'article_id' => $article->id,
                'admin_user_id' => $request->user()->id,
                'is_published' => $article->is_published
            ]);

            return ApiResponse::success(
                new ArticleResource($article->fresh()->load('publisher')),
                'Article updated successfully'
            );

        } catch (\Illuminate\Validation\ValidationException $e) {
            return ApiResponse::validationError($e->errors());

        } catch (\Exception $e) {
            DB::rollBack();

            if (str_contains(strtolower($e->getMessage()), 'uniq_articles_featured_lock')) {
                return ApiResponse::validationError([
                    'is_featured' => ['Hanya boleh ada 1 artikel yang difeature.']
                ]);
            }

            Log::error('Failed to update article', [
                'article_id' => $article->id,
                'error' => $e->getMessage(),
                'admin_user_id' => $request->user()->id
            ]);

            return ApiResponse::error(
                'Failed to update article',
                500,
                null,
                $e->getMessage()
            );
        }
    }

    public function destroyArticle(\App\Models\Article $article): JsonResponse
    {
        try {
            DB::beginTransaction();

            // Delete featured image if exists
            if ($article->featured_image_path && Storage::disk('public')->exists($article->featured_image_path)) {
                Storage::disk('public')->delete($article->featured_image_path);
            }

            $articleId = $article->id;
            $articleTitle = $article->title;
            $article->delete();

            DB::commit();

            Log::info('Article deleted', [
                'article_id' => $articleId,
                'article_title' => $articleTitle,
                'admin_user_id' => auth('sanctum')->id()
            ]);

            return ApiResponse::success(
                ['id' => $articleId, 'title' => $articleTitle],
                'Article deleted successfully'
            );

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Failed to delete article', [
                'article_id' => $article->id,
                'error' => $e->getMessage()
            ]);

            return ApiResponse::error(
                'Failed to delete article',
                500,
                null,
                $e->getMessage()
            );
        }
    }

    // Staff management
    public function staff(Request $request): JsonResponse
    {
        try {
            $query = \App\Models\StaffMember::orderBy('sort_order')->orderBy('name');

            // Apply filters
            if ($request->filled('position')) {
                $query->where('position', 'like', '%' . $request->get('position') . '%');
            }

            if ($request->filled('staff_type')) {
                $query->where('staff_type', $request->get('staff_type'));
            }

            if ($request->filled('is_active')) {
                $query->where('is_active', $request->boolean('is_active'));
            }

            if ($request->filled('search')) {
                $search = $request->get('search');
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('position', 'like', "%{$search}%")
                      ->orWhere('specialization', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
                });
            }

            $perPage = min($request->get('per_page', 15), 100);
            $staff = $query->paginate($perPage);

            return ApiResponse::paginated(
                $staff,
                StaffResource::class,
                'Staff members retrieved successfully',
                [
                    'filters' => [
                        'position' => $request->get('position'),
                        'staff_type' => $request->get('staff_type'),
                        'is_active' => $request->get('is_active'),
                        'search' => $request->get('search'),
                    ],
                    'staff_types' => \App\Enums\StaffType::options(),
                ]
            );

        } catch (\Exception $e) {
            Log::error('Failed to retrieve staff members', [
                'error' => $e->getMessage(),
                'admin_user_id' => $request->user()->id
            ]);

            return ApiResponse::error(
                'Failed to retrieve staff members',
                500,
                null,
                $e->getMessage()
            );
        }
    }

    public function storeStaff(StaffRequest $request): JsonResponse
    {
        try {
            $validated = $request->validated();

            DB::beginTransaction();

            // Double-check uniqueness of Kepala Laboratorium at persistence layer
            if (($validated['staff_type'] ?? null) === \App\Enums\StaffType::KEPALA_LABORATORIUM->value) {
                $exists = \App\Models\StaffMember::where('staff_type', \App\Enums\StaffType::KEPALA_LABORATORIUM)->exists();
                if ($exists) {
                    DB::rollBack();
                    return ApiResponse::validationError([
                        'staff_type' => ['Hanya boleh ada 1 Kepala Laboratorium.']
                    ]);
                }
            }

            // Handle photo upload
            $photoPath = null;
            if ($request->hasFile('photo')) {
                $photoPath = $request->file('photo')->store('staff', 'public');
            }

            // Create staff member
            $staff = \App\Models\StaffMember::create([
                'name' => $validated['name'],
                'position' => $validated['position'],
                'staff_type' => $validated['staff_type'],
                'specialization' => $validated['specialization'],
                'education' => $validated['education'],
                'email' => $validated['email'],
                'phone' => $validated['phone'],
                'photo_path' => $photoPath,
                'bio' => $validated['bio'],
                'research_interests' => $validated['research_interests'],
                'sort_order' => $validated['sort_order'] ?? 0,
                'is_active' => $validated['is_active'] ?? true,
            ]);

            DB::commit();

            Log::info('Staff member created', [
                'staff_id' => $staff->id,
                'admin_user_id' => $request->user()->id
            ]);

            return ApiResponse::success(
                new StaffResource($staff),
                'Staff member created successfully',
                201
            );

        } catch (\Illuminate\Validation\ValidationException $e) {
            return ApiResponse::validationError($e->errors());

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Failed to create staff member', [
                'error' => $e->getMessage(),
                'admin_user_id' => $request->user()->id
            ]);

            return ApiResponse::error(
                'Failed to create staff member',
                500,
                null,
                $e->getMessage()
            );
        }
    }

    public function showStaff(\App\Models\StaffMember $staff): JsonResponse
    {
        try {
            return ApiResponse::success(
                new StaffResource($staff),
                'Staff member retrieved successfully'
            );

        } catch (\Exception $e) {
            Log::error('Failed to retrieve staff member', [
                'staff_id' => $staff->id,
                'error' => $e->getMessage()
            ]);

            return ApiResponse::error(
                'Failed to retrieve staff member',
                500,
                null,
                $e->getMessage()
            );
        }
    }

    public function updateStaff(StaffRequest $request, \App\Models\StaffMember $staff): JsonResponse
    {
        try {
            $validated = $request->validated();

            DB::beginTransaction();

            if (($validated['staff_type'] ?? null) === \App\Enums\StaffType::KEPALA_LABORATORIUM->value) {
                $exists = \App\Models\StaffMember::where('staff_type', \App\Enums\StaffType::KEPALA_LABORATORIUM)
                    ->where('id', '!=', $staff->id)
                    ->exists();
                if ($exists) {
                    DB::rollBack();
                    return ApiResponse::validationError([
                        'staff_type' => ['Hanya boleh ada 1 Kepala Laboratorium.']
                    ]);
                }
            }

            $oldPhotoPath = $staff->photo_path;
            $photoPath = $oldPhotoPath;

            // Handle photo removal
            if ($request->boolean('remove_photo')) {
                $photoPath = null;
                if ($oldPhotoPath && Storage::disk('public')->exists($oldPhotoPath)) {
                    Storage::disk('public')->delete($oldPhotoPath);
                }
            }
            // Handle new photo upload
            elseif ($request->hasFile('photo')) {
                $photoPath = $request->file('photo')->store('staff', 'public');

                // Delete old photo if exists
                if ($oldPhotoPath && Storage::disk('public')->exists($oldPhotoPath)) {
                    Storage::disk('public')->delete($oldPhotoPath);
                }
            }

            // Update staff member
            $staff->update([
                'name' => $validated['name'],
                'position' => $validated['position'],
                'staff_type' => $validated['staff_type'],
                'specialization' => $validated['specialization'],
                'education' => $validated['education'],
                'email' => $validated['email'],
                'phone' => $validated['phone'],
                'photo_path' => $photoPath,
                'bio' => $validated['bio'],
                'research_interests' => $validated['research_interests'],
                'sort_order' => $validated['sort_order'] ?? $staff->sort_order,
                'is_active' => $validated['is_active'] ?? $staff->is_active,
            ]);

            DB::commit();

            Log::info('Staff member updated', [
                'staff_id' => $staff->id,
                'admin_user_id' => $request->user()->id
            ]);

            return ApiResponse::success(
                new StaffResource($staff->fresh()),
                'Staff member updated successfully'
            );

        } catch (\Illuminate\Validation\ValidationException $e) {
            return ApiResponse::validationError($e->errors());

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Failed to update staff member', [
                'staff_id' => $staff->id,
                'error' => $e->getMessage(),
                'admin_user_id' => $request->user()->id
            ]);

            return ApiResponse::error(
                'Failed to update staff member',
                500,
                null,
                $e->getMessage()
            );
        }
    }

    public function destroyStaff(\App\Models\StaffMember $staff): JsonResponse
    {
        try {
            DB::beginTransaction();

            // Delete photo if exists
            if ($staff->photo_path && Storage::disk('public')->exists($staff->photo_path)) {
                Storage::disk('public')->delete($staff->photo_path);
            }

            $staffId = $staff->id;
            $staffName = $staff->name;
            $staff->delete();

            DB::commit();

            Log::info('Staff member deleted', [
                'staff_id' => $staffId,
                'staff_name' => $staffName,
                'admin_user_id' => auth('sanctum')->id()
            ]);

            return ApiResponse::success(
                ['id' => $staffId, 'name' => $staffName],
                'Staff member deleted successfully'
            );

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Failed to delete staff member', [
                'staff_id' => $staff->id,
                'error' => $e->getMessage()
            ]);

            return ApiResponse::error(
                'Failed to delete staff member',
                500,
                null,
                $e->getMessage()
            );
        }
    }

    // Site settings
    public function siteSettings(Request $request): JsonResponse
    {
        try {
            $query = \App\Models\SiteSetting::orderBy('key');

            // Apply filters
            if ($request->filled('type')) {
                $query->ofType($request->get('type'));
            }

            if ($request->filled('is_active')) {
                $query->where('is_active', $request->boolean('is_active'));
            }

            if ($request->filled('search')) {
                $search = $request->get('search');
                $query->where(function ($q) use ($search) {
                    $q->where('key', 'like', "%{$search}%")
                      ->orWhere('title', 'like', "%{$search}%")
                      ->orWhere('content', 'like', "%{$search}%");
                });
            }

            $perPage = min($request->get('per_page', 50), 100);
            $settings = $query->paginate($perPage);

            // Transform to key-value pairs for easier frontend consumption
            $settingsData = $settings->getCollection()->mapWithKeys(function ($setting) {
                return [
                    $setting->key => [
                        'id' => $setting->id,
                        'key' => $setting->key,
                        'title' => $setting->title,
                        'content' => $setting->parsed_content,
                        'raw_content' => $setting->content,
                        'type' => $setting->type,
                        'is_active' => $setting->is_active,
                        'updated_at' => $setting->updated_at,
                    ]
                ];
            });

            return ApiResponse::success(
                [
                    'settings' => $settingsData,
                    'pagination' => [
                        'current_page' => $settings->currentPage(),
                        'last_page' => $settings->lastPage(),
                        'per_page' => $settings->perPage(),
                        'total' => $settings->total(),
                        'from' => $settings->firstItem(),
                        'to' => $settings->lastItem(),
                    ],
                    'filters' => [
                        'type' => $request->get('type'),
                        'is_active' => $request->get('is_active'),
                        'search' => $request->get('search'),
                    ],
                    'available_types' => ['text', 'textarea', 'json', 'boolean', 'number'],
                ],
                'Site settings retrieved successfully'
            );

        } catch (\Exception $e) {
            Log::error('Failed to retrieve site settings', [
                'error' => $e->getMessage(),
                'admin_user_id' => $request->user()->id
            ]);

            return ApiResponse::error(
                'Failed to retrieve site settings',
                500,
                null,
                $e->getMessage()
            );
        }
    }

    public function updateSiteSettings(SiteSettingsRequest $request): JsonResponse
    {
        try {
            $validated = $request->validated();

            DB::beginTransaction();

            $updatedSettings = [];
            $errors = [];

            foreach ($validated['settings'] as $settingData) {
                try {
                    // Validate content based on type
                    $content = $settingData['content'];

                    if ($settingData['type'] === 'json' && !empty($content)) {
                        // Validate JSON format
                        $decoded = json_decode($content, true);
                        if (json_last_error() !== JSON_ERROR_NONE) {
                            $errors[] = "Invalid JSON format for setting '{$settingData['key']}': " . json_last_error_msg();
                            continue;
                        }
                        $content = json_encode($decoded); // Normalize JSON
                    } elseif ($settingData['type'] === 'boolean') {
                        $content = $content ? '1' : '0';
                    } elseif ($settingData['type'] === 'number' && !empty($content)) {
                        if (!is_numeric($content)) {
                            $errors[] = "Invalid number format for setting '{$settingData['key']}'";
                            continue;
                        }
                        $content = (string) $content;
                    }

                    // Update or create setting
                    $setting = \App\Models\SiteSetting::updateOrCreate(
                        ['key' => $settingData['key']],
                        [
                            'title' => $settingData['title'] ?? ucwords(str_replace(['_', '-'], ' ', $settingData['key'])),
                            'content' => $content ?? '',
                            'type' => $settingData['type'],
                            'is_active' => $settingData['is_active'] ?? true,
                        ]
                    );

                    $updatedSettings[] = [
                        'key' => $setting->key,
                        'title' => $setting->title,
                        'content' => $setting->parsed_content,
                        'type' => $setting->type,
                        'is_active' => $setting->is_active,
                    ];

                } catch (\Exception $e) {
                    $errors[] = "Failed to update setting '{$settingData['key']}': " . $e->getMessage();
                }
            }

            if (!empty($errors)) {
                DB::rollBack();
                return ApiResponse::error(
                    'Some settings failed to update',
                    400,
                    ['errors' => $errors]
                );
            }

            DB::commit();

            Log::info('Site settings updated', [
                'updated_settings' => array_column($updatedSettings, 'key'),
                'admin_user_id' => $request->user()->id,
                'total_updated' => count($updatedSettings)
            ]);

            return ApiResponse::success(
                [
                    'updated_settings' => $updatedSettings,
                    'total_updated' => count($updatedSettings),
                    'message' => 'Site settings updated successfully'
                ],
                'Site settings updated successfully'
            );

        } catch (\Illuminate\Validation\ValidationException $e) {
            return ApiResponse::validationError($e->errors());

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Failed to update site settings', [
                'error' => $e->getMessage(),
                'admin_user_id' => $request->user()->id
            ]);

            return ApiResponse::error(
                'Failed to update site settings',
                500,
                null,
                $e->getMessage()
            );
        }
    }

    // Gallery management
    public function gallery(Request $request): JsonResponse
    {
        try {
            $query = Gallery::query();

            // Handle sorting
            $sortBy = $request->get('sort', 'sort_order');
            if ($sortBy === 'created_at') {
                $query->orderBy('created_at', 'desc')->orderBy('sort_order');
            } else {
                $query->orderBy('sort_order')->orderBy('created_at', 'desc');
            }

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
                GalleryResource::class,
                'Gallery items retrieved successfully',
                [
                    'filters' => [
                        'category' => $request->get('category'),
                        'is_active' => $request->get('is_active'),
                        'search' => $request->get('search'),
                        'sort' => $request->get('sort'),
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

    public function storeGallery(GalleryRequest $request): JsonResponse
    {
        try {
            $validated = $request->validated();

            DB::beginTransaction();

            // Handle image upload
            $imagePath = null;
            if ($request->hasFile('image')) {
                $imagePath = $request->file('image')->store('gallery', 'public');
            }

            // Create gallery item (sort_order auto-handled by GalleryRequest)
            $gallery = Gallery::create([
                'title' => $validated['title'],
                'description' => $validated['description'] ?? null,
                'image_path' => $imagePath,
                'alt_text' => $validated['alt_text'] ?? $validated['title'],
                'category' => $validated['category'],
                'sort_order' => $validated['sort_order'],
                'is_active' => $validated['is_active'] ?? true,
            ]);

            DB::commit();

            Log::info('Gallery item created', [
                'gallery_id' => $gallery->id,
                'admin_user_id' => $request->user()->id
            ]);

            return ApiResponse::success(
                new GalleryResource($gallery),
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
                new GalleryResource($gallery),
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

    public function updateGallery(GalleryRequest $request, Gallery $gallery): JsonResponse
    {
        try {
            $validated = $request->validated();

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

            // Update gallery item (sort_order auto-handled by GalleryRequest)
            $gallery->update([
                'title' => $validated['title'],
                'description' => $validated['description'] ?? null,
                'image_path' => $imagePath,
                'alt_text' => $validated['alt_text'] ?? $validated['title'],
                'category' => $validated['category'],
                'sort_order' => $validated['sort_order'],
                'is_active' => $validated['is_active'] ?? true,
            ]);

            DB::commit();

            Log::info('Gallery item updated', [
                'gallery_id' => $gallery->id,
                'admin_user_id' => $request->user()->id
            ]);

            return ApiResponse::success(
                new GalleryResource($gallery->fresh()),
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
                'admin_user_id' => auth('sanctum')->id()
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

    public function reorderGallery(Request $request): JsonResponse
    {
        try {
            // Support both single item and bulk reorder
            $requestData = $request->all();

            if (isset($requestData['id']) && isset($requestData['sort_order'])) {
                // Single item reorder
                $requestData = ['order' => [$requestData]];
            }

            $validator = validator($requestData, [
                'order' => 'required|array|min:1',
                'order.*.id' => 'required|integer|exists:galleries,id',
                'order.*.sort_order' => 'required|integer|min:0'
            ]);

            if ($validator->fails()) {
                return ApiResponse::validationError($validator->errors()->toArray());
            }

            DB::beginTransaction();

            $order = $requestData['order'];
            foreach ($order as $item) {
                Gallery::where('id', $item['id'])->update([
                    'sort_order' => $item['sort_order'],
                    'updated_at' => now()
                ]);
            }

            DB::commit();

            Log::info('Gallery items reordered', [
                'items_count' => count($order),
                'admin_user_id' => $request->user()->id,
                'items' => $order
            ]);

            return ApiResponse::success(
                [
                    'reordered_count' => count($order),
                    'items' => $order
                ],
                'Gallery items reordered successfully'
            );

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Failed to reorder gallery items', [
                'error' => $e->getMessage(),
                'request_data' => $request->all(),
                'admin_user_id' => $request->user()->id
            ]);

            return ApiResponse::error(
                'Failed to reorder gallery items',
                500,
                null,
                $e->getMessage()
            );
        }
    }

    public function getFeaturedSlots(): JsonResponse
    {
        try {
            $setting = \App\Models\SiteSetting::where('key', 'homepage_gallery_slots')->first();

            // Safe JSON decode with fallback - ensure string keys for slots
            $slots = ['1' => null, '2' => null, '3' => null, '4' => null];
            if ($setting && $setting->content) {
                $decoded = json_decode($setting->content, true);
                if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                    // Ensure we maintain string keys for slots 1-4
                    foreach (['1', '2', '3', '4'] as $slot) {
                        if (isset($decoded[$slot])) {
                            $slots[$slot] = $decoded[$slot];
                        }
                    }
                }
            }

            // Get gallery items for the slots
            $galleryIds = array_filter($slots);
            $galleries = [];

            if (!empty($galleryIds)) {
                $galleryItems = Gallery::whereIn('id', $galleryIds)->active()->get();
                foreach ($galleryItems as $gallery) {
                    $galleries[$gallery->id] = new GalleryResource($gallery);
                }
            }

            // Get all available galleries
            $availableGalleries = Gallery::active()->ordered()->get();

            return ApiResponse::success([
                'slots' => $slots,
                'galleries' => $galleries,
                'available_galleries' => GalleryResource::collection($availableGalleries)
            ], 'Featured slots retrieved successfully');

        } catch (\Exception $e) {
            Log::error('Failed to get featured gallery slots', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            // Return safe defaults instead of error
            return ApiResponse::success([
                'slots' => ['1' => null, '2' => null, '3' => null, '4' => null],
                'galleries' => [],
                'available_galleries' => []
            ], 'Featured slots retrieved with defaults (error occurred)');
        }
    }

    public function updateFeaturedSlots(Request $request): JsonResponse
    {
        try {
            $request->validate([
                '1' => 'nullable|integer|exists:galleries,id',
                '2' => 'nullable|integer|exists:galleries,id',
                '3' => 'nullable|integer|exists:galleries,id',
                '4' => 'nullable|integer|exists:galleries,id',
            ]);

            // Validate uniqueness
            $slots = [
                '1' => $request->input('1'),
                '2' => $request->input('2'),
                '3' => $request->input('3'),
                '4' => $request->input('4'),
            ];

            $nonNullSlots = array_filter($slots);
            if (count($nonNullSlots) !== count(array_unique($nonNullSlots))) {
                return ApiResponse::validationError([
                    'slots' => ['Each gallery can only be assigned to one slot']
                ]);
            }

            // Validate that all selected galleries are active
            if (!empty($nonNullSlots)) {
                $activeCount = Gallery::whereIn('id', $nonNullSlots)->where('is_active', true)->count();
                if ($activeCount !== count($nonNullSlots)) {
                    return ApiResponse::validationError([
                        'slots' => ['All selected galleries must be active']
                    ]);
                }
            }

            DB::beginTransaction();

            \App\Models\SiteSetting::updateOrCreate(
                ['key' => 'homepage_gallery_slots'],
                [
                    'title' => 'Homepage Gallery Featured Slots',
                    'content' => json_encode($slots),
                    'type' => 'json',
                    'is_active' => true
                ]
            );

            DB::commit();

            Log::info('Featured gallery slots updated', [
                'slots' => $slots,
                'admin_user_id' => $request->user()->id
            ]);

            return ApiResponse::success(
                ['slots' => $slots],
                'Featured slots updated successfully'
            );

        } catch (\Illuminate\Validation\ValidationException $e) {
            return ApiResponse::validationError($e->errors());

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Failed to update featured gallery slots', [
                'error' => $e->getMessage(),
                'admin_user_id' => $request->user()->id
            ]);

            return ApiResponse::error(
                'Failed to update featured gallery slots',
                500,
                null,
                $e->getMessage()
            );
        }
    }
}
