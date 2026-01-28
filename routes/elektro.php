<?php

use App\Http\Controllers\Elektro\Admin;;
use App\Http\Controllers\Elektro\Dosen;
use App\Http\Controllers\Elektro\Mahasiswa;
use Illuminate\Support\Facades\Route;

// Lite System Routes

Route::middleware(['auth', 'firstlogin','program:elektro'])->prefix('elektro')->name('elektro.')->group(function () {
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
