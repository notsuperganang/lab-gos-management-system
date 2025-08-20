<?php

use App\Http\Controllers\Api\Admin\DashboardController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Admin API Routes (Session-based Authentication)
|--------------------------------------------------------------------------
| These routes use session authentication instead of Sanctum tokens
| for compatibility with the admin frontend interface.
*/

Route::middleware(['auth', 'role:admin'])->prefix('admin-api')->name('admin-api.')->group(function () {
    
    // Dashboard and statistics with session auth
    Route::get('/dashboard/stats', [DashboardController::class, 'statistics'])
        ->name('dashboard.stats');
    Route::get('/activity-logs', [DashboardController::class, 'activityLogs'])
        ->name('activity-logs');
    Route::get('/notifications', [DashboardController::class, 'notifications'])
        ->name('notifications');
        
});