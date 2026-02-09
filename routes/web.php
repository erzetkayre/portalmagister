<?php

use Inertia\Inertia;
use App\Http\Controllers\Auth;
use App\Http\Controllers\Admin;
use App\Http\Controllers\Dosen;
use App\Http\Controllers\Shared;
use App\Http\Controllers\Mahasiswa;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Koordinator;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CekController;

Route::get('/', function () {return Inertia::render('Welcome');})->name('home');

// Route::get('/', [CekController::class,'index'])->name('home');

Route::middleware('guest')->group(function () {
    Route::get('login', [Auth\LoginController::class, 'create'])->name('login');
    Route::post('login', [Auth\LoginController::class, 'store']);
});

Route::middleware(['auth','firstlogin'])->group(function () {
    // First Login Password Change
    Route::get('change-password', [Auth\FirstLoginController::class, 'create'])->name('create.first.login');
    Route::post('change-password', [Auth\FirstLoginController::class, 'update'])->name('post.first.login');

    // Dashboard Route
    Route::get('dashboard',[Shared\DashboardController::class,'index'])->name('dashboard');
    Route::post('logout', [Auth\LoginController::class, 'destroy'])->name('logout');

     // Profile Routes
    Route::get('pengaturan/profil', [Auth\ProfileController::class, 'edit'])->name('profile.edit');
    Route::post('pengaturan/profil', [Auth\ProfileController::class, 'updateProfile'])->name('profile.update');
    Route::post('pengaturan/password', [Auth\ProfileController::class, 'updatePassword'])->name('password.update');
    Route::post('pengaturan/appearance', [Auth\ProfileController::class, 'updateAppearance'])->name('appearance.update');
    // Route::get('foto-profil/{userId}', [Auth\ProfilePhotoController::class, 'show'])->name('profile.photo.show');
    // Route::get('foto-profil', [Auth\ProfilePhotoController::class, 'current'])->name('profile.photo.current');
    Route::delete('foto-profil', [Auth\ProfileController::class, 'deletePhoto'])->name('profile.photo.delete');

    // Admin Routes
    Route::middleware('can:admin')->prefix('admin')->name('admin.')->group(function () {
        // Users Management
        Route::prefix('users')->name('users.')->group(function(){
            Route::get('/', [Shared\Admin\UserManagementController::class, 'index'])->name('index');
            Route::get('/create', [Shared\Admin\UserManagementController::class, 'create'])->name('create');
            Route::post('/', [Shared\Admin\UserManagementController::class, 'store'])->name('store');
            Route::get('/{id}', [Shared\Admin\UserManagementController::class, 'show'])->name('show');
            Route::get('/edit/{id}', [Shared\Admin\UserManagementController::class, 'edit'])->name('edit');
            Route::put('/{id}', [Shared\Admin\UserManagementController::class, 'update'])->name('update');
            Route::delete('/{id}', [Shared\Admin\UserManagementController::class, 'destroy'])->name('delete');
            Route::post('/{id}/reset-password', [Shared\Admin\UserManagementController::class, 'resetPassword'])->name('reset.password');
            Route::post('/import', [Shared\Admin\UserManagementController::class, 'import'])->name('import');
        });

        // Students Management
        Route::prefix('mahasiswa')->name('mahasiswa.')->group(function(){
            Route::get('/',[]);
        });

        // Lecturers Management
        Route::prefix('dosen')->name('dosen.')->group(function(){
            Route::get('/',[]);
        });

        // Classes Management
        Route::prefix('ruang')->name('ruang.')->group(function(){
            Route::get('/',[]);
        });

        // Courses Management
        Route::prefix('mk')->name('mk.')->group(function(){
            Route::get('/',[]);
        });

        // Positions Management
        Route::prefix('jabatan')->name('jabatan.')->group(function(){
            Route::get('/',[]);
        });
    });


    //     Route::prefix('draft')->name('draft.')->group(function(){
    //         Route::get('',[Admin\DraftPratesisController::class,'index'])->name('index');
    //         Route::get('/{id}',[Admin\DraftPratesisController::class,'show'])->name('show');
    //         Route::post('/{id}',[Admin\DraftPratesisController::class,'update'])->name('update');
    //     });
    //     Route::prefix('sempro')->name('sempro.')->group(function(){
    //         Route::get('',[Admin\SeminarProposalController::class,'index'])->name('index');
    //         Route::get('/{id}',[Admin\SeminarProposalController::class,'show'])->name('show');
    //         Route::post('/{id}',[Admin\SeminarProposalController::class,'update'])->name('update');
    //         Route::post('/{id}/approve', [Admin\SeminarProposalController::class, 'approve'])->name('approve');
    //         Route::post('/{id}/reject', [Admin\SeminarProposalController::class, 'reject'])->name('reject');
    //         Route::post('/{id}/approve-schedule', [Admin\SeminarProposalController::class, 'approveSchedule'])->name('approve-schedule');
    //         Route::get('/{id}/download-invitation', [Admin\SeminarProposalController::class, 'downloadInvitation'])->name('download-invitation');
    //     });

    // });

    // // Route Mahasiswa
    // Route::name('mahasiswa.')->middleware('role:mahasiswa')->group(function () {
    //     Route::get('dashboard', [Mahasiswa\DashboardController::class, 'index'])->name('dashboard');

    //     Route::prefix('draft')->name('draft.')->group(function(){
    //         Route::get('/', [Mahasiswa\DraftPratesisController::class, 'index'])->name('index');
    //         Route::get('/pengajuan', [Mahasiswa\DraftPratesisController::class, 'create'])->name('create');
    //         Route::post('/', [Mahasiswa\DraftPratesisController::class, 'store'])->name('store');
    //         Route::get('/{id}', [Mahasiswa\DraftPratesisController::class, 'show'])->name('show');

    //         // Route baru untuk template surat dan upload
    //         Route::get('/{id}/template-surat', [Mahasiswa\DraftPratesisController::class, 'generateSuratPermohonan'])->name('template-surat');
    //         Route::post('/{id}/upload-surat', [Mahasiswa\DraftPratesisController::class, 'uploadSuratPermohonan'])->name('upload-surat');
    //     });

    //     Route::prefix('pratesis')->name('sempro.')->group(function(){
    //        Route::get('/', [Mahasiswa\SeminarProposalController::class, 'index'])->name('index');
    //         Route::post('/', [Mahasiswa\SeminarProposalController::class, 'store'])->name('store');
    //         Route::get('/{id}', [Mahasiswa\SeminarProposalController::class, 'show'])->name('show');

    //         // Upload surat kelayakan
    //         Route::post('/{id}/upload-surat', [Mahasiswa\SeminarProposalController::class, 'uploadSuratKelayakan'])->name('upload-surat');

    //         // Download template surat
    //         Route::get('/{id}/template-surat', [Mahasiswa\SeminarProposalController::class, 'generateSuratKelayak   an'])->name('template-surat');

    //         // Update jadwal
    //         Route::post('/{id}/update-jadwal', [Mahasiswa\SeminarProposalController::class, 'updateJadwal'])->name('update-jadwal');
    //     });


    // });


});


// require __DIR__.'/auth.php';
// require __DIR__.'/elektro.php';
// require __DIR__.'/pwk.php';
