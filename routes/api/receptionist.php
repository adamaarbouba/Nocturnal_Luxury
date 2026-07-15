<?php

use App\Http\Controllers\Api\Receptionist\Hotel\CheckInOutController;
use App\Http\Controllers\Api\Receptionist\DashboardController;
use App\Http\Controllers\Api\Receptionist\BookingController;
use App\Http\Controllers\Api\Receptionist\PaymentController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum', 'role:receptionist'])->prefix('receptionist')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index']);

    Route::get('/check-in', [CheckInOutController::class, 'indexCheckIn']);
    Route::get('/check-in/{booking}', [CheckInOutController::class, 'showCheckIn']);
    Route::post('/check-in/{booking}', [CheckInOutController::class, 'processCheckIn']);

    Route::get('/check-out', [CheckInOutController::class, 'indexCheckOut']);
    Route::get('/check-out/{booking}', [CheckInOutController::class, 'showCheckOut']);
    Route::post('/check-out/{booking}', [CheckInOutController::class, 'processCheckOut']);

    Route::get('/bookings', [BookingController::class, 'index']);
    Route::get('/bookings/create', [BookingController::class, 'create']);
    Route::post('/bookings', [BookingController::class, 'store']);
    Route::get('/bookings/status/{status}', [BookingController::class, 'filterByStatus']);
    Route::get('/bookings/{booking}', [BookingController::class, 'show']);
    Route::post('/bookings/{booking}/confirm', [BookingController::class, 'confirmBooking']);
    Route::post('/bookings/{booking}/cancel', [BookingController::class, 'cancelBooking']);

    Route::get('/bookings/{booking}/payment', [PaymentController::class, 'showPaymentForm']);
    Route::post('/bookings/{booking}/payment', [PaymentController::class, 'recordPayment']);
    Route::delete('/bookings/{booking}/payments/{payment}', [PaymentController::class, 'deletePayment']);

    Route::post('/refund-requests/{refundRequest}/approve', [PaymentController::class, 'approveRefund']);
    Route::post('/refund-requests/{refundRequest}/deny', [PaymentController::class, 'denyRefund']);
});
