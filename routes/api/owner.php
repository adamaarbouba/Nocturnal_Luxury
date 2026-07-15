<?php

use App\Http\Controllers\Api\Owner\DashboardController;
use App\Http\Controllers\Api\Owner\HotelRequestController;
use App\Http\Controllers\Api\Owner\HotelsController;
use App\Http\Controllers\Api\Owner\MaintenanceController;
use App\Http\Controllers\Api\Owner\StaffController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum', 'role:owner'])->prefix('owner')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index']);

    Route::get('/hotels', [HotelsController::class, 'index']);
    Route::get('/hotels/{hotel}', [HotelsController::class, 'show']);
    Route::get('/hotels/{hotel}/manage', [HotelsController::class, 'manage']);
    Route::post('/hotels/{hotel}/add-room', [HotelsController::class, 'addRoom']);
    Route::post('/hotels/{hotel}/update-room-status', [HotelsController::class, 'updateRoomStatus']);
    Route::post('/hotels/{hotel}/rooms/{room}/update', [HotelsController::class, 'updateRoom']);
    Route::delete('/hotels/{hotel}/rooms/{room}', [HotelsController::class, 'deleteRoom']);
    Route::patch('/hotels/{hotel}/update', [HotelsController::class, 'updateHotel']);

    Route::post('/hotel-requests', [HotelRequestController::class, 'store']);
    Route::get('/hotel-requests', [HotelRequestController::class, 'myRequests']);

    Route::get('/hotels/{hotel}/staff', [StaffController::class, 'index']);
    Route::get('/staff/applications', [StaffController::class, 'applications']);
    Route::post('/staff/applications/{staffApplication}/approve', [StaffController::class, 'approveApplication']);
    Route::post('/staff/applications/{staffApplication}/reject', [StaffController::class, 'rejectApplication']);
    Route::delete('/hotels/{hotel}/staff/{user}', [StaffController::class, 'removeStaff']);
    Route::patch('/hotels/{hotel}/staff/{user}/wage', [StaffController::class, 'updateWage']);

    Route::get('/maintenance', [MaintenanceController::class, 'index']);
    Route::get('/maintenance/{maintenanceRequest}', [MaintenanceController::class, 'show']);
    Route::post('/maintenance/{maintenanceRequest}/transition', [MaintenanceController::class, 'transition']);
});
