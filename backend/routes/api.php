<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\TargetController;
use App\Http\Controllers\Api\StatusController;
use App\Http\Controllers\Api\HistoryController;
use App\Http\Controllers\Api\AlertController;
use App\Http\Controllers\Api\AuthController;

// Auth routes
Route::post('login', [AuthController::class, 'login']);

// Protected routes
Route::middleware('auth:api')->group(function () {
    Route::get('targets', [TargetController::class, 'index']);
    Route::post('targets', [TargetController::class, 'store']);
    Route::get('status/{id}', [StatusController::class, 'show']);
    Route::get('history/{id}', [HistoryController::class, 'show']);
    Route::get('alerts', [AlertController::class, 'index']);
});
