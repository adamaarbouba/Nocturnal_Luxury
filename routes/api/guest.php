<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Guest\DashboardController;
use App\Http\Controllers\Api\Guest\HotelsController;
use App\Http\Controllers\Api\Guest\BookingController;
use App\Http\Controllers\Api\Guest\PaymentController;
use App\Http\Controllers\Api\Guest\ReviewController;

Route::middleware(['auth:sanctum', 'role:guest'])->prefix('guest')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index']);

    Route::get('/hotels', [HotelsController::class, 'index']);
    Route::get('/hotels/{hotel}', [HotelsController::class, 'show']);

    Route::get('/bookings', [BookingController::class, 'index']);
    Route::get('/rooms/{room}/book', [BookingController::class, 'create']);
    Route::post('/rooms/{room}/book', [BookingController::class, 'store']);
    Route::get('/bookings/{booking}/confirmation', [BookingController::class, 'confirmation']);

    Route::get('/bookings/{booking}/payment', [PaymentController::class, 'showPaymentForm']);
    Route::post('/bookings/{booking}/payment', [PaymentController::class, 'processPayment']);
    Route::post('/bookings/{booking}/refund-request', [PaymentController::class, 'requestRefund']);

    Route::get('/reviews', [ReviewController::class, 'index']);
    Route::get('/bookings/{booking}/review', [ReviewController::class, 'create']);
    Route::post('/bookings/{booking}/review', [ReviewController::class, 'store']);
    Route::get('/reviews/{review}', [ReviewController::class, 'show']);
});
