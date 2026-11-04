<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SiswaController;
use App\Http\Controllers\KelasController;
use App\Http\Controllers\RfidController;
use App\Http\Controllers\AbsensiController;
use App\Http\Controllers\LaporanAbsensiController;
use App\Http\Controllers\WaliMuridController;
use App\Http\Controllers\WaliMuridAbsensiController;
use App\Http\Controllers\WaliMuridPengajuanIzinController;
use App\Http\Controllers\OperatorPengajuanIzinController;
use App\Http\Controllers\UserController;

// Halaman awal mengarahkan pengguna ke dashboard.
Route::get('/', function () {
    return redirect()->route('dashboard');
});

// Login hanya untuk pengguna yang belum login.
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])
        ->name('login');

    Route::post('/login', [AuthController::class, 'login'])
        ->name('login.process');
});

// Semua rute di dalam grup auth memerlukan login.
Route::middleware('auth')->group(function () {

    // Logout.
    Route::post('/logout', [AuthController::class, 'logout'])
        ->name('logout');

    // Dashboard dapat diakses operator dan guru yang sudah login.
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');

    // Operator dan guru boleh melihat daftar siswa dan kelas.
    Route::middleware('role:operator,guru')->group(function () {
        Route::get('/siswa', [SiswaController::class, 'index'])
            ->name('siswa.index');

        Route::get('/kelas', [KelasController::class, 'index'])
            ->name('kelas.index');

    Route::middleware('role:operator,guru')->group(function () {
        Route::get('/laporan-absensi', [
            LaporanAbsensiController::class,
            'index',
        ])->name('laporan.absensi');
        });
    });

    // Hanya operator yang boleh mengelola data siswa, kelas, dan RFID.
    Route::middleware('role:operator')->group(function () {
        Route::resource('siswa', SiswaController::class)
            ->except(['index', 'show']);

        Route::resource('kelas', KelasController::class)
            ->except(['index', 'show'])
            ->parameters(['kelas' => 'kela']);

        Route::resource('rfid', RfidController::class);

        // Halaman scan RFID hanya bisa dibuka operator.
        Route::view('/absensi', 'absensi.scan')
            ->name('absensi.scan');

        Route::get('/operator/pengajuan-izin', [
            OperatorPengajuanIzinController::class,
            'index',
        ])->name('operator.pengajuan.index');

        Route::put('/operator/pengajuan-izin/{pengajuan}/verifikasi', [
            OperatorPengajuanIzinController::class,
            'verifikasi',
        ])->name('operator.pengajuan.verifikasi');

        Route::get('/operator/pengajuan-izin/{pengajuan}/dokumen', [
            OperatorPengajuanIzinController::class,
            'downloadDokumen',
        ])->name('operator.pengajuan.dokumen');

        Route::resource('operator/users', UserController::class)
            ->except(['show'])
            ->names('operator.users');
    });
});

// Operator dan guru boleh melihat daftar absensi.
Route::middleware('role:operator,guru')->group(function () {
    Route::get('/data-absensi', [AbsensiController::class, 'index'])
        ->name('absensi.index');
});

// Hanya operator yang boleh mengoreksi absensi.
Route::middleware('role:operator')->group(function () {
    Route::get('/data-absensi/{absensi}/edit', [
        AbsensiController::class,
        'edit',
    ])->name('absensi.edit');

    Route::put('/data-absensi/{absensi}', [
        AbsensiController::class,
        'update',
    ])->name('absensi.update');
});

Route::middleware(['auth', 'wali_murid'])->group(function () {
    Route::get('/wali-murid/dashboard', [
        WaliMuridController::class,
        'dashboard'
    ])->name('wali.dashboard');

    Route::get('/wali-murid/absensi', [
        WaliMuridAbsensiController::class,
        'index'
    ])->name('wali.absensi.index');

    Route::get('/wali-murid/pengajuan', [
        WaliMuridPengajuanIzinController::class,
        'index',
    ])->name('wali.pengajuan.index');

    Route::get('/wali-murid/pengajuan/buat', [
        WaliMuridPengajuanIzinController::class,
        'create',
    ])->name('wali.pengajuan.create');

    Route::post('/wali-murid/pengajuan', [
        WaliMuridPengajuanIzinController::class,
        'store',
    ])->name('wali.pengajuan.store');
});
