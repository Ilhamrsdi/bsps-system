<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\SurveyController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\VervalDataController;
use App\Http\Controllers\GeoMapController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PenugasanController;
use App\Http\Controllers\PetugasController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\SettingController;

/*
|--------------------------------------------------------------------------
| Web Routes (BSPS Verval System)
|--------------------------------------------------------------------------
*/

// ============================================================
// PUBLIC ROUTES (Dapat diakses langsung tanpa harus login)
// ============================================================

// Halaman Utama Publik / Landing Beranda
Route::get('/', [LandingController::class, 'index'])->name('landing');
Route::get('/landing', [LandingController::class, 'index']);

// Storage Media Fallback Route (Bypass Nginx 403 Forbidden di Shared Hosting Hostinger)
Route::get('/storage/{path}', function ($path) {
    $possiblePaths = [
        storage_path('app/public/' . $path),
        public_path('storage/' . $path),
        base_path('storage/' . $path),
        base_path('../storage/app/public/' . $path),
        base_path('public_html/storage/' . $path),
        base_path('storage/uploads/' . basename($path)),
        public_path('uploads/' . basename($path)),
    ];

    $fullPath = null;
    foreach ($possiblePaths as $p) {
        if (file_exists($p) && !is_dir($p)) {
            $fullPath = $p;
            break;
        }
    }

    if (!$fullPath) {
        abort(404);
    }

    $mime = mime_content_type($fullPath) ?: 'image/jpeg';
    return response()->file($fullPath, [
        'Content-Type' => $mime,
        'Cache-Control' => 'public, max-age=86400',
    ]);
})->where('path', '.*');

// Storage Diagnostic Route
Route::get('/check-storage', function () {
    $target = storage_path('app/public/uploads');
    $link = public_path('storage/uploads');
    $files = file_exists($target) ? scandir($target) : [];
    
    return response()->json([
        'target_folder' => $target,
        'target_exists' => file_exists($target),
        'public_link' => $link,
        'link_exists' => file_exists($link),
        'files_count' => count(array_diff($files, ['.', '..'])),
        'sample_files' => array_values(array_slice(array_diff($files, ['.', '..']), 0, 10)),
    ]);
});

// Storage Auto-Fix Route (Pindahkan File ke Dedicated Folder /uploads/ Tanpa Symlink)
Route::get('/fix-storage', function () {
    $target = storage_path('app/public/uploads');
    $publicUploads = public_path('uploads');
    $baseUploads = base_path('uploads');

    $log = [];

    // 1. Buat folder fisik terpisah public_path('uploads')
    if (!file_exists($publicUploads)) {
        @mkdir($publicUploads, 0755, true);
        $log[] = "Created dedicated folder: $publicUploads";
    }

    // 2. Buat folder fisik terpisah base_path('uploads')
    if (!file_exists($baseUploads)) {
        @mkdir($baseUploads, 0755, true);
        $log[] = "Created dedicated folder: $baseUploads";
    }

    // 3. Salin seluruh file foto ke folder fisik dedicated
    $copied = 0;
    if (file_exists($target)) {
        $files = scandir($target);
        foreach ($files as $f) {
            if ($f !== '.' && $f !== '..') {
                $src = $target . '/' . $f;
                if (is_file($src)) {
                    @copy($src, $publicUploads . '/' . $f);
                    @chmod($publicUploads . '/' . $f, 0644);

                    @copy($src, $baseUploads . '/' . $f);
                    @chmod($baseUploads . '/' . $f, 0644);

                    $copied++;
                }
            }
        }
    }

    @chmod($publicUploads, 0755);
    @chmod($baseUploads, 0755);
    @exec("chmod -R 755 " . escapeshellarg($publicUploads));
    @exec("chmod -R 755 " . escapeshellarg($baseUploads));

    return response()->json([
        'status' => 'success',
        'message' => 'Successfully copied all upload files into standalone dedicated uploads directory!',
        'copied_files_count' => $copied,
        'logs' => $log,
    ]);
});

// Auth Routes (Login & Logout)
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::match(['get', 'post'], '/logout', [AuthController::class, 'logout'])->name('logout');

// ============================================================
// PROTECTED ROUTES (Wajib Login Sebagai Admin/Petugas)
// ============================================================
Route::middleware(['auth'])->group(function () {
    // Form Survey Lapangan & Dokumen Verval (Wajib Login)
    Route::get('/survey/{id?}', [SurveyController::class, 'index'])->name('survey');
    Route::get('/survei/{id?}', [SurveyController::class, 'index']);
    Route::post('/survey/{id?}', [SurveyController::class, 'store'])->name('survey.store');
    Route::put('/survey/{id}', [SurveyController::class, 'store'])->name('survey.update');
    Route::post('/survey/upload-photo', [SurveyController::class, 'uploadPhoto'])->name('survey.upload-photo');

    // Admin Dashboard System
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    // Data Verval BSPS
    Route::get('/verval-data', [VervalDataController::class, 'index'])->name('verval-data');
    Route::get('/data-verval', [VervalDataController::class, 'index'])->name('data-verval');
    Route::get('/verval-data/surat-pernyataan-kolektif', [VervalDataController::class, 'suratPernyataanKolektif'])->name('verval-data.surat-pernyataan-kolektif');
    Route::get('/verval-data/{id}/surat-pernyataan', [VervalDataController::class, 'suratPernyataan'])->name('verval-data.surat-pernyataan');
    Route::get('/data-verval/{id}/edit', function($id) { return redirect()->route('survey', ['id' => $id]); })->name('data-verval.edit');
    Route::put('/data-verval/{id}', [SurveyController::class, 'store'])->name('data-verval.update');
    Route::put('/data-verval/{id}/status', [VervalDataController::class, 'updateStatus'])->name('data-verval.update-status');

    // Geo Maps
    Route::get('/geomaps', [GeoMapController::class, 'index'])->name('geomaps');
    Route::get('/geoMaps', [GeoMapController::class, 'index']);

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
