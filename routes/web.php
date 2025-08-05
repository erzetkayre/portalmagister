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
            Route::put('/{id}', action: [Admin\Managements\MahasiswaController::class, 'update'])->name('update');
            Route::delete('/{id}', [Admin\Managements\MahasiswaController::class, 'destroy'])->name('destroy');
            Route::post('/import', [Admin\Managements\MahasiswaController::class, 'import'])->name('import');
            Route::get('/template/{filename}', [Admin\Managements\MahasiswaController::class, 'template'])->name('template');
        });

        Route::prefix('draft')->name('draft.')->group(function(){
            Route::get('',[Admin\DraftPratesisController::class,'index'])->name('index');
            Route::get('/{id}',[Admin\DraftPratesisController::class,'show'])->name('show');
            Route::post('/{id}',[Admin\DraftPratesisController::class,'update'])->name('update');
        });
    });



    // Route Koordinator
    Route::prefix('koordinator')->name('koordinator.')->middleware('role:koordinator')->group(function () {
        Route::get('dashboard', [Koordinator\DashboardController::class, 'index'])->name('dashboard');


    });



    // Route Dosen
    Route::prefix('dosen')->name('dosen.')->middleware('role:dosen')->group(function () {
        Route::get('dashboard', [Dosen\DashboardController::class, 'index'])->name('dashboard');

        Route::get('bimbingan',[Dosen\BimbinganController::class, 'index'])->name('bimbingan.index');
        // Route::get('uji',[Dosen\PengujiController::class, 'index'])->name('uji.index');


    });



    // Route Mahasiswa
    Route::name('mahasiswa.')->middleware('role:mahasiswa')->group(function () {
        Route::get('dashboard', [Mahasiswa\DashboardController::class, 'index'])->name('dashboard');

        Route::prefix('draft')->name('draft.')->group(function(){
            Route::get('/', [Mahasiswa\DraftPratesisController::class, 'index'])->name('index');
            Route::get('/pengajuan', [Mahasiswa\DraftPratesisController::class, 'create'])->name('create');
            Route::post('/', [Mahasiswa\DraftPratesisController::class, 'store'])->name('store');
            Route::get('/{id}', [Mahasiswa\DraftPratesisController::class, 'show'])->name('show');

            // Route baru untuk template surat dan upload
            Route::get('/{id}/template-surat', [Mahasiswa\DraftPratesisController::class, 'generateSuratPermohonan'])->name('template-surat');
            Route::post('/{id}/upload-surat', [Mahasiswa\DraftPratesisController::class, 'uploadSuratPermohonan'])->name('upload-surat');
        });

        Route::prefix('pratesis')->name('pratesis.')->group(function(){
            Route::get('/',[Mahasiswa\PratesisController::class,'index'])->name('index');
            Route::get('/{id}',[Mahasiswa\PratesisController::class,'edit'])->name('edit');
            Route::get('/{id}',[Mahasiswa\PratesisController::class,'update'])->name('update');
        });


    });

    // Profile Routes
    Route::get('pengaturan/profil', [Auth\ProfileController::class, 'edit'])->name('profile.edit');
    Route::post('pengaturan/profil', [Auth\ProfileController::class, 'updateProfile'])->name('profile.update');
    Route::post('pengaturan/password', [Auth\ProfileController::class, 'updatePassword'])->name('password.update');
    Route::post('pengaturan/appearance', [Auth\ProfileController::class, 'updateAppearance'])->name('appearance.update');
    Route::get('foto-profil/{userId}', [Auth\ProfilePhotoController::class, 'show'])->name('profile.photo.show');
    Route::get('foto-profil', [Auth\ProfilePhotoController::class, 'current'])->name('profile.photo.current');
    Route::delete('foto-profil', [Auth\ProfileController::class, 'deletePhoto'])->name('profile.photo.delete');
});


require __DIR__.'/auth.php';
