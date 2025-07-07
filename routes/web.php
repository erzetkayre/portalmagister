<?php

use Inertia\Inertia;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Mahasiswa;
use App\Http\Controllers\Admin;
use App\Http\Controllers\Koordinator;
use App\Http\Controllers\Dosen;
use App\Http\Controllers\Auth\AuthenticatedSessionController;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

// Route::get('dashboard', function () {
//     return Inertia::render('Dashboard');
// })->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('guest')->group(function () {
    Route::get('login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('login', [AuthenticatedSessionController::class, 'store']);
});

Route::middleware('auth')->group(function () {
    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])
        ->name('logout');

    // Route Admin
    Route::prefix('admin')->name('admin.')->middleware('role:admin')->group(function () {
        Route::get('dashboard', [Admin\DashboardController::class, 'index'])->name('dashboard');


    });

    // Route Koordinator
    Route::prefix('koordinator')->name('koordinator.')->middleware('role:koordinator')->group(function () {
        Route::get('dashboard', [Koordinator\DashboardController::class, 'index'])->name('dashboard');


    });

    // Route Dosen
    Route::prefix('dosen')->name('dosen.')->middleware('role:dosen')->group(function () {
        Route::get('dashboard', [Dosen\DashboardController::class, 'index'])->name('dashboard');


    });

    // Route Mahasiswa
    Route::name('mahasiswa.')->middleware('role:mahasiswa')->group(function () {
        Route::get('dashboard', [Mahasiswa\DashboardController::class, 'index'])->name('dashboard');


    });
});



require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
