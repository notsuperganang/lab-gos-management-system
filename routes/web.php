<?php

use App\Http\Controllers\PublicPageController;
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

