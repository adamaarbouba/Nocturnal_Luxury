<?php

use App\Http\Controllers\Api\Admin\DashboardController;
use App\Http\Controllers\Api\Admin\HotelController;
use App\Http\Controllers\Api\Admin\HotelRequestController;
use App\Http\Controllers\Api\Admin\UserController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum', 'role:admin'])->prefix('admin')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index']);

    Route::get('/users', [UserController::class, 'index']);
    Route::get('/users/{user}', [UserController::class, 'show']);
    Route::post('/users/{user}/ban', [UserController::class, 'ban']);
    Route::post('/users/{user}/unban', [UserController::class, 'unban']);

    Route::get('/hotel-requests', [HotelRequestController::class, 'index']);
    Route::get('/hotel-requests/{hotelRequest}', [HotelRequestController::class, 'show']);
    Route::post('/hotel-requests/{hotelRequest}/approve', [HotelRequestController::class, 'approve']);
    Route::post('/hotel-requests/{hotelRequest}/reject', [HotelRequestController::class, 'reject']);

    Route::get('/hotels/{hotel}', [HotelController::class, 'show']);
});
