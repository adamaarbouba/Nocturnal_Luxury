<?php

namespace App\Http\Controllers\Api\Guest;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Review;
use Illuminate\Http\JsonResponse;

class DashboardController extends Controller
{
    public function index(): JsonResponse
    {
        $user = auth()->user();

        $currentBookings = Booking::where('user_id', $user->id)
            ->whereIn('status', ['pending', 'confirmed', 'checked_in'])
            ->where('check_in_date', '>=', today())
            ->with('hotel', 'bookingItems.room')
            ->orderBy('check_in_date', 'asc')
            ->get();

        $pastBookings = Booking::where('user_id', $user->id)
            ->whereIn('status', ['completed', 'checked_out', 'cancelled'])
            ->where('check_out_date', '<=', today())
            ->with('hotel', 'bookingItems.room')
            ->orderBy('check_out_date', 'desc')
            ->get();

        $reviewsCount = Review::where('user_id', $user->id)->count();

        return response()->json([
            'currentBookingsCount' => $currentBookings->count(),
            'pastBookingsCount' => $pastBookings->count(),
            'reviewsCount' => $reviewsCount,
            'currentBookings' => $currentBookings,
            'pastBookings' => $pastBookings,
        ]);
    }
}
