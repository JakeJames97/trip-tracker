<?php

use App\Http\Controllers\CountriesController;
use App\Http\Controllers\Trips\DiscoverController;

require __DIR__ . '/auth.php';
require __DIR__ . '/trips.php';
require __DIR__ . '/destinations.php';
require __DIR__ . '/tasks.php';

Route::get('/countries', CountriesController::class);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/discover', DiscoverController::class);
});
