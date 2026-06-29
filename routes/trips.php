<?php

use App\Http\Controllers\Trips\CloneController;
use App\Http\Controllers\Trips\CreateController;
use App\Http\Controllers\Trips\DeleteController;
use App\Http\Controllers\Trips\DiscoverController;
use App\Http\Controllers\Trips\IndexController;
use App\Http\Controllers\Trips\ShowController;
use App\Http\Controllers\Trips\ToggleLikesController;
use App\Http\Controllers\Trips\UpdateController;
use App\Http\Middleware\OptionalSanctumAuth;
use Illuminate\Support\Facades\Route;

Route::middleware(OptionalSanctumAuth::class)->group(function () {
    Route::get('/trips/{trip}', ShowController::class);
    Route::get('/discover', DiscoverController::class);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/trips', IndexController::class);
    Route::post('/trips', CreateController::class);
    Route::put('/trips/{trip}', UpdateController::class)->can('update', 'trip');
    Route::delete('/trips/{trip}', DeleteController::class)->can('delete', 'trip');
    Route::post('/trips/{trip}/like', ToggleLikesController::class);
    Route::post('/trips/{trip}/clone', CloneController::class);
});
