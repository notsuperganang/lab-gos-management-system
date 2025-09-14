<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateProfileRequest;
use App\Http\Requests\Admin\UpdatePasswordRequest;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class ProfileController extends Controller
{
    /**
     * Update the authenticated user's profile.
     */
    public function update(Request $request): JsonResponse
    {
        try {
            $user = $request->user();

            // Validate the request
            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|max:255|unique:users,email,' . $user->id,
                'phone' => 'nullable|string|max:20',
                'position' => 'nullable|string|max:255',
                'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // 2MB max
            ]);

            // Handle avatar upload
            if ($request->hasFile('avatar')) {
                // Delete old avatar if exists
                if ($user->avatar_path && Storage::disk('public')->exists($user->avatar_path)) {
                    Storage::disk('public')->delete($user->avatar_path);
                }

                // Store new avatar
                $avatarPath = $request->file('avatar')->store('staff', 'public');
                $validatedData['avatar_path'] = $avatarPath;
            }

            // Remove avatar field from validated data to avoid confusion
            unset($validatedData['avatar']);

            // Update the user
            $user->update($validatedData);

            // Refresh user data
            $user->refresh();

            return response()->json([
                'success' => true,
                'message' => 'Profile updated successfully',
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'phone' => $user->phone,
                    'position' => $user->position,
                    'role' => $user->role,
                    'avatar_path' => $user->avatar_path,
                    'avatar_url' => $user->avatar_path ? asset('storage/' . $user->avatar_path) : null,
                ],
            ]);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);

        } catch (\Exception $e) {
            \Log::error('Profile update error: ' . $e->getMessage(), [
                'user_id' => $request->user()->id,
                'exception' => $e,
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to update profile. Please try again.',
            ], 500);
        }
    }

    /**
     * Update the authenticated user's password.
     */
    public function updatePassword(Request $request): JsonResponse
    {
        try {
            $user = $request->user();

            // Validate the request
            $validatedData = $request->validate([
                'current_password' => 'required|string',
                'new_password' => 'required|string|min:8|confirmed',
            ]);

            // Check if current password is correct
            if (!Hash::check($validatedData['current_password'], $user->password)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Current password is incorrect',
                    'errors' => [
                        'current_password' => ['The current password is incorrect.']
                    ],
                ], 422);
            }

            // Update password
            $user->update([
                'password' => Hash::make($validatedData['new_password']),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Password updated successfully',
            ]);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);

        } catch (\Exception $e) {
            \Log::error('Password update error: ' . $e->getMessage(), [
                'user_id' => $request->user()->id,
                'exception' => $e,
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to update password. Please try again.',
            ], 500);
        }
    }

    /**
     * Remove the authenticated user's avatar.
     */
    public function removeAvatar(Request $request): JsonResponse
    {
        try {
            $user = $request->user();

            // Delete avatar file if exists
            if ($user->avatar_path && Storage::disk('public')->exists($user->avatar_path)) {
                Storage::disk('public')->delete($user->avatar_path);
            }

            // Update user record
            $user->update(['avatar_path' => null]);

            return response()->json([
                'success' => true,
                'message' => 'Profile picture removed successfully',
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'phone' => $user->phone,
                    'position' => $user->position,
                    'role' => $user->role,
                    'avatar_path' => null,
                    'avatar_url' => null,
                ],
            ]);

        } catch (\Exception $e) {
            \Log::error('Avatar removal error: ' . $e->getMessage(), [
                'user_id' => $request->user()->id,
                'exception' => $e,
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to remove profile picture. Please try again.',
            ], 500);
        }
    }
}
