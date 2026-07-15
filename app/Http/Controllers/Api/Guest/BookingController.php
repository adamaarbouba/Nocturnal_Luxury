<?php

namespace App\Http\Controllers\Api\Guest;

use App\Http\Controllers\Controller;
use App\Models\Room;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class BookingController extends Controller
{
    public function index(): JsonResponse
    {
        $user = auth()->user();

        $currentBookings = Booking::where('user_id', $user->id)
            ->whereIn('status', ['pending', 'confirmed', 'checked_in'])
            ->with(['hotel', 'bookingItems.room'])
            ->orderBy('check_in_date', 'desc')
            ->get()
            ->map(function ($booking) {
                $booking->remaining_balance = $booking->remainingBalance();
                return $booking;
            });

        $pastBookings = Booking::where('user_id', $user->id)
            ->whereIn('status', ['completed', 'checked_out', 'cancelled'])
            ->with(['hotel', 'bookingItems.room'])
            ->orderBy('check_out_date', 'desc')
            ->get();

        return response()->json([
            'currentBookings' => $currentBookings,
            'pastBookings' => $pastBookings,
        ]);
    }

    public function create(Room $room): JsonResponse
    {
        $room->load('hotel');

        return response()->json(['room' => $room]);
    }

    public function store(Request $request, Room $room): JsonResponse
    {
        $validated = $request->validate([
            'check_in_date' => 'required|date|after:today',
            'check_out_date' => 'required|date|after:check_in_date',
            'special_requests' => 'nullable|string|max:500',
        ], [
            'check_in_date.after' => 'Check-in date must be in the future',
            'check_out_date.after' => 'Check-out date must be after check-in date',
        ]);

        $checkInDate = new \DateTime($validated['check_in_date']);
        $checkOutDate = new \DateTime($validated['check_out_date']);
        $nights = $checkOutDate->diff($checkInDate)->days;
        $totalAmount = $nights * $room->price_per_night;

        $booking = Booking::create([
            'user_id' => auth()->id(),
            'hotel_id' => $room->hotel_id,
            'check_in_date' => $validated['check_in_date'],
            'check_out_date' => $validated['check_out_date'],
            'total_amount' => $totalAmount,
            'special_requests' => $validated['special_requests'] ?? null,
            'status' => 'pending',
            'payment_status' => 'pending',
        ]);

        $booking->bookingItems()->create([
            'room_id' => $room->id,
            'quantity' => $nights,
            'price_per_night' => $room->price_per_night,
        ]);

        return response()->json([
            'message' => 'Booking created! Please complete payment.',
            'booking' => $booking,
        ], 201);
    }

    public function confirmation(Booking $booking): JsonResponse
    {
        if ($booking->user_id !== auth()->id()) {
            abort(403, 'Unauthorized');
        }

        $booking->load('hotel', 'bookingItems.room');

        return response()->json(['booking' => $booking]);
    }
}
