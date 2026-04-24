<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Admin\HotelRequestController as AdminHotelRequestController;
use App\Http\Controllers\Admin\HotelController as AdminHotelController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Owner\HotelRequestController as OwnerHotelRequestController;
use App\Http\Controllers\Owner\DashboardController as OwnerDashboardController;
use App\Http\Controllers\Owner\HotelsController as OwnerHotelsController;
use App\Http\Controllers\Owner\MaintenanceController as OwnerMaintenanceController;
use App\Http\Controllers\Owner\StaffController as OwnerStaffController;
use App\Http\Controllers\Receptionist\Hotel\CheckInOutController as ReceptionistCheckInOutController;
use App\Http\Controllers\Receptionist\DashboardController as ReceptionistDashboardController;
use App\Http\Controllers\Receptionist\BookingController as ReceptionistBookingController;
use App\Http\Controllers\Receptionist\PaymentController as ReceptionistPaymentController;
use App\Http\Controllers\ProfileController;



// Base Route
Route::get('/', [HomeController::class, 'index'])->name('welcome');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.store');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.store');
});

Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

Route::middleware('auth')->group(function () {

    Route::get('/dashboard', function () {
        $roleMap = [
            'admin' => 'admin.dashboard',
            'owner' => 'owner.dashboard',
            'receptionist' => 'receptionist.dashboard',
            'staff' => 'staff.dashboard',
            'cleaner' => 'cleaner.dashboard',
            'inspector' => 'inspector.dashboard',
            'guest' => 'guest.dashboard',
        ];

        $userRole = auth()->user()->role->slug;
        $dashboardRoute = $roleMap[$userRole] ?? 'admin.dashboard';

        return redirect()->route($dashboardRoute);
    })->name('dashboard');
    // Global Profile Routes - available to all authenticated users
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::get('/profile/change-password', [ProfileController::class, 'changePassword'])->name('profile.change-password');
    Route::post('/profile/change-password', [ProfileController::class, 'updatePassword'])->name('profile.update-password');
    Route::delete('/profile', [ProfileController::class, 'delete'])->name('profile.delete');


    Route::middleware('role:admin')->group(function () {
        Route::get('/admin/dashboard', [AdminDashboardController::class, 'index'])->name('admin.dashboard');

        // User Management
        Route::get('/admin/users', [AdminUserController::class, 'index'])->name('admin.users.index');
        Route::get('/admin/users/{user}', [AdminUserController::class, 'show'])->name('admin.users.show');
        Route::post('/admin/users/{user}/ban', [AdminUserController::class, 'ban'])->name('admin.users.ban');
        Route::post('/admin/users/{user}/unban', [AdminUserController::class, 'unban'])->name('admin.users.unban');

        // Hotel Requests
        Route::get('/admin/hotel-requests', [AdminHotelRequestController::class, 'index'])->name('admin.hotel-requests.index');
        Route::get('/admin/hotel-requests/{hotelRequest}', [AdminHotelRequestController::class, 'show'])->name('admin.hotel-requests.show');
        Route::post('/admin/hotel-requests/{hotelRequest}/approve', [AdminHotelRequestController::class, 'approve'])->name('admin.hotel-requests.approve');
        Route::post('/admin/hotel-requests/{hotelRequest}/reject', [AdminHotelRequestController::class, 'reject'])->name('admin.hotel-requests.reject');

        // Hotels
        Route::get('/admin/hotels/{hotel}', [AdminHotelController::class, 'show'])->name('admin.hotels.show');
    });
    // Owner Routes
    Route::middleware('role:owner')->group(function () {
        Route::get('/owner/dashboard', [OwnerDashboardController::class, 'index'])->name('owner.dashboard');

        // Hotels
        Route::get('/owner/hotels', [OwnerHotelsController::class, 'index'])->name('owner.hotels.index');
        Route::get('/owner/hotels/{hotel}', [OwnerHotelsController::class, 'show'])->name('owner.hotels.show');
        Route::get('/owner/hotels/{hotel}/manage', [OwnerHotelsController::class, 'manage'])->name('owner.hotels.manage');
        Route::post('/owner/hotels/{hotel}/add-room', [OwnerHotelsController::class, 'addRoom'])->name('owner.hotels.addRoom');
        Route::post('/owner/hotels/{hotel}/update-room-status', [OwnerHotelsController::class, 'updateRoomStatus'])->name('owner.hotels.updateRoomStatus');
        Route::post('/owner/hotels/{hotel}/rooms/{room}/update', [OwnerHotelsController::class, 'updateRoom'])->name('owner.hotels.updateRoom');
        Route::delete('/owner/hotels/{hotel}/rooms/{room}', [OwnerHotelsController::class, 'deleteRoom'])->name('owner.hotels.deleteRoom');
        Route::patch('/owner/hotels/{hotel}/update', [OwnerHotelsController::class, 'updateHotel'])->name('owner.hotels.update');

        // Hotel Requests
        Route::get('/owner/hotel-requests/create', [OwnerHotelRequestController::class, 'create'])->name('owner.hotel-requests.create');
        Route::post('/owner/hotel-requests', [OwnerHotelRequestController::class, 'store'])->name('owner.hotel-requests.store');
        Route::get('/owner/hotel-requests', [OwnerHotelRequestController::class, 'myRequests'])->name('owner.hotel-requests.index');

        // Staff Management Routes
        Route::get('/owner/hotels/{hotel}/staff', [OwnerStaffController::class, 'index'])->name('owner.staff.index');
        Route::get('/owner/staff/applications', [OwnerStaffController::class, 'applications'])->name('owner.staff.applications');
        Route::post('/owner/staff/applications/{staffApplication}/approve', [OwnerStaffController::class, 'approveApplication'])->name('owner.staff.approve');
        Route::post('/owner/staff/applications/{staffApplication}/reject', [OwnerStaffController::class, 'rejectApplication'])->name('owner.staff.reject');
        Route::delete('/owner/hotels/{hotel}/staff/{user}', [OwnerStaffController::class, 'removeStaff'])->name('owner.staff.remove');
        Route::patch('/owner/hotels/{hotel}/staff/{user}/wage', [OwnerStaffController::class, 'updateWage'])->name('owner.staff.updateWage');

        // Maintenance Routes
        Route::get('/owner/maintenance', [OwnerMaintenanceController::class, 'index'])->name('owner.maintenance.index');
        Route::get('/owner/maintenance/{maintenanceRequest}', [OwnerMaintenanceController::class, 'show'])->name('owner.maintenance.show');
        Route::post('/owner/maintenance/{maintenanceRequest}/transition', [OwnerMaintenanceController::class, 'transition'])->name('owner.maintenance.transition');
    });
    // Receptionist Routes
    Route::middleware('role:receptionist')->group(function () {
        Route::get('/receptionist/dashboard', [ReceptionistDashboardController::class, 'index'])->name('receptionist.dashboard');

        // Check-in Routes
        Route::get('/receptionist/check-in', [ReceptionistCheckInOutController::class, 'indexCheckIn'])->name('receptionist.check-in.index');
        Route::get('/receptionist/check-in/{booking}', [ReceptionistCheckInOutController::class, 'showCheckIn'])->name('receptionist.check-in.show');
        Route::post('/receptionist/check-in/{booking}', [ReceptionistCheckInOutController::class, 'processCheckIn'])->name('receptionist.check-in.process');

        // Check-out Routes
        Route::get('/receptionist/check-out', [ReceptionistCheckInOutController::class, 'indexCheckOut'])->name('receptionist.check-out.index');
        Route::get('/receptionist/check-out/{booking}', [ReceptionistCheckInOutController::class, 'showCheckOut'])->name('receptionist.check-out.show');
        Route::post('/receptionist/check-out/{booking}', [ReceptionistCheckInOutController::class, 'processCheckOut'])->name('receptionist.check-out.process');

        // Booking Management Routes
        Route::get('/receptionist/bookings', [ReceptionistBookingController::class, 'index'])->name('receptionist.bookings.index');
        Route::get('/receptionist/bookings/create', [ReceptionistBookingController::class, 'create'])->name('receptionist.bookings.create');
        Route::post('/receptionist/bookings', [ReceptionistBookingController::class, 'store'])->name('receptionist.bookings.store');
        Route::get('/receptionist/bookings/{booking}', [ReceptionistBookingController::class, 'show'])->name('receptionist.bookings.show');
        Route::get('/receptionist/bookings/status/{status}', [ReceptionistBookingController::class, 'filterByStatus'])->name('receptionist.bookings.filter');
        Route::post('/receptionist/bookings/{booking}/confirm', [ReceptionistBookingController::class, 'confirmBooking'])->name('receptionist.bookings.confirm');
        Route::post('/receptionist/bookings/{booking}/cancel', [ReceptionistBookingController::class, 'cancelBooking'])->name('receptionist.bookings.cancel');

        // Payment Routes
        Route::get('/receptionist/bookings/{booking}/payment', [ReceptionistPaymentController::class, 'showPaymentForm'])->name('receptionist.payments.form');
        Route::post('/receptionist/bookings/{booking}/payment', [ReceptionistPaymentController::class, 'recordPayment'])->name('receptionist.payments.record');
        Route::delete('/receptionist/bookings/{booking}/payments/{payment}', [ReceptionistPaymentController::class, 'deletePayment'])->name('receptionist.payments.delete');

        // Refund Request Handling
        Route::post('/receptionist/refund-requests/{refundRequest}/approve', [\App\Http\Controllers\Receptionist\PaymentController::class, 'approveRefund'])->name('receptionist.refund-requests.approve');
        Route::post('/receptionist/refund-requests/{refundRequest}/deny', [\App\Http\Controllers\Receptionist\PaymentController::class, 'denyRefund'])->name('receptionist.refund-requests.deny');
    });


    // Fallback redirect (Foundation)
    Route::fallback(function () {
        return redirect()->route('dashboard');
    });
});
