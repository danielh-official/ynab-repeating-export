<?php

use App\Http\Controllers\ExportController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\YnabController;
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

Route::post('export', ExportController::class)->name('export');

Route::prefix('ynab')->name('ynab.')->controller(YnabController::class)->group(function () {
    Route::get('callback', 'callback')->name('callback');
});
