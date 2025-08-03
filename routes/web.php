<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

// Public pages routes
Route::get('/', function () {
    return view('public.landing');
})->name('home');

Route::get('/staff', function () {
    return view('public.staff');
})->name('staff');

Route::get('/artikel', function () {
    return view('public.artikel');
})->name('artikel');

Route::get('/fasilitas', function () {
    return view('public.fasilitas');
})->name('fasilitas');

// Layanan routes
Route::prefix('layanan')->name('layanan.')->group(function () {
    // Redirect ke home karena layanan berbasis dropdown
    Route::get('/', function () {
        return redirect()->route('home');
    })->name('index');
    
    // Peminjaman alat menuju ke katalog alat
    Route::get('/peminjaman-alat', function () {
        return view('public.layanan.katalog-alat');
    })->name('peminjaman-alat');
    
    Route::get('/kunjungan', function () {
        return view('public.layanan.kunjungan');
    })->name('kunjungan');
    
    Route::get('/pengujian', function () {
        return view('public.layanan.pengujian');
    })->name('pengujian');
});

// Authenticated routes
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->middleware('verified')->name('dashboard');
    
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // Test route for development
    Route::get('/test', function () {
        return view('public.test');
    });
});

require __DIR__.'/auth.php';