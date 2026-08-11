<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\SurveyController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DashboardKecamatanController;
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
    $filename = basename($path);
    $possiblePaths = [
        public_path('uploads/' . $filename),
        base_path('uploads/' . $filename),
        storage_path('app/public/uploads/' . $filename),
        storage_path('app/public/' . $path),
        storage_path('app/' . $path),
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

// Dynamic Uploads Route (Bypass 404 & Auto Sync Files dari Storage)
Route::get('/uploads/{filename}', function ($filename) {
    $possiblePaths = [
        public_path('uploads/' . $filename),
        base_path('uploads/' . $filename),
        storage_path('app/public/uploads/' . $filename),
        storage_path('app/public/' . $filename),
        storage_path('app/' . $filename),
        storage_path('app/uploads/' . $filename),
    ];

    $foundPath = null;
    foreach ($possiblePaths as $p) {
        if (file_exists($p) && !is_dir($p)) {
            $foundPath = $p;
            break;
        }
    }

    if (!$foundPath) {
        abort(404);
    }

    // Salin otomatis ke public_html/uploads agar Nginx melayani langsung untuk berikutnya
    $dest = public_path('uploads/' . $filename);
    if (!file_exists($dest)) {
        @mkdir(dirname($dest), 0755, true);
        @copy($foundPath, $dest);
        @chmod($dest, 0644);
    }

    $mime = mime_content_type($foundPath) ?: 'image/jpeg';
    return response()->file($foundPath, [
        'Content-Type' => $mime,
        'Cache-Control' => 'public, max-age=86400',
    ]);
})->where('filename', '.*');

// Storage Diagnostic Route
Route::get('/check-storage', function () {
    $target = storage_path('app/public/uploads');
    $publicUploads = public_path('uploads');
    $targetFiles = file_exists($target) ? scandir($target) : [];
    $publicFiles = file_exists($publicUploads) ? scandir($publicUploads) : [];
    
    return response()->json([
        'target_folder' => $target,
        'target_exists' => file_exists($target),
        'public_uploads' => $publicUploads,
        'public_exists' => file_exists($publicUploads),
        'target_files_count' => count(array_diff($targetFiles, ['.', '..'])),
        'public_files_count' => count(array_diff($publicFiles, ['.', '..'])),
        'public_sample_files' => array_values(array_slice(array_diff($publicFiles, ['.', '..']), 0, 15)),
    ]);
});

// Storage Auto-Fix Route (Pindahkan Seluruh File Foto dari Storage ke /uploads/ Dedicated)
Route::get('/fix-storage', function () {
    $publicUploads = public_path('uploads');
    $baseUploads = base_path('uploads');

    if (!file_exists($publicUploads)) {
        @mkdir($publicUploads, 0755, true);
    }
    if (!file_exists($baseUploads)) {
        @mkdir($baseUploads, 0755, true);
    }

    $searchFolders = [
        storage_path('app/public/uploads'),
        storage_path('app/public'),
        storage_path('app/uploads'),
        storage_path('app'),
    ];

    $copied = 0;
    $log = [];

    foreach ($searchFolders as $folder) {
        if (file_exists($folder) && is_dir($folder)) {
            $files = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($folder, RecursiveDirectoryIterator::SKIP_DOTS),
                RecursiveIteratorIterator::SELF_FIRST
            );
            foreach ($files as $file) {
                if ($file->isFile()) {
                    $ext = strtolower($file->getExtension());
                    if (in_array($ext, ['jpg', 'jpeg', 'png', 'webp', 'pdf'])) {
                        $filename = $file->getFilename();
                        $destPublic = $publicUploads . '/' . $filename;
                        $destBase = $baseUploads . '/' . $filename;

                        if (!file_exists($destPublic)) {
                            @copy($file->getPathname(), $destPublic);
                            @chmod($destPublic, 0644);
                            $copied++;
                        }
                        if (!file_exists($destBase)) {
                            @copy($file->getPathname(), $destBase);
                            @chmod($destBase, 0644);
                        }
                    }
                }
            }
        }
    }

    @chmod($publicUploads, 0755);
    @chmod($baseUploads, 0755);

    return response()->json([
        'status' => 'success',
        'message' => 'Successfully scanned & synced all uploaded media files to dedicated /uploads/ directory!',
        'synced_files_count' => $copied,
        'public_uploads_total' => count(array_diff(scandir($publicUploads), ['.', '..'])),
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
    // Ajax Survey Photo Handlers (Specific routes must be defined before {id})
    Route::post('/survey/upload-photo', [SurveyController::class, 'uploadPhoto'])->name('survey.upload-photo');
    Route::post('/survey/delete-photo', [SurveyController::class, 'deletePhoto'])->name('survey.delete-photo');

    // Form Survey Lapangan & Dokumen Verval (Wajib Login)
    Route::get('/survey/{id?}', [SurveyController::class, 'index'])->name('survey')->where('id', '[0-9]+');
    Route::get('/survei/{id?}', [SurveyController::class, 'index'])->where('id', '[0-9]+');
    Route::post('/survey/{id?}', [SurveyController::class, 'store'])->name('survey.store')->where('id', '[0-9]+');
    Route::put('/survey/{id}', [SurveyController::class, 'store'])->name('survey.update')->where('id', '[0-9]+');

    // Admin Dashboard System (Kabupaten & Kecamatan)
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard-kecamatan', [DashboardKecamatanController::class, 'index'])
        ->middleware('role:admin,admin_kecamatan')
        ->name('dashboard.kecamatan');
    // Data Verval BSPS
    Route::get('/verval-data', [VervalDataController::class, 'index'])->name('verval-data');
    Route::get('/data-verval', [VervalDataController::class, 'index'])->name('data-verval');
    Route::get('/verval-data/surat-pernyataan-kolektif', [VervalDataController::class, 'suratPernyataanKolektif'])->name('verval-data.surat-pernyataan-kolektif');
    Route::get('/verval-data/{id}/surat-pernyataan', [VervalDataController::class, 'suratPernyataan'])->name('verval-data.surat-pernyataan');
    Route::get('/data-verval/{id}/edit', function($id) { return redirect()->route('survey', ['id' => $id]); })->name('data-verval.edit');
    Route::put('/data-verval/{id}', [SurveyController::class, 'store'])->name('data-verval.update');
    Route::put('/data-verval/{id}/status', [VervalDataController::class, 'updateStatus'])->name('data-verval.update-status');

    // Pencocokan Data Kependudukan (Dataguse vs Data Penerima BSPS) — Khusus Super Admin
    Route::middleware(['role:admin'])->group(function () {
        Route::get('/pencocokan-data', [\App\Http\Controllers\PencocokanDataController::class, 'index'])->name('pencocokan-data');
        Route::post('/pencocokan-data/sync-batch', [\App\Http\Controllers\PencocokanDataController::class, 'syncBatch'])->name('pencocokan-data.sync-batch');
        Route::post('/pencocokan-data/{id}/sync', [\App\Http\Controllers\PencocokanDataController::class, 'syncSingle'])->name('pencocokan-data.sync-single');
    });

    // Geo Maps
    Route::get('/geomaps', [GeoMapController::class, 'index'])->name('geomaps');
    Route::get('/geoMaps', [GeoMapController::class, 'index']);

    // Penugasan & Petugas Survei Management — Khusus Admin Kab. & Admin Kec.
    Route::middleware(['role:admin,admin_kecamatan'])->group(function () {
        // Penugasan Petugas Survei Lapangan
        Route::get('/penugasan', [PenugasanController::class, 'index'])->name('penugasan');
        Route::post('/penugasan/{dataMingguan}', [PenugasanController::class, 'update'])->name('penugasan.update');

        // User Management
        Route::get('/user', [UserController::class, 'index'])->name('user');
        Route::get('/user/export-admin-kecamatan', [UserController::class, 'exportAdminKecamatan'])->name('user.export-admin-kecamatan');
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

    // Update Status Keberadaan Calon Penerima (via Ajax Modal Dashboard Petugas)
    Route::put('/data-verval/{id}/status', [PetugasController::class, 'updateStatus'])->name('petugas.update-status');

    // Rekapitulasi Laporan
    Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan');
    Route::get('/laporan/export', [LaporanController::class, 'exportExcel'])->name('laporan.export');
    Route::get('/laporan/cetak', [LaporanController::class, 'cetak'])->name('laporan.cetak');

    // Pengaturan System & Tema
    Route::get('/setting', [SettingController::class, 'index'])->name('setting');
});

