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
     * Show the user management index page (API-driven).
     */
    public function usersIndex(): View
    {
        return view('superadmin.users.index');
    }
}