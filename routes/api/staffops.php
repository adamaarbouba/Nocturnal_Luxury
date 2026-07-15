<?php

use App\Http\Controllers\Api\Cleaner\DashboardController as CleanerDashboardController;
use App\Http\Controllers\Api\Inspector\DashboardController as InspectorDashboardController;
use App\Http\Controllers\Api\Staff\HotelBrowseController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum', 'role:cleaner,inspector,receptionist'])->group(function () {
    Route::get('/staff/hotels', [HotelBrowseController::class, 'index']);
    Route::get('/staff/hotels/{hotel}/apply', [HotelBrowseController::class, 'apply']);
    Route::post('/staff/hotels/{hotel}/apply', [HotelBrowseController::class, 'storeApplication']);
    Route::get('/staff/my-applications', [HotelBrowseController::class, 'myApplications']);
});

Route::middleware(['auth:sanctum', 'role:cleaner'])->group(function () {
    Route::get('/cleaner/dashboard', [CleanerDashboardController::class, 'index']);
    Route::get('/cleaner/rooms/{room}/complete', [CleanerDashboardController::class, 'showCompletionForm']);
    Route::post('/cleaner/rooms/{room}/complete', [CleanerDashboardController::class, 'completeRoom']);
});

Route::middleware(['auth:sanctum', 'role:inspector'])->group(function () {
    Route::get('/inspector/dashboard', [InspectorDashboardController::class, 'index']);
    Route::get('/inspector/rooms/{room}/inspect', [InspectorDashboardController::class, 'showInspectionForm']);
    Route::post('/inspector/rooms/{room}/inspect', [InspectorDashboardController::class, 'completeInspection']);
});

// ponytail: staff dashboard blade is static placeholders — no API endpoint needed.
