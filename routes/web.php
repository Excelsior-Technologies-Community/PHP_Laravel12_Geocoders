<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GeocodersController;
use App\Models\SearchHistory;

// Home redirect
Route::get('/', function () {
    return redirect('/search');
});

// UI Page (Blade form)
Route::get('/search', [GeocodersController::class, 'form']);

// Geocode API + UI result handler
Route::get('/geocode', [GeocodersController::class, 'index']);

// History API
Route::get('/history', function () {
    return SearchHistory::latest()->get();
});