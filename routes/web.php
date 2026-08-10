<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\SurveyController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DataMingguanController;
use App\Http\Controllers\GeoMapController;
use App\Http\Controllers\BabController;
use App\Http\Controllers\BapController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PenugasanController;
use App\Http\Controllers\PetugasController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\SettingController;

/*
|--------------------------------------------------------------------------
| Web Routes (Dinas PUPR Kabupaten Jember)
|--------------------------------------------------------------------------
*/

// ============================================================
// PUBLIC ROUTES (Dapat diakses langsung tanpa harus login)
// ============================================================

// Halaman Utama Publik / Landing Beranda (Tidak terpengaruh mode tema)
Route::get('/', [LandingController::class, 'index'])->name('landing');
Route::get('/landing', [LandingController::class, 'index']);

// Auth Routes (Login & Logout)
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::match(['get', 'post'], '/logout', [AuthController::class, 'logout'])->name('logout');

// Standalone Print Route (BAP PDF 4 Halaman F4)
Route::get('/cetak-bap/{id?}', [BabController::class, 'cetak'])->name('bap.cetak');
Route::get('/cetak_bab/{id?}', [BabController::class, 'cetak']);

// ============================================================
// PROTECTED ROUTES (Wajib Login Sebagai Admin/Petugas)
// ============================================================
Route::middleware(['auth'])->group(function () {
    // Form Survey Lapangan (Wajib Login)
    Route::get('/survey', [SurveyController::class, 'index'])->name('survey');
    Route::get('/survei', [SurveyController::class, 'index']);
    Route::post('/survey', [SurveyController::class, 'store']);
    Route::post('/survey/upload-photo', [SurveyController::class, 'uploadPhoto'])->name('survey.upload-photo');

    // Admin Dashboard System
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Data Mingguan
    Route::get('/data-mingguan', [DataMingguanController::class, 'index'])->name('data-mingguan');
    Route::get('/data_mingguan', [DataMingguanController::class, 'index']);
    Route::get('/data-mingguan/create', [DataMingguanController::class, 'create'])->name('data-mingguan.create');
    Route::post('/data-mingguan', [DataMingguanController::class, 'store'])->name('data-mingguan.store');
    Route::get('/data-mingguan/{dataMingguan}', [DataMingguanController::class, 'show'])->name('data-mingguan.show');
    Route::get('/data-mingguan/{dataMingguan}/edit', [DataMingguanController::class, 'edit'])->name('data-mingguan.edit');
    Route::put('/data-mingguan/{dataMingguan}', [DataMingguanController::class, 'update'])->name('data-mingguan.update');
    Route::delete('/data-mingguan/{dataMingguan}', [DataMingguanController::class, 'destroy'])->name('data-mingguan.destroy');

    // Geo Maps
    Route::get('/geomaps', [GeoMapController::class, 'index'])->name('geomaps');
    Route::get('/geoMaps', [GeoMapController::class, 'index']);

    // Berita Acara Pemeriksaan (BAP) — terhubung ke database
    Route::get('/bab', [BapController::class, 'index'])->name('bab');
    Route::post('/bab', [BapController::class, 'store'])->name('bap.store');
    Route::post('/bab/generate-all', [BapController::class, 'generateAll'])->name('bap.generate-all');
    Route::post('/bab/generate-from-kegiatan/{dataMingguan}', [BapController::class, 'generateFromKegiatan'])->name('bap.generate-from-kegiatan');
    Route::patch('/bab/{bap}/status', [BapController::class, 'updateStatus'])->name('bap.status');
    Route::delete('/bab/{bap}', [BapController::class, 'destroy'])->name('bap.destroy');

    // Penugasan & Petugas Survei Management — Khusus Admin
    Route::middleware(['role:admin'])->group(function () {
        // Penugasan Petugas Survei Lapangan
        Route::get('/penugasan', [PenugasanController::class, 'index'])->name('penugasan');
        Route::post('/penugasan/{dataMingguan}', [PenugasanController::class, 'update'])->name('penugasan.update');

        // User Management
        Route::get('/user', [UserController::class, 'index'])->name('user');
        Route::post('/user', [UserController::class, 'store'])->name('user.store');
        Route::put('/user/{id}', [UserController::class, 'update'])->name('user.update');
        Route::delete('/user/{id}', [UserController::class, 'destroy'])->name('user.destroy');
    });

    // Workspace Khusus Petugas Survei Lapangan
    Route::prefix('petugas')->name('petugas.')->group(function () {
        Route::get('/dashboard', [PetugasController::class, 'dashboard'])->name('dashboard');
        Route::get('/belum-survei', [PetugasController::class, 'belumSurvei'])->name('belum-survei');
        Route::get('/sudah-survei', [PetugasController::class, 'sudahSurvei'])->name('sudah-survei');
        Route::post('/update-location', [PetugasController::class, 'updateLocation'])->name('update-location');
    });

    // Rekapitulasi Laporan
    Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan');
    Route::get('/laporan/export', [LaporanController::class, 'exportExcel'])->name('laporan.export');
    Route::get('/laporan/cetak', [LaporanController::class, 'cetak'])->name('laporan.cetak');

    // Pengaturan System & Tema
    Route::get('/setting', [SettingController::class, 'index'])->name('setting');
});
