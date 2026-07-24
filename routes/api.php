<?php

use App\Http\Controllers\Api\LogController;
use Illuminate\Support\Facades\Route;

// Trasy zabezpieczone kluczem API Sanctum
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/v1/logs', [LogController::class, 'store']);
    Route::get('/v1/logs', [LogController::class, 'index']);
    Route::get('/v1/logs/{log}', [LogController::class, 'show']);
});