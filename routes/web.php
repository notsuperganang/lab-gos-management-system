<?php

use App\Http\Controllers\PublicPageController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

// Public pages routes
Route::get('/', [PublicPageController::class, 'landing'])->name('home');

Route::get('/staff', [PublicPageController::class, 'staff'])->name('staff');

Route::get('/artikel', [PublicPageController::class, 'articles'])->name('artikel');
Route::get('/artikel/{article:slug}', [PublicPageController::class, 'showArticle'])->name('artikel.show');

Route::get('/galeri', [PublicPageController::class, 'gallery'])->name('galeri');

// Tracking routes
Route::prefix('tracking')->name('tracking.')->group(function () {
    Route::get('/peminjaman-alat', function () {
        return view('public.tracking.peminjaman-alat');
    })->name('peminjaman-alat');

    Route::get('/kunjungan', function () {
        return view('public.tracking.kunjungan');
    })->name('kunjungan');

    Route::get('/pengujian', function () {
        return view('public.tracking.pengujian');
    })->name('pengujian');
});

// Layanan routes
Route::prefix('layanan')->name('layanan.')->group(function () {
    // Redirect ke home karena layanan berbasis dropdown
    Route::get('/', function () {
        return redirect()->route('home');
    })->name('index');

    Route::get('/peminjaman-alat/form', [PublicPageController::class, 'borrowForm'])->name('form-peminjaman');

    Route::get('/peminjaman-alat/tracking/{requestId}', function ($requestId) {
        return view('public.layanan.tracking-peminjaman', ['requestId' => $requestId]);
    })->name('peminjaman-alat.tracking');

    // Tracking detail page (accepts request ID as query parameter)
    Route::get('/tracking-peminjaman', function () {
        $requestId = request()->get('rid');
        return view('public.layanan.tracking-peminjaman', ['requestId' => $requestId]);
    })->name('tracking-peminjaman');

    // Peminjaman alat menuju ke katalog alat
    Route::get('/peminjaman-alat', [PublicPageController::class, 'equipmentCatalog'])->name('peminjaman-alat');

    Route::get('/kunjungan', [PublicPageController::class, 'visitForm'])->name('kunjungan');

    // Kunjungan tracking route (with dynamic visit ID)
    Route::get('/kunjungan/confirmation/{visitId}', function ($visitId) {
        return view('public.layanan.tracking-kunjungan', ['visitId' => $visitId]);
    })->name('kunjungan.confirmation');

    Route::get('/pengujian', [PublicPageController::class, 'testingService'])->name('pengujian');

    // Testing tracking route (with dynamic testing ID)
    Route::get('/testing/confirmation/{testingId}', function ($testingId) {
        return view('public.layanan.tracking-pengujian', ['testingId' => $testingId]);
    })->name('testing.confirmation');

    // Testing tracking detail page (accepts request ID as query parameter)
    Route::get('/tracking-pengujian', function () {
        $requestId = request()->get('rid');
        return view('public.layanan.tracking-pengujian', ['requestId' => $requestId]);
    })->name('tracking-pengujian');
});

/*
|--------------------------------------------------------------------------
| Admin Views (Sanctum-only Authentication)
|--------------------------------------------------------------------------
| Admin web routes for Blade views that use Sanctum API authentication
| These routes serve the admin interface but data loading is API-driven
*/

// Admin login page
Route::get('/admin/login', function () {
    return view('admin.login-sanctum');
})->name('admin.login');

// Admin routes group (views only - data comes from API)
Route::prefix('admin')->name('admin.')->group(function () {

    // Main Dashboard
    Route::get('/', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard.index');

    // PROFIL DAN PUBLIKASI (Profile & Publications)
    Route::prefix('site-settings')->name('site-settings.')->group(function () {
        Route::get('/', [AdminController::class, 'siteSettings'])->name('index');
        Route::get('/edit', [AdminController::class, 'editSiteSettings'])->name('edit');
        Route::put('/update', [AdminController::class, 'updateSiteSettings'])->name('update');
    });

    Route::prefix('staff')->name('staff.')->group(function () {
        Route::get('/', [AdminController::class, 'staffIndex'])->name('index');
        Route::get('/create', [AdminController::class, 'staffCreate'])->name('create');
        Route::post('/', [AdminController::class, 'staffStore'])->name('store');
        Route::get('/{staff}', [AdminController::class, 'staffShow'])->name('show');
        Route::get('/{staff}/edit', [AdminController::class, 'staffEdit'])->name('edit');
        Route::put('/{staff}', [AdminController::class, 'staffUpdate'])->name('update');
        Route::delete('/{staff}', [AdminController::class, 'staffDestroy'])->name('destroy');
    });

    Route::prefix('articles')->name('articles.')->group(function () {
        Route::get('/', [AdminController::class, 'articlesIndex'])->name('index');
        Route::get('/create', [AdminController::class, 'articlesCreate'])->name('create');
        Route::post('/', [AdminController::class, 'articlesStore'])->name('store');
        Route::get('/{article}', [AdminController::class, 'articlesShow'])->name('show');
        Route::get('/{article}/edit', [AdminController::class, 'articlesEdit'])->name('edit');
        Route::put('/{article}', [AdminController::class, 'articlesUpdate'])->name('update');
        Route::delete('/{article}', [AdminController::class, 'articlesDestroy'])->name('destroy');
    });

    Route::prefix('gallery')->name('gallery.')->group(function () {
        Route::get('/', [AdminController::class, 'galleryIndex'])->name('index');
        Route::get('/create', [AdminController::class, 'galleryCreate'])->name('create');
        Route::post('/', [AdminController::class, 'galleryStore'])->name('store');
        Route::get('/{gallery}', [AdminController::class, 'galleryShow'])->name('show');
        Route::get('/{gallery}/edit', [AdminController::class, 'galleryEdit'])->name('edit');
        Route::put('/{gallery}', [AdminController::class, 'galleryUpdate'])->name('update');
        Route::delete('/{gallery}', [AdminController::class, 'galleryDestroy'])->name('destroy');
    });

    // SARANA DAN PENJADWALAN (Facilities & Scheduling)
    Route::prefix('equipment')->name('equipment.')->group(function () {
        Route::get('/', [AdminController::class, 'equipmentIndex'])->name('index');
        Route::get('/create', [AdminController::class, 'equipmentCreate'])->name('create');
        Route::post('/', [AdminController::class, 'equipmentStore'])->name('store');
        Route::get('/{equipment}', [AdminController::class, 'equipmentShow'])->name('show');
        Route::get('/{equipment}/edit', [AdminController::class, 'equipmentEdit'])->name('edit');
        Route::put('/{equipment}', [AdminController::class, 'equipmentUpdate'])->name('update');
        Route::delete('/{equipment}', [AdminController::class, 'equipmentDestroy'])->name('destroy');
    });

    Route::prefix('visit-schedule')->name('visit-schedule.')->group(function () {
        Route::get('/', [AdminController::class, 'visitScheduleIndex'])->name('index');
        Route::get('/calendar', [AdminController::class, 'visitScheduleCalendar'])->name('calendar');
        Route::post('/block-time', [AdminController::class, 'blockTimeSlot'])->name('block-time');
        Route::delete('/unblock-time/{id}', [AdminController::class, 'unblockTimeSlot'])->name('unblock-time');
    });

    // LAYANAN LABORATORIUM (Laboratory Services)
    Route::prefix('borrowing')->name('borrowing.')->group(function () {
        Route::get('/', [AdminController::class, 'borrowingIndex'])->name('index');
        Route::get('/{request}', [AdminController::class, 'borrowingShow'])->name('show');
        Route::put('/{request}/approve', [AdminController::class, 'borrowingApprove'])->name('approve');
        Route::put('/{request}/reject', [AdminController::class, 'borrowingReject'])->name('reject');
        Route::put('/{request}/complete', [AdminController::class, 'borrowingComplete'])->name('complete');
    });

    Route::prefix('visits')->name('visits.')->group(function () {
        Route::get('/', [AdminController::class, 'visitsIndex'])->name('index');
        Route::get('/{request}', [AdminController::class, 'visitsShow'])->name('show');
        Route::put('/{request}/approve', [AdminController::class, 'visitsApprove'])->name('approve');
        Route::put('/{request}/reject', [AdminController::class, 'visitsReject'])->name('reject');
        Route::put('/{request}/complete', [AdminController::class, 'visitsComplete'])->name('complete');
    });

    Route::prefix('testing')->name('testing.')->group(function () {
        Route::get('/', [AdminController::class, 'testingIndex'])->name('index');
        Route::get('/{request}', [AdminController::class, 'testingShow'])->name('show');
        Route::put('/{request}/approve', [AdminController::class, 'testingApprove'])->name('approve');
        Route::put('/{request}/reject', [AdminController::class, 'testingReject'])->name('reject');
        Route::put('/{request}/complete', [AdminController::class, 'testingComplete'])->name('complete');
    });

    // Profile Settings
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/settings', [AdminController::class, 'profileSettings'])->name('settings');
    });

    // User Management (for Super Admin)
    Route::prefix('users')->name('users.')->group(function () {
        Route::get('/', [AdminController::class, 'usersIndex'])->name('index');
    });
});

/*
|--------------------------------------------------------------------------
| Super Admin Views (Sanctum-only Authentication)
|--------------------------------------------------------------------------
| Super admin web routes for Blade views that use Sanctum API authentication
| These routes serve the super admin interface but data loading is API-driven
*/

// Super Admin routes group (views only - data comes from API)
Route::prefix('superadmin')->name('superadmin.')->group(function () {

    // User Management (API-driven with modals)
    Route::prefix('users')->name('users.')->group(function () {
        Route::get('/', [App\Http\Controllers\SuperAdmin\SuperAdminController::class, 'usersIndex'])->name('index');
        // All other operations (create, edit, update, delete) are handled via API endpoints
    });
});

