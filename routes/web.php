<?php

use App\Http\Controllers\PrediksiController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ExcelPreviewController;
use App\Http\Controllers\InputDataController;

// Route::get('/', function () {
//     return view('welcome');
// });

Route::get('/', [ProfileController::class, 'login'])->name('login');
Route::get('/reset', [ProfileController::class, 'reset'])->name('reset');
Route::get('/verify', [ProfileController::class, 'verify'])->name('verify');
Route::get('/register', [ProfileController::class, 'register'])->name('register');


Route::get('/dashboard', [ProfileController::class, 'dashboard'])->name('dashboard');

Route::get('/user-profile', [ProfileController::class, 'create'])->name('user-profile');
Route::put('/user-profile', [ProfileController::class, 'update'])->name('user-profile.update');
Route::get('/user-management', [ProfileController::class, 'index'])->name('user-management');

//halaman input data
Route::get('/input-data', [ProfileController::class, 'inputData'])->name('input-data');
Route::post('/preview-excel', [InputDataController::class, 'preview'])->name('preview.excel');
Route::post('/simpan-data', [InputDataController::class, 'simpanData'])->name('simpan.data');


Route::get('/prediksi', [ProfileController::class, 'prediksi'])->name('prediksi');
Route::post('/prediksi', [PrediksiController::class, 'processAndPredict'])->name('prediction.process');
Route::get('/prediksi/hasil/{id}', [PrediksiController::class, 'showResult'])->name('prediction.result');

Route::get('/virtual-reality', [ProfileController::class, 'virtualReality'])->name('virtual-reality');
Route::get('/rtl', [ProfileController::class, 'rtl'])->name('rtl');
Route::get('/notifications', [ProfileController::class, 'notifications'])->name('notifications');
Route::get('/profile', [ProfileController::class, 'profile'])->name('profile');
Route::get('/static-sign-in', [ProfileController::class, 'staticSignIn'])->name('static-sign-in');
Route::get('/static-sign-up', [ProfileController::class, 'staticSignUp'])->name('static-sign-up');
Route::get('/logout', [ProfileController::class, 'logout'])->name('logout');
