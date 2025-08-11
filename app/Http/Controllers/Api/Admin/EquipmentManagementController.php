<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\ApiResponse;
use App\Models\Equipment;
use App\Models\Category;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class EquipmentManagementController extends Controller
{
    /**
     * Get paginated list of equipment with filtering (Admin)
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = Equipment::with('category')
                ->orderBy('created_at', 'desc');
            
            // Apply filters
            if ($request->filled('category_id')) {
                $query->where('category_id', $request->get('category_id'));
            }
            
            if ($request->filled('status')) {
                $query->where('status', $request->get('status'));
            }
            
            if ($request->filled('condition_status')) {
                $query->where('condition_status', $request->get('condition_status'));
            }
            
            if ($request->filled('availability')) {
                if ($request->get('availability') === 'available') {
                    $query->available();
                } elseif ($request->get('availability') === 'low') {
                    $query->where('status', 'active')
                          ->whereRaw('available_quantity <= total_quantity * 0.2')
                          ->where('available_quantity', '>', 0);
                } elseif ($request->get('availability') === 'out_of_stock') {
                    $query->where('available_quantity', 0);
                }
            }
            
            if ($request->filled('maintenance')) {
                if ($request->get('maintenance') === 'due') {
                    $query->where(function($q) {
                        $q->where('next_maintenance_date', '<=', now()->addDays(30))
                          ->orWhere('status', 'maintenance');
                    });
                }
            }
            
            if ($request->filled('search')) {
                $search = $request->get('search');
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('model', 'like', "%{$search}%")
                      ->orWhere('manufacturer', 'like', "%{$search}%")
                      ->orWhere('location', 'like', "%{$search}%")
                      ->orWhereHas('category', function ($categoryQuery) use ($search) {
                          $categoryQuery->where('name', 'like', "%{$search}%");
                      });
                });
            }
            
            // Sorting
            $sortBy = $request->get('sort_by', 'created_at');
            $sortOrder = $request->get('sort_order', 'desc');
            
            if (in_array($sortBy, ['name', 'manufacturer', 'purchase_date', 'available_quantity', 'status', 'condition_status'])) {
                $query->orderBy($sortBy, $sortOrder);
            } else {
                $query->orderBy('created_at', 'desc');
            }
            
            $perPage = min($request->get('per_page', 20), 100);
            $equipment = $query->paginate($perPage);
            
            // Transform data for admin view
            $equipmentData = $equipment->getCollection()->map(function ($item) {
                return [
                    'id' => $item->id,
                    'name' => $item->name,
                    'model' => $item->model,
                    'manufacturer' => $item->manufacturer,
                    'specifications' => $item->specifications,
                    'total_quantity' => $item->total_quantity,
                    'available_quantity' => $item->available_quantity,
                    'status' => $item->status,
                    'condition_status' => $item->condition_status,
                    'purchase_date' => $item->purchase_date?->format('Y-m-d'),
                    'purchase_price' => $item->purchase_price,
                    'location' => $item->location,
                    'image_url' => $item->image_url,
                    'manual_url' => $item->manual_url,
                    'notes' => $item->notes,
                    'last_maintenance_date' => $item->last_maintenance_date?->format('Y-m-d'),
                    'next_maintenance_date' => $item->next_maintenance_date?->format('Y-m-d'),
                    'category' => $item->category ? [
                        'id' => $item->category->id,
                        'name' => $item->category->name,
                    ] : null,
                    'is_available' => $item->isAvailable(),
                    'needs_maintenance' => $item->needsMaintenance(),
                    'status_color' => $item->status_color,
                    'condition_color' => $item->condition_color,
                    'utilization_rate' => $item->total_quantity > 0 ? 
                        round((($item->total_quantity - $item->available_quantity) / $item->total_quantity) * 100, 1) : 0,
                    'created_at' => $item->created_at->format('Y-m-d H:i:s'),
                    'updated_at' => $item->updated_at->format('Y-m-d H:i:s'),
                ];
            });
            
            return response()->json([
                'success' => true,
                'message' => 'Equipment retrieved successfully',
                'data' => $equipmentData,
                'meta' => [
                    'pagination' => [
                        'current_page' => $equipment->currentPage(),
                        'last_page' => $equipment->lastPage(),
                        'per_page' => $equipment->perPage(),
                        'total' => $equipment->total(),
                        'from' => $equipment->firstItem(),
                        'to' => $equipment->lastItem(),
                    ],
                    'filters' => [
                        'category_id' => $request->get('category_id'),
                        'status' => $request->get('status'),
                        'condition_status' => $request->get('condition_status'),
                        'availability' => $request->get('availability'),
                        'maintenance' => $request->get('maintenance'),
                        'search' => $request->get('search'),
                        'sort_by' => $sortBy,
                        'sort_order' => $sortOrder,
                    ],
                    'summary' => [
                        'total_equipment' => Equipment::count(),
                        'active_equipment' => Equipment::where('status', 'active')->count(),
                        'available_equipment' => Equipment::available()->count(),
                        'maintenance_equipment' => Equipment::where('status', 'maintenance')->count(),
                        'retired_equipment' => Equipment::where('status', 'retired')->count(),
                    ],
                    'available_statuses' => [
                        'active' => 'Active',
                        'maintenance' => 'Maintenance',
                        'retired' => 'Retired',
                    ],
                    'available_conditions' => [
                        'excellent' => 'Excellent',
                        'good' => 'Good',
                        'fair' => 'Fair',
                        'poor' => 'Poor',
                    ],
                ]
            ]);
            
        } catch (\Exception $e) {
            Log::error('Failed to retrieve equipment list', [
                'error' => $e->getMessage(),
                'admin_user_id' => $request->user()->id
            ]);
            
            return ApiResponse::error(
                'Failed to retrieve equipment',
                500,
                null,
                $e->getMessage()
            );
        }
    }
    
    /**
     * Create new equipment
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'category_id' => 'required|exists:categories,id',
                'model' => 'nullable|string|max:255',
                'manufacturer' => 'nullable|string|max:255',
                'specifications' => 'nullable|array',
                'total_quantity' => 'required|integer|min:1',
                'available_quantity' => 'required|integer|min:0|lte:total_quantity',
                'status' => 'required|string|in:active,maintenance,retired',
                'condition_status' => 'required|string|in:excellent,good,fair,poor',
                'purchase_date' => 'nullable|date|before_or_equal:today',
                'purchase_price' => 'nullable|numeric|min:0',
                'location' => 'nullable|string|max:255',
                'notes' => 'nullable|string|max:1000',
                'last_maintenance_date' => 'nullable|date|before_or_equal:today',
                'next_maintenance_date' => 'nullable|date|after_or_equal:today',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:10240', // 10MB max
                'manual_file' => 'nullable|file|mimes:pdf,doc,docx|max:20480', // 20MB max
            ]);
            
            DB::beginTransaction();
            
            // Handle image upload
            $imagePath = null;
            if ($request->hasFile('image')) {
                $imagePath = $request->file('image')->store('equipment', 'public');
            }
            
            // Handle manual file upload
            $manualPath = null;
            if ($request->hasFile('manual_file')) {
                $manualPath = $request->file('manual_file')->store('equipment/manuals', 'public');
            }
            
            // Create equipment
            $equipment = Equipment::create([
                'name' => $validated['name'],
                'category_id' => $validated['category_id'],
                'model' => $validated['model'] ?? null,
                'manufacturer' => $validated['manufacturer'] ?? null,
                'specifications' => $validated['specifications'] ?? null,
                'total_quantity' => $validated['total_quantity'],
                'available_quantity' => $validated['available_quantity'],
                'status' => $validated['status'],
                'condition_status' => $validated['condition_status'],
                'purchase_date' => $validated['purchase_date'] ?? null,
                'purchase_price' => $validated['purchase_price'] ?? null,
                'location' => $validated['location'] ?? null,
                'image_path' => $imagePath,
                'manual_file_path' => $manualPath,
                'notes' => $validated['notes'] ?? null,
                'last_maintenance_date' => $validated['last_maintenance_date'] ?? null,
                'next_maintenance_date' => $validated['next_maintenance_date'] ?? null,
            ]);
            
            $equipment->load('category');
            
            DB::commit();
            
            Log::info('Equipment created', [
                'equipment_id' => $equipment->id,
                'equipment_name' => $equipment->name,
                'admin_user_id' => $request->user()->id
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Equipment created successfully',
                'data' => [
                    'id' => $equipment->id,
                    'name' => $equipment->name,
                    'model' => $equipment->model,
                    'manufacturer' => $equipment->manufacturer,
                    'specifications' => $equipment->specifications,
                    'total_quantity' => $equipment->total_quantity,
                    'available_quantity' => $equipment->available_quantity,
                    'status' => $equipment->status,
                    'condition_status' => $equipment->condition_status,
                    'purchase_date' => $equipment->purchase_date?->format('Y-m-d'),
                    'purchase_price' => $equipment->purchase_price,
                    'location' => $equipment->location,
                    'image_url' => $equipment->image_url,
                    'manual_url' => $equipment->manual_url,
                    'notes' => $equipment->notes,
                    'last_maintenance_date' => $equipment->last_maintenance_date?->format('Y-m-d'),
                    'next_maintenance_date' => $equipment->next_maintenance_date?->format('Y-m-d'),
                    'category' => [
                        'id' => $equipment->category->id,
                        'name' => $equipment->category->name,
                    ],
                    'created_at' => $equipment->created_at->format('Y-m-d H:i:s'),
                ]
            ], 201);
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            return ApiResponse::validationError($e->errors());
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            // Clean up uploaded files if equipment creation failed
            if (isset($imagePath) && Storage::disk('public')->exists($imagePath)) {
                Storage::disk('public')->delete($imagePath);
            }
            if (isset($manualPath) && Storage::disk('public')->exists($manualPath)) {
                Storage::disk('public')->delete($manualPath);
            }
            
            Log::error('Failed to create equipment', [
                'error' => $e->getMessage(),
                'admin_user_id' => $request->user()->id
            ]);
            
            return ApiResponse::error(
                'Failed to create equipment',
                500,
                null,
                $e->getMessage()
            );
        }
    }
    
    /**
     * Show equipment details
     */
    public function show(Equipment $equipment): JsonResponse
    {
        try {
            $equipment->load('category');
            
            return response()->json([
                'success' => true,
                'message' => 'Equipment details retrieved successfully',
                'data' => [
                    'id' => $equipment->id,
                    'name' => $equipment->name,
                    'model' => $equipment->model,
                    'manufacturer' => $equipment->manufacturer,
                    'specifications' => $equipment->specifications,
                    'total_quantity' => $equipment->total_quantity,
                    'available_quantity' => $equipment->available_quantity,
                    'status' => $equipment->status,
                    'condition_status' => $equipment->condition_status,
                    'purchase_date' => $equipment->purchase_date?->format('Y-m-d'),
                    'purchase_price' => $equipment->purchase_price,
                    'location' => $equipment->location,
                    'image_url' => $equipment->image_url,
                    'manual_url' => $equipment->manual_url,
                    'notes' => $equipment->notes,
                    'last_maintenance_date' => $equipment->last_maintenance_date?->format('Y-m-d'),
                    'next_maintenance_date' => $equipment->next_maintenance_date?->format('Y-m-d'),
                    'category' => $equipment->category ? [
                        'id' => $equipment->category->id,
                        'name' => $equipment->category->name,
                    ] : null,
                    'is_available' => $equipment->isAvailable(),
                    'needs_maintenance' => $equipment->needsMaintenance(),
                    'status_color' => $equipment->status_color,
                    'condition_color' => $equipment->condition_color,
                    'utilization_rate' => $equipment->total_quantity > 0 ? 
                        round((($equipment->total_quantity - $equipment->available_quantity) / $equipment->total_quantity) * 100, 1) : 0,
                    'created_at' => $equipment->created_at->format('Y-m-d H:i:s'),
                    'updated_at' => $equipment->updated_at->format('Y-m-d H:i:s'),
                ]
            ]);
            
        } catch (\Exception $e) {
            Log::error('Failed to retrieve equipment details', [
                'equipment_id' => $equipment->id,
                'error' => $e->getMessage()
            ]);
            
            return ApiResponse::error(
                'Failed to retrieve equipment details',
                500,
                null,
                $e->getMessage()
            );
        }
    }
    
    /**
     * Update equipment
     */
    public function update(Request $request, Equipment $equipment): JsonResponse
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'category_id' => 'required|exists:categories,id',
                'model' => 'nullable|string|max:255',
                'manufacturer' => 'nullable|string|max:255',
                'specifications' => 'nullable|array',
                'total_quantity' => 'required|integer|min:1',
                'available_quantity' => 'required|integer|min:0|lte:total_quantity',
                'status' => 'required|string|in:active,maintenance,retired',
                'condition_status' => 'required|string|in:excellent,good,fair,poor',
                'purchase_date' => 'nullable|date|before_or_equal:today',
                'purchase_price' => 'nullable|numeric|min:0',
                'location' => 'nullable|string|max:255',
                'notes' => 'nullable|string|max:1000',
                'last_maintenance_date' => 'nullable|date|before_or_equal:today',
                'next_maintenance_date' => 'nullable|date|after_or_equal:today',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:10240',
                'manual_file' => 'nullable|file|mimes:pdf,doc,docx|max:20480',
                'remove_image' => 'boolean',
                'remove_manual' => 'boolean',
            ]);
            
            DB::beginTransaction();
            
            $oldImagePath = $equipment->image_path;
            $oldManualPath = $equipment->manual_file_path;
            $imagePath = $oldImagePath;
            $manualPath = $oldManualPath;
            
            // Handle image removal
            if ($request->boolean('remove_image')) {
                $imagePath = null;
                if ($oldImagePath && Storage::disk('public')->exists($oldImagePath)) {
                    Storage::disk('public')->delete($oldImagePath);
                }
            }
            // Handle new image upload
            elseif ($request->hasFile('image')) {
                $imagePath = $request->file('image')->store('equipment', 'public');
                
                // Delete old image if exists
                if ($oldImagePath && Storage::disk('public')->exists($oldImagePath)) {
                    Storage::disk('public')->delete($oldImagePath);
                }
            }
            
            // Handle manual file removal
            if ($request->boolean('remove_manual')) {
                $manualPath = null;
                if ($oldManualPath && Storage::disk('public')->exists($oldManualPath)) {
                    Storage::disk('public')->delete($oldManualPath);
                }
            }
            // Handle new manual file upload
            elseif ($request->hasFile('manual_file')) {
                $manualPath = $request->file('manual_file')->store('equipment/manuals', 'public');
                
                // Delete old manual if exists
                if ($oldManualPath && Storage::disk('public')->exists($oldManualPath)) {
                    Storage::disk('public')->delete($oldManualPath);
                }
            }
            
            // Update equipment
            $equipment->update([
                'name' => $validated['name'],
                'category_id' => $validated['category_id'],
                'model' => $validated['model'] ?? null,
                'manufacturer' => $validated['manufacturer'] ?? null,
                'specifications' => $validated['specifications'] ?? null,
                'total_quantity' => $validated['total_quantity'],
                'available_quantity' => $validated['available_quantity'],
                'status' => $validated['status'],
                'condition_status' => $validated['condition_status'],
                'purchase_date' => $validated['purchase_date'] ?? null,
                'purchase_price' => $validated['purchase_price'] ?? null,
                'location' => $validated['location'] ?? null,
                'image_path' => $imagePath,
                'manual_file_path' => $manualPath,
                'notes' => $validated['notes'] ?? null,
                'last_maintenance_date' => $validated['last_maintenance_date'] ?? null,
                'next_maintenance_date' => $validated['next_maintenance_date'] ?? null,
            ]);
            
            $equipment->load('category');
            
            DB::commit();
            
            Log::info('Equipment updated', [
                'equipment_id' => $equipment->id,
                'equipment_name' => $equipment->name,
                'admin_user_id' => $request->user()->id
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Equipment updated successfully',
                'data' => [
                    'id' => $equipment->id,
                    'name' => $equipment->name,
                    'model' => $equipment->model,
                    'manufacturer' => $equipment->manufacturer,
                    'specifications' => $equipment->specifications,
                    'total_quantity' => $equipment->total_quantity,
                    'available_quantity' => $equipment->available_quantity,
                    'status' => $equipment->status,
                    'condition_status' => $equipment->condition_status,
                    'purchase_date' => $equipment->purchase_date?->format('Y-m-d'),
                    'purchase_price' => $equipment->purchase_price,
                    'location' => $equipment->location,
                    'image_url' => $equipment->image_url,
                    'manual_url' => $equipment->manual_url,
                    'notes' => $equipment->notes,
                    'last_maintenance_date' => $equipment->last_maintenance_date?->format('Y-m-d'),
                    'next_maintenance_date' => $equipment->next_maintenance_date?->format('Y-m-d'),
                    'category' => [
                        'id' => $equipment->category->id,
                        'name' => $equipment->category->name,
                    ],
                    'updated_at' => $equipment->updated_at->format('Y-m-d H:i:s'),
                ]
            ]);
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            return ApiResponse::validationError($e->errors());
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Failed to update equipment', [
                'equipment_id' => $equipment->id,
                'error' => $e->getMessage(),
                'admin_user_id' => $request->user()->id
            ]);
            
            return ApiResponse::error(
                'Failed to update equipment',
                500,
                null,
                $e->getMessage()
            );
        }
    }
    
    /**
     * Delete equipment
     */
    public function destroy(Equipment $equipment): JsonResponse
    {
        try {
            // Check if equipment is being used in any requests
            $activeBorrows = $equipment->activeBorrowRequests()->count();
            if ($activeBorrows > 0) {
                return ApiResponse::error(
                    'Cannot delete equipment that is currently in use or has active borrow requests',
                    400
                );
            }
            
            DB::beginTransaction();
            
            // Delete associated files
            if ($equipment->image_path && Storage::disk('public')->exists($equipment->image_path)) {
                Storage::disk('public')->delete($equipment->image_path);
            }
            
            if ($equipment->manual_file_path && Storage::disk('public')->exists($equipment->manual_file_path)) {
                Storage::disk('public')->delete($equipment->manual_file_path);
            }
            
            $equipmentId = $equipment->id;
            $equipmentName = $equipment->name;
            
            $equipment->delete();
            
            DB::commit();
            
            Log::info('Equipment deleted', [
                'equipment_id' => $equipmentId,
                'equipment_name' => $equipmentName,
                'admin_user_id' => auth()->id()
            ]);
            
            return ApiResponse::success(
                ['id' => $equipmentId],
                'Equipment deleted successfully'
            );
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Failed to delete equipment', [
                'equipment_id' => $equipment->id,
                'error' => $e->getMessage()
            ]);
            
            return ApiResponse::error(
                'Failed to delete equipment',
                500,
                null,
                $e->getMessage()
            );
        }
    }
    
    /**
     * Get equipment categories for admin management
     */
    public function categories(Request $request): JsonResponse
    {
        try {
            $query = Category::withCount('equipment')
                ->orderBy('name');
            
            if ($request->filled('search')) {
                $search = $request->get('search');
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%");
                });
            }
            
            $categories = $query->get();
            
            $categoryData = $categories->map(function ($category) {
                return [
                    'id' => $category->id,
                    'name' => $category->name,
                    'description' => $category->description,
                    'equipment_count' => $category->equipment_count,
                    'created_at' => $category->created_at->format('Y-m-d H:i:s'),
                    'updated_at' => $category->updated_at->format('Y-m-d H:i:s'),
                ];
            });
            
            return ApiResponse::success(
                $categoryData,
                'Equipment categories retrieved successfully'
            );
            
        } catch (\Exception $e) {
            Log::error('Failed to retrieve equipment categories', [
                'error' => $e->getMessage(),
                'admin_user_id' => $request->user()->id
            ]);
            
            return ApiResponse::error(
                'Failed to retrieve equipment categories',
                500,
                null,
                $e->getMessage()
            );
        }
    }
    
    /**
     * Create new equipment category
     */
    public function storeCategory(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255|unique:categories,name',
                'description' => 'nullable|string|max:1000',
            ]);
            
            $category = Category::create([
                'name' => $validated['name'],
                'description' => $validated['description'] ?? null,
            ]);
            
            Log::info('Equipment category created', [
                'category_id' => $category->id,
                'category_name' => $category->name,
                'admin_user_id' => $request->user()->id
            ]);
            
            return ApiResponse::success(
                [
                    'id' => $category->id,
                    'name' => $category->name,
                    'description' => $category->description,
                    'equipment_count' => 0,
                    'created_at' => $category->created_at->format('Y-m-d H:i:s'),
                ],
                'Equipment category created successfully',
                201
            );
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            return ApiResponse::validationError($e->errors());
            
        } catch (\Exception $e) {
            Log::error('Failed to create equipment category', [
                'error' => $e->getMessage(),
                'admin_user_id' => $request->user()->id
            ]);
            
            return ApiResponse::error(
                'Failed to create equipment category',
                500,
                null,
                $e->getMessage()
            );
        }
    }
    
    /**
     * Update equipment category
     */
    public function updateCategory(Request $request, Category $category): JsonResponse
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255|unique:categories,name,' . $category->id,
                'description' => 'nullable|string|max:1000',
            ]);
            
            $category->update([
                'name' => $validated['name'],
                'description' => $validated['description'] ?? null,
            ]);
            
            Log::info('Equipment category updated', [
                'category_id' => $category->id,
                'category_name' => $category->name,
                'admin_user_id' => $request->user()->id
            ]);
            
            return ApiResponse::success(
                [
                    'id' => $category->id,
                    'name' => $category->name,
                    'description' => $category->description,
                    'equipment_count' => $category->equipment()->count(),
                    'updated_at' => $category->updated_at->format('Y-m-d H:i:s'),
                ],
                'Equipment category updated successfully'
            );
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            return ApiResponse::validationError($e->errors());
            
        } catch (\Exception $e) {
            Log::error('Failed to update equipment category', [
                'category_id' => $category->id,
                'error' => $e->getMessage(),
                'admin_user_id' => $request->user()->id
            ]);
            
            return ApiResponse::error(
                'Failed to update equipment category',
                500,
                null,
                $e->getMessage()
            );
        }
    }
    
    /**
     * Delete equipment category
     */
    public function destroyCategory(Category $category): JsonResponse
    {
        try {
            // Check if category has equipment
            $equipmentCount = $category->equipment()->count();
            if ($equipmentCount > 0) {
                return ApiResponse::error(
                    "Cannot delete category that contains {$equipmentCount} equipment item(s). Please move or delete the equipment first.",
                    400
                );
            }
            
            $categoryId = $category->id;
            $categoryName = $category->name;
            
            $category->delete();
            
            Log::info('Equipment category deleted', [
                'category_id' => $categoryId,
                'category_name' => $categoryName,
                'admin_user_id' => auth()->id()
            ]);
            
            return ApiResponse::success(
                ['id' => $categoryId],
                'Equipment category deleted successfully'
            );
            
        } catch (\Exception $e) {
            Log::error('Failed to delete equipment category', [
                'category_id' => $category->id,
                'error' => $e->getMessage()
            ]);
            
            return ApiResponse::error(
                'Failed to delete equipment category',
                500,
                null,
                $e->getMessage()
            );
        }
    }
}