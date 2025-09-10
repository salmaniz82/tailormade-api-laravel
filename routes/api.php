<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;




/*
Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
*/


Route::get('/swatches', [App\Http\Controllers\SwatchesController::class, 'index']);

Route::get('/swatch/{id}', [App\Http\Controllers\SwatchesController::class, 'show']);

/*
Route::get('/swatches/{id}', [App\Http\Controllers\SwatchesController::class, 'show']);
*/

Route::post('/swatches', [App\Http\Controllers\SwatchesController::class, 'store']);

Route::put('/swatches/{id}', [App\Http\Controllers\SwatchesController::class, 'update']);

Route::delete('/swatches/{id}', [App\Http\Controllers\SwatchesController::class, 'destroy']);

// Swatch Meta for the dashboard
Route::get('/swatchemeta', [App\Http\Controllers\StockController::class, 'swatchMeta']);

Route::get('/stocks', [App\Http\Controllers\StockController::class, 'index']);

Route::post('/stocks', [App\Http\Controllers\StockController::class, 'store']);

Route::delete('/stocks/{id}', [App\Http\Controllers\StockController::class, 'destroy']);

Route::put('/stocks/{id}', [App\Http\Controllers\StockController::class, 'update']);








Route::get('/hello', function() {

    return "HELLO WORLD FROM API ROUTE";

});

