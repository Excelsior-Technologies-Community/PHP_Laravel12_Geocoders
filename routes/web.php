<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GeocodersController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/geocode', [GeocodersController::class, 'index']);