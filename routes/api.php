<?php

use App\Http\Controllers\Api\Public\SiteController;
use App\Http\Controllers\Api\Public\StaffController;
use App\Http\Controllers\Api\Public\ArticleController;
use App\Http\Controllers\Api\Public\EquipmentController;
use App\Http\Controllers\Api\Public\RequestController;
use App\Http\Controllers\Api\Public\TrackingController;
use App\Http\Controllers\Api\Public\GalleryController;
use App\Http\Controllers\Api\VisitSlotsController;
use App\Http\Controllers\Api\Admin\RequestManagementController;
use App\Http\Controllers\Api\Admin\EquipmentManagementController;
use App\Http\Controllers\Api\Admin\ContentManagementController;
use App\Http\Controllers\Api\Admin\DashboardController;
use App\Http\Controllers\Api\Admin\ReportsController;
use App\Http\Controllers\Api\Admin\CalendarController;
use App\Http\Controllers\Api\SuperAdmin\UserManagementController;
use App\Http\Controllers\Api\WhatsAppController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

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

// API Authentication endpoint for admin login
Route::post('/admin/login', function (Request $request) {
    $request->validate([
        'email' => 'required|email',
        'password' => 'required',
    ]);

    $user = User::where('email', $request->email)->first();

    if (!$user || !Hash::check($request->password, $user->password)) {
        return response()->json([
            'success' => false,
            'message' => 'Invalid credentials',
        ], 401);
    }

    // Check if user is admin or super_admin
    if (!in_array($user->role, ['admin', 'super_admin'])) {
        return response()->json([
            'success' => false,
            'message' => 'Unauthorized access - admin role required',
        ], 403);
    }

    // Generate Sanctum token
    $token = $user->createToken('admin-token')->plainTextToken;

    return response()->json([
        'success' => true,
        'message' => 'Login successful',
        'data' => [
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
            ],
            'token' => $token,
            'token_type' => 'Bearer',
        ]
    ]);
})->name('api.admin.login');

// API Logout endpoint
Route::middleware('auth:sanctum')->post('/admin/logout', function (Request $request) {
    $request->user()->currentAccessToken()->delete();

    return response()->json([
        'success' => true,
        'message' => 'Logged out successfully',
    ]);
})->name('api.admin.logout');

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

// Gallery
Route::get('/gallery', [GalleryController::class, 'index'])
    ->name('api.gallery.index');
Route::get('/gallery/{gallery}', [GalleryController::class, 'show'])
    ->name('api.gallery.show');

// Visit scheduling
Route::get('/visits/available-slots', [VisitSlotsController::class, 'getAvailableSlots'])
    ->name('api.visits.available-slots');

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

    // Cancel requests
    Route::delete('/borrow/{requestId}/cancel', [TrackingController::class, 'cancelBorrowRequest'])
        ->name('borrow.cancel');
    Route::delete('/visit/{requestId}/cancel', [TrackingController::class, 'cancelVisitRequest'])
        ->name('visit.cancel');
    Route::delete('/testing/{requestId}/cancel', [TrackingController::class, 'cancelTestingRequest'])
        ->name('testing.cancel');
});


/*
|--------------------------------------------------------------------------
| Admin API Routes (Authentication Required - Admin/Super Admin)
|--------------------------------------------------------------------------
| These routes require authentication and admin/super_admin role.
| They provide CRUD operations for managing the laboratory system.
*/

Route::middleware(['auth:sanctum', 'role:admin,super_admin'])->prefix('admin')->name('api.admin.')->group(function () {

    // Dashboard and statistics
    Route::get('/dashboard/stats', [DashboardController::class, 'statistics'])
        ->name('dashboard.stats');
    Route::get('/activity-logs', [DashboardController::class, 'activityLogs'])
        ->name('activity-logs');
    Route::get('/notifications', [DashboardController::class, 'notifications'])
        ->name('notifications');
    Route::post('/notifications/{notification}/read', [DashboardController::class, 'markNotificationAsRead'])
        ->name('notifications.mark-read');
    Route::post('/notifications/mark-all-read', [DashboardController::class, 'markAllNotificationsAsRead'])
        ->name('notifications.mark-all-read');

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
        
        // Borrow request letter management
        Route::get('/borrow/{borrowRequest}/letter', [RequestManagementController::class, 'getBorrowRequestLetter'])
            ->name('borrow.letter');
        Route::post('/borrow/{borrowRequest}/letter/regenerate', [RequestManagementController::class, 'regenerateBorrowRequestLetter'])
            ->name('borrow.letter.regenerate');

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

        // Visit request letter management
        Route::get('/visit/{visitRequest}/letter', [RequestManagementController::class, 'getVisitRequestLetter'])
            ->name('visit.letter');
        Route::post('/visit/{visitRequest}/letter/regenerate', [RequestManagementController::class, 'regenerateVisitRequestLetter'])
            ->name('visit.letter.regenerate');

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

        // Testing request letter management
        Route::get('/testing/{testingRequest}/letter', [RequestManagementController::class, 'getTestingRequestLetter'])
            ->name('testing.letter');
        Route::post('/testing/{testingRequest}/letter/regenerate', [RequestManagementController::class, 'regenerateTestingRequestLetter'])
            ->name('testing.letter.regenerate');
    });

    // Equipment management
    Route::prefix('equipment')->name('equipment.')->group(function () {
        // Equipment categories (must come before {equipment} route to avoid conflicts)
        Route::get('/categories', [EquipmentManagementController::class, 'categories'])
            ->name('categories.index');
        Route::post('/categories', [EquipmentManagementController::class, 'storeCategory'])
            ->name('categories.store');
        Route::put('/categories/{category}', [EquipmentManagementController::class, 'updateCategory'])
            ->name('categories.update');
        Route::delete('/categories/{category}', [EquipmentManagementController::class, 'destroyCategory'])
            ->name('categories.destroy');

        // Equipment summary statistics
        Route::get('/summary', [EquipmentManagementController::class, 'summary'])
            ->name('summary');
        
        // Equipment CRUD (specific routes come after more general ones)
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

        // Gallery
        // Gallery specific routes (must come before parameterized routes)
        Route::put('/gallery/reorder', [ContentManagementController::class, 'reorderGallery'])
            ->name('gallery.reorder');
        Route::get('/gallery/featured-slots', [ContentManagementController::class, 'getFeaturedSlots'])
            ->name('gallery.featured-slots.get');
        Route::put('/gallery/featured-slots', [ContentManagementController::class, 'updateFeaturedSlots'])
            ->name('gallery.featured-slots.update');
        
        // Gallery CRUD routes
        Route::get('/gallery', [ContentManagementController::class, 'gallery'])
            ->name('gallery.index');
        Route::post('/gallery', [ContentManagementController::class, 'storeGallery'])
            ->name('gallery.store');
        Route::get('/gallery/{gallery}', [ContentManagementController::class, 'showGallery'])
            ->name('gallery.show');
        Route::put('/gallery/{gallery}', [ContentManagementController::class, 'updateGallery'])
            ->name('gallery.update');
        Route::delete('/gallery/{gallery}', [ContentManagementController::class, 'destroyGallery'])
            ->name('gallery.destroy');
    });

    // Reports and analytics
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/equipment-usage', [ReportsController::class, 'equipmentUsage'])
            ->name('equipment-usage');
        Route::get('/request-analytics', [ReportsController::class, 'requestAnalytics'])
            ->name('request-analytics');
    });


    // Calendar management
    Route::prefix('calendar')->name('calendar.')->group(function () {
        // Day view - get all time slots for a specific date
        Route::get('/day/{date}', [\App\Http\Controllers\Api\Admin\CalendarController::class, 'dayView'])
            ->name('day-view');

        // Month view - get monthly summary of availability
        Route::get('/month/{year}/{month}', [\App\Http\Controllers\Api\Admin\CalendarController::class, 'monthView'])
            ->name('month-view');

        // Block time slots
        Route::post('/block', [\App\Http\Controllers\Api\Admin\CalendarController::class, 'blockSlot'])
            ->name('block-slot');

        // Unblock single time slot
        Route::delete('/unblock/{blockedTimeSlot}', [\App\Http\Controllers\Api\Admin\CalendarController::class, 'unblockSlot'])
            ->name('unblock-slot');

        // Bulk unblock multiple time slots
        Route::delete('/bulk-unblock', [\App\Http\Controllers\Api\Admin\CalendarController::class, 'bulkUnblock'])
            ->name('bulk-unblock');

        // List all blocked slots with filtering
        Route::get('/blocked-slots', [\App\Http\Controllers\Api\Admin\CalendarController::class, 'blockedSlotsList'])
            ->name('blocked-slots-list');
    });

    // Visit schedule management - route aliases for exact specification URLs
    Route::prefix('visit')->name('visit.')->group(function () {
        // Get hourly availability grid for a specific date
        Route::get('/availability', [\App\Http\Controllers\Api\Admin\CalendarController::class, 'getAvailability'])
            ->name('availability');

        // Get month overview with availability counts
        Route::get('/calendar', [\App\Http\Controllers\Api\Admin\CalendarController::class, 'getCalendar'])
            ->name('calendar');

        // Toggle block/unblock a single 1-hour time slot
        Route::put('/blocks/toggle', [\App\Http\Controllers\Api\Admin\CalendarController::class, 'toggleBlock'])
            ->name('blocks.toggle');
    });

    // Profile management for authenticated admin users
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', function (Request $request) {
            return response()->json([
                'success' => true,
                'data' => [
                    'user' => $request->user()->load('roles:id,name'),
                ],
            ]);
        })->name('show');

        Route::put('/', function (Request $request) {
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email,' . $request->user()->id,
                'current_password' => 'nullable|string|min:8',
                'password' => 'nullable|string|min:8|confirmed',
            ]);

            $user = $request->user();

            // Check current password if changing password
            if ($request->filled('password')) {
                if (!$request->filled('current_password') || !Hash::check($request->current_password, $user->password)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Current password is incorrect',
                        'errors' => ['current_password' => ['The current password is incorrect.']]
                    ], 422);
                }
                $user->password = Hash::make($request->password);
            }

            $user->name = $request->name;
            $user->email = $request->email;
            $user->save();

            return response()->json([
                'success' => true,
                'message' => 'Profile updated successfully',
                'data' => [
                    'user' => $user->load('roles:id,name'),
                ],
            ]);
        })->name('update');

        Route::post('/change-password', function (Request $request) {
            $request->validate([
                'current_password' => 'required|string',
                'password' => 'required|string|min:8|confirmed',
            ]);

            $user = $request->user();

            if (!Hash::check($request->current_password, $user->password)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Current password is incorrect',
                    'errors' => ['current_password' => ['The current password is incorrect.']]
                ], 422);
            }

            $user->password = Hash::make($request->password);
            $user->save();

            return response()->json([
                'success' => true,
                'message' => 'Password changed successfully',
            ]);
        })->name('change-password');
    });

    // WhatsApp communication
    Route::prefix('whatsapp')->name('whatsapp.')->group(function () {
        // Get admin templates for messaging users
        Route::get('/templates', [\App\Http\Controllers\Api\WhatsAppController::class, 'adminTemplates'])
            ->name('templates');

        // Generate WhatsApp link for admin to message user
        Route::post('/generate-link', [\App\Http\Controllers\Api\WhatsAppController::class, 'adminGenerateLink'])
            ->name('generate-link');
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
