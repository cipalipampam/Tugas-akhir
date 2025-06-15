<?php

use App\Http\Controllers\PerformaController;
use App\Http\Controllers\PrediksiController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\InputDataController;
use App\Http\Controllers\VisualisasiController;
use App\Http\Controllers\SessionsController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\KebijakanController;
use App\Http\Controllers\UserManagementController;

// Route::get('/', function () {
//     return view('welcome');
// });

// Guest routes (accessible without login)
Route::middleware('guest')->group(function () {
    Route::get('/', [SessionsController::class, 'create'])->name('login');
    Route::post('/login', [SessionsController::class, 'store'])->name('login.store');
    Route::get('/reset', [ProfileController::class, 'reset'])->name('reset');
    Route::post('/reset', [SessionsController::class, 'show'])->name('password.email');
    Route::get('/verify', [ProfileController::class, 'verify'])->name('verify');
    Route::get('/reset-password/{token}', [ProfileController::class, 'reset'])->name('password.reset');
    Route::post('/reset-password', [SessionsController::class, 'update'])->name('password.update');
});

// Protected routes (require authentication)
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/user-profile', [ProfileController::class, 'create'])->name('user-profile');
    Route::put('/user-profile', [ProfileController::class, 'update'])->name('user-profile.update');
    Route::get('/user-management', [ProfileController::class, 'index'])->name('user-management');
    Route::get('/input-data', [ProfileController::class, 'inputData'])->name('input-data');
    Route::post('/preview-excel', [InputDataController::class, 'preview'])->name('preview.excel');
    Route::post('/simpan-data', [InputDataController::class, 'simpanData'])->name('simpan.data');
    Route::get('/prediksi', [PrediksiController::class, 'index'])->name('prediksi');
    Route::post('/prediksi', [PrediksiController::class, 'processAndPredict'])->name('prediction.process');
    Route::get('/prediksi/hasil/{id}', [PrediksiController::class, 'showResult'])->name('prediction.result');
    Route::post('/prediksi/upload-excel', [PrediksiController::class, 'uploadExcelDanPrediksi'])->name('prediction.upload.excel');
    Route::get('/template/download', function () {
        $path = public_path('templates/template_data_siswa.xlsx');
        return response()->download($path, 'template_data_siswa.xlsx');
    })->name('template.download');
    Route::get('/performa', [PerformaController::class, 'index'])->name('performa');
    Route::post('/performa/evaluate', [PerformaController::class, 'evaluate'])->name('performa.evaluate');
    Route::get('/visualisasi-data', [VisualisasiController::class, 'index'])->name('visualisasi-data');
    Route::get('/export', [ExportController::class, 'index'])->name('export');
    Route::post('/export', [ExportController::class, 'export'])->name('export.process');
    Route::get('/profile', [ProfileController::class, 'profile'])->name('profile');
    Route::get('/static-sign-in', [ProfileController::class, 'staticSignIn'])->name('static-sign-in');
    Route::get('/static-sign-up', [ProfileController::class, 'staticSignUp'])->name('static-sign-up');
    Route::post('/logout', [SessionsController::class, 'destroy'])->name('logout');
    Route::get('/download-template', [InputDataController::class, 'downloadTemplate'])->name('download.template');

    // Kebijakan routes
    Route::middleware([\App\Http\Middleware\CheckRole::class.':superadministrator'])->group(function () {
        Route::get('/kebijakan', [KebijakanController::class, 'index'])->name('kebijakan.index');
        Route::get('/kebijakan/create', [KebijakanController::class, 'create'])->name('kebijakan.create');
        Route::post('/kebijakan', [KebijakanController::class, 'store'])->name('kebijakan.store');
        Route::delete('/kebijakan/{id}', [KebijakanController::class, 'destroy'])->name('kebijakan.destroy');
    });

    // User management routes
    Route::middleware(['auth', \App\Http\Middleware\CheckRole::class.':superadministrator'])->group(function () {
        Route::get('/user-management', [UserManagementController::class, 'index'])->name('user-management.index');
        Route::get('/user-management/create', [UserManagementController::class, 'create'])->name('user-management.create');
        Route::post('/user-management', [UserManagementController::class, 'store'])->name('user-management.store');
        Route::get('/user-management/{user}/edit', [UserManagementController::class, 'edit'])->name('user-management.edit');
        Route::put('/user-management/{user}', [UserManagementController::class, 'update'])->name('user-management.update');
        Route::delete('/user-management/{user}', [UserManagementController::class, 'destroy'])->name('user-management.destroy');
    });
});