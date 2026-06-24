<?php

use App\Http\Controllers\Trips\CreateController;
use App\Http\Controllers\Trips\DeleteController;
use App\Http\Controllers\Trips\IndexController;
use App\Http\Controllers\Trips\ShowController;
use App\Http\Controllers\Trips\ToggleLikesController;
use App\Http\Controllers\Trips\UpdateController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/trips', IndexController::class);
    Route::post('/trips', CreateController::class);
    Route::get('/trips/{trip}', ShowController::class)->can('view', 'trip');
    Route::put('/trips/{trip}', UpdateController::class)->can('update', 'trip');
    Route::delete('/trips/{trip}', DeleteController::class)->can('delete', 'trip');
    Route::post('/trips/{trip}/like', ToggleLikesController::class);
});
