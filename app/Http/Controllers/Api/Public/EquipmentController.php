<?php

namespace App\Http\Controllers\Api\Public;

use App\Http\Controllers\Controller;
use App\Models\Equipment;
use App\Models\Category;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EquipmentController extends Controller
{
    /**
     * Get paginated list of available equipment
     * 
     * Supports filtering by category, condition, availability status, and search queries.
     * Returns equipment with availability information for borrowing.
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = Equipment::with('category')->active();
            
            // Apply filters
            if ($request->filled('category_id')) {
                $query->byCategory($request->get('category_id'));
            }
            
            if ($request->filled('condition')) {
                $query->byCondition($request->get('condition'));
            }
            
            if ($request->filled('available_only')) {
                $query->available();
            }
            
            if ($request->filled('search')) {
                $search = $request->get('search');
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('model', 'like', "%{$search}%")
                      ->orWhere('manufacturer', 'like', "%{$search}%")
                      ->orWhereHas('category', function ($categoryQuery) use ($search) {
                          $categoryQuery->where('name', 'like', "%{$search}%");
                      });
                });
            }
            
            // Sorting
            $sortBy = $request->get('sort_by', 'name');
            $sortOrder = $request->get('sort_order', 'asc');
            
            if (in_array($sortBy, ['name', 'manufacturer', 'purchase_date', 'available_quantity'])) {
                $query->orderBy($sortBy, $sortOrder);
            } else {
                $query->orderBy('name', 'asc');
            }
            
            // Pagination
            $perPage = min($request->get('per_page', 12), 50); // Max 50 items per page
            $equipment = $query->paginate($perPage);
            
            // Transform data
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
                    'location' => $item->location,
                    'image_url' => $item->image_url,
                    'manual_url' => $item->manual_url,
                    'category' => $item->category ? [
                        'id' => $item->category->id,
                        'name' => $item->category->name,
                    ] : null,
                    'is_available' => $item->isAvailable(),
                    'needs_maintenance' => $item->needsMaintenance(),
                    'status_color' => $item->status_color,
                    'condition_color' => $item->condition_color,
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
                        'condition' => $request->get('condition'),
                        'available_only' => $request->get('available_only'),
                        'search' => $request->get('search'),
                        'sort_by' => $sortBy,
                        'sort_order' => $sortOrder,
                    ]
                ]
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve equipment',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }
    
    /**
     * Get specific equipment details
     */
    public function show(Equipment $equipment): JsonResponse
    {
        try {
            // Check if equipment is active
            if ($equipment->status !== 'active') {
                return response()->json([
                    'success' => false,
                    'message' => 'Equipment not available'
                ], 404);
            }
            
            // Load relationships
            $equipment->load('category');
            
            $equipmentData = [
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
            ];
            
            return response()->json([
                'success' => true,
                'message' => 'Equipment details retrieved successfully',
                'data' => $equipmentData
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve equipment details',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }
    
    /**
     * Get equipment categories
     */
    public function categories(): JsonResponse
    {
        try {
            $categories = Category::orderBy('name')->get(['id', 'name', 'description']);
            
            return response()->json([
                'success' => true,
                'message' => 'Equipment categories retrieved successfully',
                'data' => $categories
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve equipment categories',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }
}