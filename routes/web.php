<?php

use App\Http\Controllers\PrediksiController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\InputDataController;
use App\Http\Controllers\VisualisasiController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\SessionsController;
use App\Http\Controllers\ExportController;

// Route::get('/', function () {
//     return view('welcome');
// });

// Guest routes (accessible without login)
Route::middleware('guest')->group(function () {
    Route::get('/', [SessionsController::class, 'create'])->name('login');
    Route::post('/login', [SessionsController::class, 'store'])->name('login.store');
    Route::get('/register', [ProfileController::class, 'register'])->name('register');
    Route::post('/register', [RegisterController::class, 'store'])->name('register.store');
    Route::get('/reset', [ProfileController::class, 'reset'])->name('reset');
    Route::get('/verify', [ProfileController::class, 'verify'])->name('verify');
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
    Route::get('/prediksi', [ProfileController::class, 'prediksi'])->name('prediksi');
    Route::post('/prediksi', [PrediksiController::class, 'processAndPredict'])->name('prediction.process');
    Route::get('/prediksi/hasil/{id}', [PrediksiController::class, 'showResult'])->name('prediction.result');
    Route::post('/prediksi/upload-excel', [PrediksiController::class, 'uploadExcelDanPrediksi'])->name('prediction.upload.excel');
    Route::get('/template/download', [PrediksiController::class, 'download'])->name('template.download');
    Route::get('/visualisasi-data', [VisualisasiController::class, 'index'])->name('visualisasi-data');
    Route::get('/rtl', [ProfileController::class, 'rtl'])->name('rtl');
    Route::get('/export', [ExportController::class, 'index'])->name('export');
    Route::post('/export', [ExportController::class, 'export'])->name('export.process');
    Route::get('/profile', [ProfileController::class, 'profile'])->name('profile');
    Route::get('/static-sign-in', [ProfileController::class, 'staticSignIn'])->name('static-sign-in');
    Route::get('/static-sign-up', [ProfileController::class, 'staticSignUp'])->name('static-sign-up');
    Route::post('/logout', [SessionsController::class, 'destroy'])->name('logout');
});
