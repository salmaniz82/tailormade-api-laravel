<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\UserController;
use App\Http\Controllers\SwatchesController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\DashboardController;

Route::get('/', function () {
    return 'Cloth Library API';
});

Route::get('/login', [UserController::class, 'showLogin'])->name('login');

Route::post('/login', [UserController::class, 'login']);

Route::get('/logout', [UserController::class, 'logout'])->name('logout');


Route::middleware(['admin'])->group(function () {

Route::get('/dashboard', [DashboardController::class, 'index'])->name('Dashboard');

});




/*
Route::get('/create-admin', [UserController::class, 'createAdminUser']);
*/

Route::apiResource('stocks', StockController::class);
