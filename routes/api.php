<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Example protected route (needs auth:api middleware)

/*
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
*/

// Example open route
Route::get('/swatches', [App\Http\Controllers\SwatchesController::class, 'index']);

