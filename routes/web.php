<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\UserController;
use App\Http\Controllers\SwatchesController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\DashboardController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/login', [UserController::class, 'showLogin'])->name('login');

Route::post('/login', [UserController::class, 'login']);
Route::get('/logout', [UserController::class, 'logout'])->name('logout');

Route::get('/api/swatches', [swatchesController::class, 'index']);
Route::get('/dashboard', [DashboardController::class, 'index'])->name('Dashboard');


/*
Route::get('/create-admin', [UserController::class, 'createAdminUser']);
*/

Route::apiResource('stocks', StockController::class);
