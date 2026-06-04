<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GeocodersController;

Route::get('/', function () {
    return redirect('/search');
});

Route::get('/search', [GeocodersController::class, 'form']);

Route::get('/geocode', [GeocodersController::class, 'index']);

Route::post('/reverse-geocode', [GeocodersController::class, 'reverse']);

Route::post('/bulk-geocode', [GeocodersController::class, 'bulkUpload']);

Route::get('/history', [GeocodersController::class, 'getHistory']);

Route::delete('/history/{id}', [GeocodersController::class, 'deleteHistory']);

Route::delete('/history-clear', [GeocodersController::class, 'clearHistory']);

Route::get('/export-csv', [GeocodersController::class, 'exportCsv']);