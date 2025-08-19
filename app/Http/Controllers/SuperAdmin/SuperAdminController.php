<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SuperAdminController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        // Middleware is handled by routes (web.php) - no need to apply here in Laravel 11+
    }

    /**
     * Show the user management index page.
     */
    public function usersIndex(): View
    {
        return view('superadmin.users.index');
    }

    /**
     * Show create user page.
     */
    public function usersCreate(): View
    {
        return view('superadmin.users.create');
    }

    /**
     * Store new user.
     */
    public function usersStore(Request $request)
    {
        // Implementation will use API endpoints
        return redirect()->route('superadmin.users.index')
                        ->with('success', 'User created successfully.');
    }

    /**
     * Show user details.
     */
    public function usersShow($id): View
    {
        return view('superadmin.users.show', compact('id'));
    }

    /**
     * Show edit user page.
     */
    public function usersEdit($id): View
    {
        return view('superadmin.users.edit', compact('id'));
    }

    /**
     * Update user.
     */
    public function usersUpdate(Request $request, $id)
    {
        // Implementation will use API endpoints
        return redirect()->route('superadmin.users.index')
                        ->with('success', 'User updated successfully.');
    }

    /**
     * Delete user.
     */
    public function usersDestroy($id)
    {
        // Implementation will use API endpoints
        return redirect()->route('superadmin.users.index')
                        ->with('success', 'User deleted successfully.');
    }

    /**
     * Update user status (activate/deactivate).
     */
    public function usersUpdateStatus(Request $request, $id)
    {
        // Implementation will use API endpoints
        return redirect()->route('superadmin.users.index')
                        ->with('success', 'User status updated successfully.');
    }
}