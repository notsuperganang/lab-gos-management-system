<?php

namespace App\Http\Controllers\Api\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Http\Requests\SuperAdmin\UserCreateRequest;
use App\Http\Requests\SuperAdmin\UserUpdateRequest;
use App\Http\Resources\ApiResponse;
use App\Http\Resources\SuperAdmin\UserResource;
use App\Http\Resources\SuperAdmin\UserCollection;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class UserManagementController extends Controller
{
    /**
     * Get users with filtering and pagination.
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = User::with(['roles', 'permissions']);
            
            // Apply filters
            if ($request->filled('role')) {
                $query->where('role', $request->get('role'));
            }
            
            if ($request->filled('is_active')) {
                $query->where('is_active', $request->boolean('is_active'));
            }
            
            if ($request->filled('search')) {
                $search = $request->get('search');
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%")
                      ->orWhere('position', 'like', "%{$search}%");
                });
            }
            
            // Sorting
            $sortBy = $request->get('sort_by', 'created_at');
            $sortOrder = $request->get('sort_order', 'desc');
            $query->orderBy($sortBy, $sortOrder);
            
            $perPage = min($request->get('per_page', 15), 100);
            $users = $query->paginate($perPage);
            
            return ApiResponse::paginated(
                $users,
                UserResource::class,
                'Users retrieved successfully',
                [
                    'filters' => [
                        'role' => $request->get('role'),
                        'is_active' => $request->get('is_active'),
                        'search' => $request->get('search'),
                    ],
                    'available_roles' => ['superadmin', 'admin', 'staff'],
                ]
            );
            
        } catch (\Exception $e) {
            Log::error('Failed to retrieve users', [
                'error' => $e->getMessage(),
                'superadmin_id' => $request->user()->id
            ]);
            
            return ApiResponse::error(
                'Failed to retrieve users',
                500,
                null,
                $e->getMessage()
            );
        }
    }
    
    /**
     * Create a new user.
     */
    public function store(UserCreateRequest $request): JsonResponse
    {
        try {
            $validated = $request->validated();
            
            DB::beginTransaction();
            
            // Handle avatar upload
            $avatarPath = null;
            if ($request->hasFile('avatar')) {
                $avatarPath = $request->file('avatar')->store('avatars', 'public');
            }
            
            // Create user
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'phone' => $validated['phone'] ?? null,
                'position' => $validated['position'] ?? null,
                'role' => $validated['role'],
                'avatar_path' => $avatarPath,
                'is_active' => $validated['is_active'] ?? true,
            ]);
            
            // Assign role using Spatie Permission
            $user->assignRole($validated['role']);
            
            DB::commit();
            
            Log::info('User created by SuperAdmin', [
                'user_id' => $user->id,
                'superadmin_id' => $request->user()->id,
                'role' => $validated['role']
            ]);
            
            return ApiResponse::success(
                new UserResource($user->load(['roles', 'permissions'])),
                'User created successfully',
                201
            );
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            return ApiResponse::validationError($e->errors());
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Failed to create user', [
                'error' => $e->getMessage(),
                'superadmin_id' => $request->user()->id,
                'data' => $validated ?? []
            ]);
            
            return ApiResponse::error(
                'Failed to create user',
                500,
                null,
                $e->getMessage()
            );
        }
    }
    
    /**
     * Get user details.
     */
    public function show(User $user): JsonResponse
    {
        try {
            $user->load(['roles', 'permissions']);
            
            return ApiResponse::success(
                new UserResource($user),
                'User retrieved successfully'
            );
            
        } catch (\Exception $e) {
            Log::error('Failed to retrieve user details', [
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);
            
            return ApiResponse::error(
                'Failed to retrieve user details',
                500,
                null,
                $e->getMessage()
            );
        }
    }
    
    /**
     * Update user information.
     */
    public function update(UserUpdateRequest $request, User $user): JsonResponse
    {
        try {
            $validated = $request->validated();
            
            DB::beginTransaction();
            
            $oldAvatarPath = $user->avatar_path;
            $avatarPath = $oldAvatarPath;
            
            // Handle avatar removal
            if ($request->boolean('remove_avatar')) {
                $avatarPath = null;
                if ($oldAvatarPath && Storage::disk('public')->exists($oldAvatarPath)) {
                    Storage::disk('public')->delete($oldAvatarPath);
                }
            }
            // Handle new avatar upload
            elseif ($request->hasFile('avatar')) {
                $avatarPath = $request->file('avatar')->store('avatars', 'public');
                
                // Delete old avatar if exists
                if ($oldAvatarPath && Storage::disk('public')->exists($oldAvatarPath)) {
                    Storage::disk('public')->delete($oldAvatarPath);
                }
            }
            
            // Prepare update data
            $updateData = [
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'] ?? null,
                'position' => $validated['position'] ?? null,
                'role' => $validated['role'],
                'avatar_path' => $avatarPath,
                'is_active' => $validated['is_active'] ?? $user->is_active,
            ];
            
            // Update password if provided
            if (!empty($validated['password'])) {
                $updateData['password'] = Hash::make($validated['password']);
            }
            
            // Update user
            $user->update($updateData);
            
            // Update role if changed
            if ($user->role !== $validated['role']) {
                $user->syncRoles([$validated['role']]);
            }
            
            DB::commit();
            
            Log::info('User updated by SuperAdmin', [
                'user_id' => $user->id,
                'superadmin_id' => $request->user()->id,
                'changes' => array_keys($updateData)
            ]);
            
            return ApiResponse::success(
                new UserResource($user->fresh()->load(['roles', 'permissions'])),
                'User updated successfully'
            );
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            return ApiResponse::validationError($e->errors());
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Failed to update user', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
                'superadmin_id' => $request->user()->id
            ]);
            
            return ApiResponse::error(
                'Failed to update user',
                500,
                null,
                $e->getMessage()
            );
        }
    }
    
    /**
     * Delete user (soft delete for safety).
     */
    public function destroy(User $user): JsonResponse
    {
        try {
            // Prevent deletion of current user
            if (auth()->id() === $user->id) {
                return ApiResponse::error(
                    'Cannot delete your own account',
                    400
                );
            }
            
            // Prevent deletion of last superadmin
            if ($user->hasRole('superadmin') && User::role('superadmin')->count() <= 1) {
                return ApiResponse::error(
                    'Cannot delete the last superadmin user',
                    400
                );
            }
            
            DB::beginTransaction();
            
            $userId = $user->id;
            $userName = $user->name;
            
            // Delete avatar if exists
            if ($user->avatar_path && Storage::disk('public')->exists($user->avatar_path)) {
                Storage::disk('public')->delete($user->avatar_path);
            }
            
            // Remove roles and permissions
            $user->roles()->detach();
            $user->permissions()->detach();
            
            // Delete user
            $user->delete();
            
            DB::commit();
            
            Log::info('User deleted by SuperAdmin', [
                'deleted_user_id' => $userId,
                'deleted_user_name' => $userName,
                'superadmin_id' => auth()->id()
            ]);
            
            return ApiResponse::success(
                ['id' => $userId, 'name' => $userName],
                'User deleted successfully'
            );
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Failed to delete user', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
                'superadmin_id' => auth()->id()
            ]);
            
            return ApiResponse::error(
                'Failed to delete user',
                500,
                null,
                $e->getMessage()
            );
        }
    }
    
    /**
     * Update user status (activate/deactivate).
     */
    public function updateStatus(Request $request, User $user): JsonResponse
    {
        try {
            $validated = $request->validate([
                'is_active' => 'required|boolean'
            ]);
            
            // Prevent deactivating current user
            if (auth()->id() === $user->id && !$validated['is_active']) {
                return ApiResponse::error(
                    'Cannot deactivate your own account',
                    400
                );
            }
            
            // Prevent deactivating last active superadmin
            if ($user->hasRole('superadmin') && !$validated['is_active']) {
                $activeSuperadmins = User::role('superadmin')->where('is_active', true)->count();
                if ($activeSuperadmins <= 1) {
                    return ApiResponse::error(
                        'Cannot deactivate the last active superadmin',
                        400
                    );
                }
            }
            
            $user->update(['is_active' => $validated['is_active']]);
            
            Log::info('User status updated by SuperAdmin', [
                'user_id' => $user->id,
                'new_status' => $validated['is_active'] ? 'active' : 'inactive',
                'superadmin_id' => $request->user()->id
            ]);
            
            return ApiResponse::success(
                new UserResource($user->load(['roles', 'permissions'])),
                'User status updated successfully'
            );
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            return ApiResponse::validationError($e->errors());
            
        } catch (\Exception $e) {
            Log::error('Failed to update user status', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
                'superadmin_id' => $request->user()->id
            ]);
            
            return ApiResponse::error(
                'Failed to update user status',
                500,
                null,
                $e->getMessage()
            );
        }
    }
}