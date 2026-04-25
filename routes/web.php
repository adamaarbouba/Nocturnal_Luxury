<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Admin\HotelRequestController as AdminHotelRequestController;
use App\Http\Controllers\Admin\HotelController as AdminHotelController;
use App\Http\Controllers\Owner\HotelRequestController as OwnerHotelRequestController;
use App\Http\Controllers\Owner\DashboardController as OwnerDashboardController;
use App\Http\Controllers\Owner\HotelsController as OwnerHotelsController;
use App\Http\Controllers\Owner\MaintenanceController as OwnerMaintenanceController;
use App\Http\Controllers\Owner\StaffController as OwnerStaffController;
use App\Http\Controllers\Staff\HotelBrowseController as StaffHotelBrowseController;
use App\Http\Controllers\Receptionist\Hotel\CheckInOutController as ReceptionistCheckInOutController;
use App\Http\Controllers\Receptionist\DashboardController as ReceptionistDashboardController;
use App\Http\Controllers\Receptionist\BookingController as ReceptionistBookingController;
use App\Http\Controllers\Receptionist\PaymentController as ReceptionistPaymentController;
use App\Http\Controllers\Inspector\DashboardController as InspectorDashboardController;
use App\Http\Controllers\Guest\DashboardController as GuestDashboardController;
use App\Http\Controllers\Guest\HotelsController as GuestHotelsController;
use App\Http\Controllers\Guest\BookingController as GuestBookingController;
use App\Http\Controllers\Guest\ReviewController as GuestReviewController;
use App\Http\Controllers\HomeController;

use \App\Http\Controllers\Cleaner\DashboardController as CleanerDashboardController;
use App\Http\Controllers\Guest\PaymentController as GuestPaymentController;
use Illuminate\Support\Facades\Auth;

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
        return redirect()->route(auth()->user()->dashboardRoute());
    })->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::get('/profile/change-password', [ProfileController::class, 'changePassword'])->name('profile.change-password');
    Route::post('/profile/change-password', [ProfileController::class, 'updatePassword'])->name('profile.update-password');
    Route::delete('/profile', [ProfileController::class, 'delete'])->name('profile.delete');

    Route::middleware('role:staff')->group(function () {
        Route::get('/staff/dashboard', function () {
            return view('staff.dashboard');
        })->name('staff.dashboard');
    });

    Route::middleware('role:admin')->group(function () {
        Route::get('/admin/dashboard', [AdminDashboardController::class, 'index'])->name('admin.dashboard');

        Route::get('/admin/users', [AdminUserController::class, 'index'])->name('admin.users.index');
        Route::get('/admin/users/{user}', [AdminUserController::class, 'show'])->name('admin.users.show');
        Route::post('/admin/users/{user}/ban', [AdminUserController::class, 'ban'])->name('admin.users.ban');
        Route::post('/admin/users/{user}/unban', [AdminUserController::class, 'unban'])->name('admin.users.unban');

        Route::get('/admin/hotel-requests', [AdminHotelRequestController::class, 'index'])->name('admin.hotel-requests.index');
        Route::get('/admin/hotel-requests/{hotelRequest}', [AdminHotelRequestController::class, 'show'])->name('admin.hotel-requests.show');
        Route::post('/admin/hotel-requests/{hotelRequest}/approve', [AdminHotelRequestController::class, 'approve'])->name('admin.hotel-requests.approve');
        Route::post('/admin/hotel-requests/{hotelRequest}/reject', [AdminHotelRequestController::class, 'reject'])->name('admin.hotel-requests.reject');

        Route::get('/admin/hotels/{hotel}', [AdminHotelController::class, 'show'])->name('admin.hotels.show');
    });

    Route::middleware('role:owner')->group(function () {
        Route::get('/owner/dashboard', [OwnerDashboardController::class, 'index'])->name('owner.dashboard');

        Route::get('/owner/hotels', [OwnerHotelsController::class, 'index'])->name('owner.hotels.index');
        Route::get('/owner/hotels/{hotel}', [OwnerHotelsController::class, 'show'])->name('owner.hotels.show');
        Route::get('/owner/hotels/{hotel}/manage', [OwnerHotelsController::class, 'manage'])->name('owner.hotels.manage');
        Route::post('/owner/hotels/{hotel}/add-room', [OwnerHotelsController::class, 'addRoom'])->name('owner.hotels.addRoom');
        Route::post('/owner/hotels/{hotel}/update-room-status', [OwnerHotelsController::class, 'updateRoomStatus'])->name('owner.hotels.updateRoomStatus');
        Route::post('/owner/hotels/{hotel}/rooms/{room}/update', [OwnerHotelsController::class, 'updateRoom'])->name('owner.hotels.updateRoom');
        Route::delete('/owner/hotels/{hotel}/rooms/{room}', [OwnerHotelsController::class, 'deleteRoom'])->name('owner.hotels.deleteRoom');
        Route::patch('/owner/hotels/{hotel}/update', [OwnerHotelsController::class, 'updateHotel'])->name('owner.hotels.update');

        Route::get('/owner/hotel-requests/create', [OwnerHotelRequestController::class, 'create'])->name('owner.hotel-requests.create');
        Route::post('/owner/hotel-requests', [OwnerHotelRequestController::class, 'store'])->name('owner.hotel-requests.store');
        Route::get('/owner/hotel-requests', [OwnerHotelRequestController::class, 'myRequests'])->name('owner.hotel-requests.index');

        Route::get('/owner/hotels/{hotel}/staff', [OwnerStaffController::class, 'index'])->name('owner.staff.index');
        Route::get('/owner/staff/applications', [OwnerStaffController::class, 'applications'])->name('owner.staff.applications');
        Route::post('/owner/staff/applications/{staffApplication}/approve', [OwnerStaffController::class, 'approveApplication'])->name('owner.staff.approve');
        Route::post('/owner/staff/applications/{staffApplication}/reject', [OwnerStaffController::class, 'rejectApplication'])->name('owner.staff.reject');
        Route::delete('/owner/hotels/{hotel}/staff/{user}', [OwnerStaffController::class, 'removeStaff'])->name('owner.staff.remove');
        Route::patch('/owner/hotels/{hotel}/staff/{user}/wage', [OwnerStaffController::class, 'updateWage'])->name('owner.staff.updateWage');

        Route::get('/owner/maintenance', [OwnerMaintenanceController::class, 'index'])->name('owner.maintenance.index');
        Route::get('/owner/maintenance/{maintenanceRequest}', [OwnerMaintenanceController::class, 'show'])->name('owner.maintenance.show');
        Route::post('/owner/maintenance/{maintenanceRequest}/transition', [OwnerMaintenanceController::class, 'transition'])->name('owner.maintenance.transition');
    });

    Route::middleware('role:receptionist')->group(function () {
        Route::get('/receptionist/dashboard', [ReceptionistDashboardController::class, 'index'])->name('receptionist.dashboard');

        Route::get('/receptionist/check-in', [ReceptionistCheckInOutController::class, 'indexCheckIn'])->name('receptionist.check-in.index');
        Route::get('/receptionist/check-in/{booking}', [ReceptionistCheckInOutController::class, 'showCheckIn'])->name('receptionist.check-in.show');
        Route::post('/receptionist/check-in/{booking}', [ReceptionistCheckInOutController::class, 'processCheckIn'])->name('receptionist.check-in.process');

        Route::get('/receptionist/check-out', [ReceptionistCheckInOutController::class, 'indexCheckOut'])->name('receptionist.check-out.index');
        Route::get('/receptionist/check-out/{booking}', [ReceptionistCheckInOutController::class, 'showCheckOut'])->name('receptionist.check-out.show');
        Route::post('/receptionist/check-out/{booking}', [ReceptionistCheckInOutController::class, 'processCheckOut'])->name('receptionist.check-out.process');

        Route::get('/receptionist/bookings', [ReceptionistBookingController::class, 'index'])->name('receptionist.bookings.index');
        Route::get('/receptionist/bookings/create', [ReceptionistBookingController::class, 'create'])->name('receptionist.bookings.create');
        Route::post('/receptionist/bookings', [ReceptionistBookingController::class, 'store'])->name('receptionist.bookings.store');
        Route::get('/receptionist/bookings/{booking}', [ReceptionistBookingController::class, 'show'])->name('receptionist.bookings.show');
        Route::get('/receptionist/bookings/status/{status}', [ReceptionistBookingController::class, 'filterByStatus'])->name('receptionist.bookings.filter');
        Route::post('/receptionist/bookings/{booking}/confirm', [ReceptionistBookingController::class, 'confirmBooking'])->name('receptionist.bookings.confirm');
        Route::post('/receptionist/bookings/{booking}/cancel', [ReceptionistBookingController::class, 'cancelBooking'])->name('receptionist.bookings.cancel');

        Route::get('/receptionist/bookings/{booking}/payment', [ReceptionistPaymentController::class, 'showPaymentForm'])->name('receptionist.payments.form');
        Route::post('/receptionist/bookings/{booking}/payment', [ReceptionistPaymentController::class, 'recordPayment'])->name('receptionist.payments.record');
        Route::delete('/receptionist/bookings/{booking}/payments/{payment}', [ReceptionistPaymentController::class, 'deletePayment'])->name('receptionist.payments.delete');

        Route::post('/receptionist/refund-requests/{refundRequest}/approve', [ReceptionistPaymentController::class, 'approveRefund'])->name('receptionist.refund-requests.approve');
        Route::post('/receptionist/refund-requests/{refundRequest}/deny', [ReceptionistPaymentController::class, 'denyRefund'])->name('receptionist.refund-requests.deny');
    });

    Route::middleware('role:cleaner')->group(function () {
        Route::get('/cleaner/dashboard', [CleanerDashboardController::class, 'index'])->name('cleaner.dashboard');
        Route::get('/cleaner/rooms/{room}/complete', [CleanerDashboardController::class, 'showCompletionForm'])->name('cleaner.rooms.complete-form');
        Route::post('/cleaner/rooms/{room}/complete', [CleanerDashboardController::class, 'completeRoom'])->name('cleaner.rooms.complete');
    });

    Route::middleware('role:inspector')->group(function () {
        Route::get('/inspector/dashboard', [InspectorDashboardController::class, 'index'])->name('inspector.dashboard');
        Route::get('/inspector/rooms/{room}/inspect', [InspectorDashboardController::class, 'showInspectionForm'])->name('inspector.rooms.inspect-form');
        Route::post('/inspector/rooms/{room}/inspect', [InspectorDashboardController::class, 'completeInspection'])->name('inspector.rooms.inspect');
    });

    Route::middleware('role:cleaner,inspector,receptionist')->group(function () {
        Route::get('/staff/hotels', [StaffHotelBrowseController::class, 'index'])->name('staff.hotels.index');
        Route::get('/staff/hotels/{hotel}/apply', [StaffHotelBrowseController::class, 'apply'])->name('staff.hotels.apply');
        Route::post('/staff/hotels/{hotel}/apply', [StaffHotelBrowseController::class, 'storeApplication'])->name('staff.hotels.storeApplication');
        Route::get('/staff/my-applications', [StaffHotelBrowseController::class, 'myApplications'])->name('staff.my-applications');
    });

    Route::middleware('role:guest')->group(function () {
        Route::get('/guest/dashboard', [GuestDashboardController::class, 'index'])->name('guest.dashboard');
    });

    Route::get('/guest/hotels', [GuestHotelsController::class, 'index'])->name('guest.hotels.index');
    Route::get('/guest/hotels/{hotel}', [GuestHotelsController::class, 'show'])->name('guest.hotels.show');
    Route::get('/guest/bookings', [GuestBookingController::class, 'index'])->name('guest.bookings.index');
    Route::get('/guest/rooms/{room}/book', [GuestBookingController::class, 'create'])->name('guest.bookings.create');
    Route::post('/guest/rooms/{room}/book', [GuestBookingController::class, 'store'])->name('guest.bookings.store');
    Route::get('/guest/bookings/{booking}/confirmation', [GuestBookingController::class, 'confirmation'])->name('guest.bookings.confirmation');
    Route::get('/guest/bookings/{booking}/payment', [GuestPaymentController::class, 'showPaymentForm'])->name('guest.payments.form');
    Route::post('/guest/bookings/{booking}/payment', [GuestPaymentController::class, 'processPayment'])->name('guest.payments.process');
    Route::post('/guest/bookings/{booking}/refund-request', [GuestPaymentController::class, 'requestRefund'])->name('guest.refund-requests.store');
    Route::get('/guest/reviews', [GuestReviewController::class, 'index'])->name('guest.reviews.index');
    Route::get('/guest/bookings/{booking}/review', [GuestReviewController::class, 'create'])->name('guest.reviews.create');
    Route::post('/guest/bookings/{booking}/review', [GuestReviewController::class, 'store'])->name('guest.reviews.store');
    Route::get('/guest/reviews/{review}', [GuestReviewController::class, 'show'])->name('guest.reviews.show');

    Route::fallback(function () {
        if (!auth()->check()) {
            return redirect()->route('welcome');
        }

        return redirect()->route(auth()->user()->dashboardRoute());
    });
});
