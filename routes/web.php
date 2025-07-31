<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Routing\Route as RoutingRoute;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('public.landing');
});

// Public pages routes
Route::get('/staff', function () {
    return view('public.staff');
})->name('staff');

Route::get('/artikel', function () {
    return view('public.artikel');
})->name('artikel');

Route::get('/layanan', function () {
    return view('public.layanan');
})->name('layanan');

Route::get('/fasilitas', function () {
    return view('public.fasilitas');
})->name('fasilitas');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/dashboard', function () {
        return view('dashboard');
    })->middleware(['auth', 'verified'])->name('dashboard');

    Route::get('/test', function () {
        return view('public.test');
    });
});

require __DIR__.'/auth.php';
