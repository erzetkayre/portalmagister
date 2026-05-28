<?php

use App\Http\Controllers\PWK\Mahasiswa;
use App\Http\Controllers\PWK\Dosen;
use App\Http\Controllers\PWK\Koordinator;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'firstlogin', 'program:pwk'])
    ->prefix('pwk')
    ->name('pwk.')
    ->group(function () {

    // ─── Mahasiswa ────────────────────────────────────────────────────────────
    Route::middleware('can:mahasiswa')->prefix('mahasiswa')->name('mahasiswa.')->group(function () {

        Route::prefix('tesis')->name('tesis.')->group(function () {
            Route::get('/',          [Mahasiswa\TesisController::class,       'index'])->name('index');
            Route::get('/daftar',    [Mahasiswa\PendaftaranController::class, 'create'])->name('daftar');
            Route::post('/daftar',   [Mahasiswa\PendaftaranController::class, 'store'])->name('daftar.store');
            Route::get('/dokumen',   [Mahasiswa\PendaftaranController::class, 'dokumen'])->name('dokumen');
            Route::post('/dokumen',  [Mahasiswa\PendaftaranController::class, 'storeDokumen'])->name('dokumen.store');
        });

        Route::prefix('logbook')->name('logbook.')->group(function () {
            Route::get('/',          [Mahasiswa\LogbookController::class, 'index'])->name('index');
            Route::get('/tambah',    [Mahasiswa\LogbookController::class, 'create'])->name('create');
            Route::post('/',         [Mahasiswa\LogbookController::class, 'store'])->name('store');
        });

        Route::get('/ujian/{jenis}', [Mahasiswa\UjianController::class, 'show'])->name('ujian.show');
        Route::post('/ujian/{jenis}',[Mahasiswa\UjianController::class, 'store'])->name('ujian.store');
    });

    // ─── Dosen ────────────────────────────────────────────────────────────────
    Route::middleware('can:dosen')->prefix('dosen')->name('dosen.')->group(function () {
        Route::get('/akademik',      [Dosen\AkademikController::class,   'index'])->name('akademik.index');
        Route::get('/bimbingan',     [Dosen\BimbinganController::class,  'index'])->name('bimbingan.index');
        Route::get('/bimbingan/{id}',[Dosen\BimbinganController::class,  'show'])->name('bimbingan.show');
        Route::get('/ujian/{jenis}', [Dosen\UjianController::class,      'index'])->name('ujian.show');
    });

    // ─── Koordinator ──────────────────────────────────────────────────────────
    Route::middleware('can:koordinator')->prefix('koordinator')->name('koordinator.')->group(function () {

        Route::prefix('tesis')->name('tesis.')->group(function () {
            Route::get('/pengajuan',              [Koordinator\TesisPengajuanController::class, 'index'])->name('pengajuan.index');
            Route::get('/pengajuan/{id}',         [Koordinator\TesisPengajuanController::class, 'show'])->name('pengajuan.show');
            Route::post('/pengajuan/{id}/setuju', [Koordinator\TesisPengajuanController::class, 'setuju'])->name('pengajuan.setuju');
            Route::post('/pengajuan/{id}/tolak',  [Koordinator\TesisPengajuanController::class, 'tolak'])->name('pengajuan.tolak');
            Route::get('/export',                 [Koordinator\TesisDaftarController::class,    'export'])->name('export');
            Route::get('/',                       [Koordinator\TesisDaftarController::class,    'index'])->name('index');
            Route::get('/{id}',                   [Koordinator\TesisDaftarController::class,    'show'])->name('show');
        });

        Route::prefix('logbook')->name('logbook.')->group(function () {
            Route::get('/',          [Koordinator\LogbookController::class, 'index'])->name('index');
            Route::get('/{tesisId}', [Koordinator\LogbookController::class, 'show'])->name('show');
        });

        Route::prefix('ujian')->name('ujian.')->group(function () {
            Route::get('/{jenis}/pengajuan',          [Koordinator\UjianController::class, 'pengajuan'])->name('pengajuan');
            Route::get('/{jenis}/daftar',             [Koordinator\UjianController::class, 'daftar'])->name('daftar');
            Route::post('/{jenis}/{id}/jadwal',       [Koordinator\UjianController::class, 'jadwal'])->name('jadwal');
            Route::post('/{jenis}/{id}/hasil',        [Koordinator\UjianController::class, 'hasil'])->name('hasil');
            Route::post('/{jenis}/{id}/lulus-revisi', [Koordinator\UjianController::class, 'lulusRevisi'])->name('lulus.revisi');
        });

        Route::prefix('perubahan-judul')->name('perubahan.judul.')->group(function () {
            Route::get('/',          [Koordinator\PerubahanJudulController::class, 'index'])->name('index');
            Route::get('/{id}',      [Koordinator\PerubahanJudulController::class, 'show'])->name('show');
            Route::post('/{id}/proses', [Koordinator\PerubahanJudulController::class, 'proses'])->name('proses');
        });
    });
});
