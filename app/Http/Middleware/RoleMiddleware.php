<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        $user = $request->user();
        
        // Check if user is authenticated
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Authentication required.'
            ], 401);
        }
        
        // Check if user is active
        if (!$user->is_active) {
            return response()->json([
                'success' => false,
                'message' => 'Your account has been deactivated. Please contact an administrator.'
            ], 403);
        }
        
        // Role-specific checks
        switch ($role) {
            case 'admin':
                if (!$user->isAdmin()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Admin access required.'
                    ], 403);
                }
                break;
                
            case 'superadmin':
                if (!$user->isSuperAdmin()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Super admin access required.'
                    ], 403);
                }
                break;
                
            default:
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid role specified.'
                ], 403);
        }
        
        return $next($request);
    }
}