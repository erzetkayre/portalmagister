<?php

use App\Http\Controllers\PWK\Admin;
use App\Http\Controllers\PWK\Dosen;
use App\Http\Controllers\PWK\Mahasiswa;
use Illuminate\Support\Facades\Route;

// Lite System Routes

Route::middleware(['auth', 'firstlogin','program:pwk'])->prefix('pwk')->name('pwk.')->group(function () {
    Route::get('/cek', [Admin\UserManagementController::class, 'index'])->name('dashboard');
    Route::middleware('can:admin')->prefix('admin')->name('admin.')->group(function () {
            Route::get('/cek', [Admin\AdminManagementController::class, 'index'])->name('dashboard');
        });
        Route::middleware('can:dosen')->prefix('dosen')->name('dosen.')->group(function () {
            Route::get('/cek', [Dosen\AdminManagementController::class, 'index'])->name('dashboard');
        });
        Route::middleware('can:mahasiswa')->prefix('mahasiswa')->name('mahasiswa.')->group(function () {
            Route::get('/cek', [Mahasiswa\AdminManagementController::class, 'index'])->name('dashboard');
        });
});
