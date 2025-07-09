<?php

use Inertia\Inertia;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Mahasiswa;
use App\Http\Controllers\Admin;
use App\Http\Controllers\Koordinator;
use App\Http\Controllers\Dosen;
use App\Http\Controllers\Auth;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

// Route::get('dashboard', function () {
//     return Inertia::render('Dashboard');
// })->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('guest')->group(function () {
    Route::get('login', [Auth\AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('login', [Auth\AuthenticatedSessionController::class, 'store']);
});

Route::middleware(['auth', 'firstlogin'])->group(function () {
    Route::get('change-password', [Auth\FirstLoginController::class, 'index'])->name('index.first.login');
    Route::post('change-password', [Auth\FirstLoginController::class, 'update'])->name('post.first.login');

    Route::post('logout', [Auth\AuthenticatedSessionController::class, 'destroy'])->name('logout');

    // Route Admin
    Route::prefix('admin')->name('admin.')->middleware('role:admin')->group(function () {
        Route::get('dashboard', [Admin\DashboardController::class, 'index'])->name('dashboard');

        // Management Users
        Route::prefix('user')->name('users.')->group( function() {
            Route::get('/', [Admin\Managements\UsersController::class, 'index'])->name('index');
            Route::get('/create', [Admin\Managements\UsersController::class, 'create'])->name('create');
            Route::post('/', [Admin\Managements\UsersController::class, 'store'])->name('store');
            Route::get('/{id}', [Admin\Managements\UsersController::class, 'show'])->name('show');
            Route::get('/{id}/edit', [Admin\Managements\UsersController::class, 'edit'])->name('edit');
            Route::put('/{id}', [Admin\Managements\UsersController::class, 'update'])->name('update');
            Route::delete('/{id}', [Admin\Managements\UsersController::class, 'destroy'])->name('destroy');
            Route::put('/{id}/reset-password', [Admin\Managements\UsersController::class, 'resetPassword'])->name('reset.password');
        });

        // Management Dosen
        Route::prefix('dosen')->name('dosen.')->group( function() {
            Route::get('/', [Admin\Managements\DosenController::class, 'index'])->name('index');
            Route::get('/create', [Admin\Managements\DosenController::class, 'create'])->name('create');
            Route::post('/', [Admin\Managements\DosenController::class, 'store'])->name('store');
            Route::get('/{id}', [Admin\Managements\DosenController::class, 'show'])->name('show');
            Route::get('/{id}/edit', [Admin\Managements\DosenController::class, 'edit'])->name('edit');
            Route::put('/{id}', [Admin\Managements\DosenController::class, 'update'])->name('update');
            Route::delete('/{id}', [Admin\Managements\DosenController::class, 'destroy'])->name('destroy');
            Route::post('/import', [Admin\Managements\DosenController::class, 'import'])->name('import');
        });

        // Management Mahasiswa
        Route::prefix('mahasiswa')->name('mahasiswa.')->group( function() {
            Route::get('/', [Admin\Managements\MahasiswaController::class, 'index'])->name('index');
            Route::get('/create', [Admin\Managements\MahasiswaController::class, 'create'])->name('create');
            Route::post('/', [Admin\Managements\MahasiswaController::class, 'store'])->name('store');
            Route::get('/{id}', [Admin\Managements\MahasiswaController::class, 'show'])->name('show');
            Route::get('/{id}/edit', [Admin\Managements\MahasiswaController::class, 'edit'])->name('edit');
            Route::put('/{id}', [Admin\Managements\MahasiswaController::class, 'update'])->name('update');
            Route::delete('/{id}', [Admin\Managements\MahasiswaController::class, 'destroy'])->name('destroy');
            Route::post('/import', [Admin\Managements\MahasiswaController::class, 'import'])->name('import');
        });

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
