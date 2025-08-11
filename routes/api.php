<?php

use App\Http\Controllers\Api\Public\SiteController;
use App\Http\Controllers\Api\Public\StaffController;
use App\Http\Controllers\Api\Public\ArticleController;
use App\Http\Controllers\Api\Public\EquipmentController;
use App\Http\Controllers\Api\Public\RequestController;
use App\Http\Controllers\Api\Public\TrackingController;
use App\Http\Controllers\Api\Admin\RequestManagementController;
use App\Http\Controllers\Api\Admin\EquipmentManagementController;
use App\Http\Controllers\Api\Admin\ContentManagementController;
use App\Http\Controllers\Api\Admin\DashboardController;
use App\Http\Controllers\Api\SuperAdmin\UserManagementController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Test route for authenticated user
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

/*
|--------------------------------------------------------------------------
| Public API Routes (No Authentication Required)
|--------------------------------------------------------------------------
| These routes are accessible by anyone and primarily serve the frontend
| and allow students/guests to browse content and submit service requests.
*/

// Site information and settings
Route::get('/site/settings', [SiteController::class, 'settings'])
    ->name('api.site.settings');

// Staff members
Route::get('/staff', [StaffController::class, 'index'])
    ->name('api.staff.index');
Route::get('/staff/{staff}', [StaffController::class, 'show'])
    ->name('api.staff.show');

// Articles and news
Route::get('/articles', [ArticleController::class, 'index'])
    ->name('api.articles.index');
Route::get('/articles/{article:slug}', [ArticleController::class, 'show'])
    ->name('api.articles.show');

// Equipment catalog
Route::get('/equipment/categories', [EquipmentController::class, 'categories'])
    ->name('api.equipment.categories');
Route::get('/equipment', [EquipmentController::class, 'index'])
    ->name('api.equipment.index');
Route::get('/equipment/{equipment}', [EquipmentController::class, 'show'])
    ->name('api.equipment.show');

// Request submissions
Route::prefix('requests')->name('api.requests.')->group(function () {
    Route::post('/borrow', [RequestController::class, 'submitBorrowRequest'])
        ->name('borrow');
    Route::post('/visit', [RequestController::class, 'submitVisitRequest'])
        ->name('visit');
    Route::post('/testing', [RequestController::class, 'submitTestingRequest'])
        ->name('testing');
});

// Request tracking
Route::prefix('tracking')->name('api.tracking.')->group(function () {
    Route::get('/borrow/{requestId}', [TrackingController::class, 'trackBorrowRequest'])
        ->name('borrow');
    Route::get('/visit/{requestId}', [TrackingController::class, 'trackVisitRequest'])
        ->name('visit');
    Route::get('/testing/{requestId}', [TrackingController::class, 'trackTestingRequest'])
        ->name('testing');
});

/*
|--------------------------------------------------------------------------
| Admin API Routes (Authentication Required - Admin/Super Admin)
|--------------------------------------------------------------------------
| These routes require authentication and admin/super_admin role.
| They provide CRUD operations for managing the laboratory system.
*/

Route::middleware(['auth:sanctum', 'role:admin'])->prefix('admin')->name('api.admin.')->group(function () {
    
    // Dashboard and statistics
    Route::get('/dashboard/stats', [DashboardController::class, 'statistics'])
        ->name('dashboard.stats');
    Route::get('/activity-logs', [DashboardController::class, 'activityLogs'])
        ->name('activity-logs');
    Route::get('/notifications', [DashboardController::class, 'notifications'])
        ->name('notifications');
    
    // Request management
    Route::prefix('requests')->name('requests.')->group(function () {
        // Borrow requests
        Route::get('/borrow', [RequestManagementController::class, 'borrowRequests'])
            ->name('borrow.index');
        Route::get('/borrow/{borrowRequest}', [RequestManagementController::class, 'showBorrowRequest'])
            ->name('borrow.show');
        Route::put('/borrow/{borrowRequest}', [RequestManagementController::class, 'updateBorrowRequest'])
            ->name('borrow.update');
        Route::put('/borrow/{borrowRequest}/approve', [RequestManagementController::class, 'approveBorrowRequest'])
            ->name('borrow.approve');
        Route::put('/borrow/{borrowRequest}/reject', [RequestManagementController::class, 'rejectBorrowRequest'])
            ->name('borrow.reject');
        
        // Visit requests
        Route::get('/visit', [RequestManagementController::class, 'visitRequests'])
            ->name('visit.index');
        Route::get('/visit/{visitRequest}', [RequestManagementController::class, 'showVisitRequest'])
            ->name('visit.show');
        Route::put('/visit/{visitRequest}', [RequestManagementController::class, 'updateVisitRequest'])
            ->name('visit.update');
        Route::put('/visit/{visitRequest}/approve', [RequestManagementController::class, 'approveVisitRequest'])
            ->name('visit.approve');
        Route::put('/visit/{visitRequest}/reject', [RequestManagementController::class, 'rejectVisitRequest'])
            ->name('visit.reject');
        
        // Testing requests
        Route::get('/testing', [RequestManagementController::class, 'testingRequests'])
            ->name('testing.index');
        Route::get('/testing/{testingRequest}', [RequestManagementController::class, 'showTestingRequest'])
            ->name('testing.show');
        Route::put('/testing/{testingRequest}', [RequestManagementController::class, 'updateTestingRequest'])
            ->name('testing.update');
        Route::put('/testing/{testingRequest}/approve', [RequestManagementController::class, 'approveTestingRequest'])
            ->name('testing.approve');
        Route::put('/testing/{testingRequest}/reject', [RequestManagementController::class, 'rejectTestingRequest'])
            ->name('testing.reject');
    });
    
    // Equipment management
    Route::prefix('equipment')->name('equipment.')->group(function () {
        Route::get('/', [EquipmentManagementController::class, 'index'])
            ->name('index');
        Route::post('/', [EquipmentManagementController::class, 'store'])
            ->name('store');
        Route::get('/{equipment}', [EquipmentManagementController::class, 'show'])
            ->name('show');
        Route::put('/{equipment}', [EquipmentManagementController::class, 'update'])
            ->name('update');
        Route::delete('/{equipment}', [EquipmentManagementController::class, 'destroy'])
            ->name('destroy');
        
        // Equipment categories
        Route::get('/categories', [EquipmentManagementController::class, 'categories'])
            ->name('categories.index');
        Route::post('/categories', [EquipmentManagementController::class, 'storeCategory'])
            ->name('categories.store');
        Route::put('/categories/{category}', [EquipmentManagementController::class, 'updateCategory'])
            ->name('categories.update');
        Route::delete('/categories/{category}', [EquipmentManagementController::class, 'destroyCategory'])
            ->name('categories.destroy');
    });
    
    // Content management
    Route::prefix('content')->name('content.')->group(function () {
        // Articles
        Route::get('/articles', [ContentManagementController::class, 'articles'])
            ->name('articles.index');
        Route::post('/articles', [ContentManagementController::class, 'storeArticle'])
            ->name('articles.store');
        Route::get('/articles/{article}', [ContentManagementController::class, 'showArticle'])
            ->name('articles.show');
        Route::put('/articles/{article}', [ContentManagementController::class, 'updateArticle'])
            ->name('articles.update');
        Route::delete('/articles/{article}', [ContentManagementController::class, 'destroyArticle'])
            ->name('articles.destroy');
        
        // Staff members
        Route::get('/staff', [ContentManagementController::class, 'staff'])
            ->name('staff.index');
        Route::post('/staff', [ContentManagementController::class, 'storeStaff'])
            ->name('staff.store');
        Route::get('/staff/{staff}', [ContentManagementController::class, 'showStaff'])
            ->name('staff.show');
        Route::put('/staff/{staff}', [ContentManagementController::class, 'updateStaff'])
            ->name('staff.update');
        Route::delete('/staff/{staff}', [ContentManagementController::class, 'destroyStaff'])
            ->name('staff.destroy');
        
        // Site settings
        Route::get('/site-settings', [ContentManagementController::class, 'siteSettings'])
            ->name('site-settings.index');
        Route::put('/site-settings', [ContentManagementController::class, 'updateSiteSettings'])
            ->name('site-settings.update');
    });
});

/*
|--------------------------------------------------------------------------
| Super Admin API Routes (Super Admin Only)
|--------------------------------------------------------------------------
| These routes require super_admin role specifically and provide
| user management capabilities for managing admin accounts.
*/

Route::middleware(['auth:sanctum', 'role:superadmin'])->prefix('superadmin')->name('api.superadmin.')->group(function () {
    
    // User management
    Route::prefix('users')->name('users.')->group(function () {
        Route::get('/', [UserManagementController::class, 'index'])
            ->name('index');
        Route::post('/', [UserManagementController::class, 'store'])
            ->name('store');
        Route::get('/{user}', [UserManagementController::class, 'show'])
            ->name('show');
        Route::put('/{user}', [UserManagementController::class, 'update'])
            ->name('update');
        Route::delete('/{user}', [UserManagementController::class, 'destroy'])
            ->name('destroy');
        Route::put('/{user}/status', [UserManagementController::class, 'updateStatus'])
            ->name('update-status');
    });
});