<?php

namespace App\Http\Controllers\Api\Public;

use App\Http\Controllers\Controller;
use App\Models\StaffMember;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StaffController extends Controller
{
    /**
     * Get paginated list of active staff members
     * 
     * Supports filtering by position and search queries.
     * Returns staff members ordered by sort_order and name.
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = StaffMember::active()->ordered();
            
            // Apply filters
            if ($request->filled('position')) {
                $query->byPosition($request->get('position'));
            }
            
            if ($request->filled('search')) {
                $search = $request->get('search');
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('specialization', 'like', "%{$search}%")
                      ->orWhere('research_interests', 'like', "%{$search}%");
                });
            }
            
            // Pagination
            $perPage = min($request->get('per_page', 15), 50); // Max 50 items per page
            $staff = $query->paginate($perPage);
            
            // Transform data
            $staffData = $staff->getCollection()->map(function ($staffMember) {
                return [
                    'id' => $staffMember->id,
                    'name' => $staffMember->name,
                    'position' => $staffMember->position,
                    'specialization' => $staffMember->specialization,
                    'education' => $staffMember->education,
                    'email' => $staffMember->email,
                    'phone' => $staffMember->phone,
                    'photo_url' => $staffMember->photo_url,
                    'bio' => $staffMember->bio,
                    'research_interests' => $staffMember->research_interests,
                    'full_contact' => $staffMember->full_contact,
                ];
            });
            
            return response()->json([
                'success' => true,
                'message' => 'Staff members retrieved successfully',
                'data' => $staffData,
                'meta' => [
                    'pagination' => [
                        'current_page' => $staff->currentPage(),
                        'last_page' => $staff->lastPage(),
                        'per_page' => $staff->perPage(),
                        'total' => $staff->total(),
                        'from' => $staff->firstItem(),
                        'to' => $staff->lastItem(),
                    ],
                    'filters' => [
                        'position' => $request->get('position'),
                        'search' => $request->get('search'),
                    ]
                ]
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve staff members',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }
    
    /**
     * Get specific staff member details
     */
    public function show(StaffMember $staff): JsonResponse
    {
        try {
            // Check if staff member is active
            if (!$staff->is_active) {
                return response()->json([
                    'success' => false,
                    'message' => 'Staff member not found'
                ], 404);
            }
            
            $staffData = [
                'id' => $staff->id,
                'name' => $staff->name,
                'position' => $staff->position,
                'specialization' => $staff->specialization,
                'education' => $staff->education,
                'email' => $staff->email,
                'phone' => $staff->phone,
                'photo_url' => $staff->photo_url,
                'bio' => $staff->bio,
                'research_interests' => $staff->research_interests,
                'full_contact' => $staff->full_contact,
                'sort_order' => $staff->sort_order,
            ];
            
            return response()->json([
                'success' => true,
                'message' => 'Staff member retrieved successfully',
                'data' => $staffData
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve staff member',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }
}