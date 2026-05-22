<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GeocodersController;

// Home redirect
Route::get('/', function () {
    return redirect('/search');
});

// UI Page (Blade form)
Route::get('/search', [GeocodersController::class, 'form']);

// Geocode API + UI result handler
Route::get('/geocode', [GeocodersController::class, 'index']);


// History routes
Route::get('/history', [GeocodersController::class, 'getHistory']);
Route::delete('/history/{id}', [GeocodersController::class, 'deleteHistory']);
Route::delete('/history-clear', [GeocodersController::class, 'clearHistory']);
Route::get('/export-csv', [GeocodersController::class, 'exportCsv']);