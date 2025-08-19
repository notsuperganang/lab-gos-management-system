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
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();
        
        // Check if user is authenticated
        if (!$user) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Authentication required.'
                ], 401);
            }
            return redirect()->route('login');
        }
        
        // Check if user is active
        if (isset($user->is_active) && !$user->is_active) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Your account has been deactivated. Please contact an administrator.'
                ], 403);
            }
            auth()->logout();
            return redirect()->route('login')->withErrors(['message' => 'Your account has been deactivated.']);
        }
        
        // Check if user has any of the required roles
        $hasRole = false;
        foreach ($roles as $role) {
            if ($this->userHasRole($user, $role)) {
                $hasRole = true;
                break;
            }
        }
        
        if (!$hasRole) {
            $rolesList = implode(' or ', $roles);
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => "Access denied. Requires {$rolesList} role."
                ], 403);
            }
            abort(403, "Access denied. Requires {$rolesList} role.");
        }
        
        return $next($request);
    }
    
    /**
     * Check if user has the specified role
     */
    protected function userHasRole($user, string $role): bool
    {
        // Handle multiple roles separated by |
        if (str_contains($role, '|')) {
            $allowedRoles = explode('|', $role);
            foreach ($allowedRoles as $allowedRole) {
                if ($this->userHasRole($user, trim($allowedRole))) {
                    return true;
                }
            }
            return false;
        }
        
        // Check specific role
        switch (strtolower($role)) {
            case 'admin':
                return $this->isAdmin($user);
                
            case 'super_admin':
            case 'superadmin':
                return $this->isSuperAdmin($user);
                
            case 'admin|super_admin':
            case 'admin|superadmin':
                return $this->isAdmin($user) || $this->isSuperAdmin($user);
                
            default:
                // Check if user has role property
                if (isset($user->role)) {
                    return strtolower($user->role) === strtolower($role);
                }
                
                // Try Spatie Permission package if available
                if (method_exists($user, 'hasRole')) {
                    return $user->hasRole($role);
                }
                
                return false;
        }
    }
    
    /**
     * Check if user is admin
     */
    protected function isAdmin($user): bool
    {
        // Check role property
        if (isset($user->role)) {
            return in_array(strtolower($user->role), ['admin', 'super_admin']);
        }
        
        // Check method if exists
        if (method_exists($user, 'isAdmin')) {
            return $user->isAdmin();
        }
        
        // Try Spatie Permission package
        if (method_exists($user, 'hasRole')) {
            return $user->hasRole(['admin', 'super_admin']);
        }
        
        return false;
    }
    
    /**
     * Check if user is super admin
     */
    protected function isSuperAdmin($user): bool
    {
        // Check role property
        if (isset($user->role)) {
            return strtolower($user->role) === 'super_admin';
        }
        
        // Check method if exists
        if (method_exists($user, 'isSuperAdmin')) {
            return $user->isSuperAdmin();
        }
        
        // Try Spatie Permission package
        if (method_exists($user, 'hasRole')) {
            return $user->hasRole('super_admin');
        }
        
        return false;
    }
}