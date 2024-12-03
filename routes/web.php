<?php

use App\Http\Controllers\ExportController;
use App\Http\Controllers\GuideController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PrivacyController;
use App\Http\Controllers\SampleExportController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', HomeController::class)->name('home');

Route::get('/privacy', PrivacyController::class)->name('privacy');

Route::get('/guide', GuideController::class)->name('guide');

Route::post('/export', ExportController::class)->name('export');

Route::post('/sample/export', SampleExportController::class)->name('sample.export');

Route::ynabSdkLaravelOauth();
