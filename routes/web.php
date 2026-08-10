<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\VervalDataController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\SettingController;

/*
|--------------------------------------------------------------------------
| Web Routes (BSPS Verval System)
|--------------------------------------------------------------------------
*/

// Public Landing & Auth Routes
Route::get('/', [LandingController::class, 'index'])->name('landing');
Route::get('/landing', [LandingController::class, 'index']);

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::match(['get', 'post'], '/logout', [AuthController::class, 'logout'])->name('logout');

// Main Application Routes (Dashboard & Data Verval)
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/data-verval', [VervalDataController::class, 'index'])->name('data-verval');
    Route::get('/data-verval/{id}/edit', [VervalDataController::class, 'edit'])->name('data-verval.edit');
    Route::put('/data-verval/{id}', [VervalDataController::class, 'update'])->name('data-verval.update');
    Route::put('/data-verval/{id}/status', [VervalDataController::class, 'updateStatus'])->name('data-verval.update-status');
    Route::get('/data_verval', [VervalDataController::class, 'index']);

    // Setting
    Route::get('/setting', [SettingController::class, 'index'])->name('setting');

    // User Management
    Route::get('/user', [UserController::class, 'index'])->name('user');
    Route::post('/user', [UserController::class, 'store'])->name('user.store');
    Route::put('/user/{id}', [UserController::class, 'update'])->name('user.update');
    Route::delete('/user/{id}', [UserController::class, 'destroy'])->name('user.destroy');
});
