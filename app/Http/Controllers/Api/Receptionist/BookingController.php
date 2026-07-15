<?php

namespace App\Http\Controllers\Api\Receptionist;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\HotelReceptionist;
use App\Models\Room;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    /**
     * Get the receptionist's assigned hotel
     */
    private function getReceptionistHotel()
    {
        $receptionist = HotelReceptionist::where('user_id', Auth::id())
            ->with('hotel')
            ->first();

        if (!$receptionist || $receptionist->status === 'inactive') {
            abort(403, 'You are not assigned to a hotel or your status is inactive.');
        }

        return $receptionist->hotel;
    }

    private function stats($hotelId)
    {
        return [
            'pendingBookings' => Booking::where('hotel_id', $hotelId)->where('status', 'pending')->count(),
            'checkedInBookings' => Booking::where('hotel_id', $hotelId)->where('status', 'checked_in')->count(),
            'checkedOutBookings' => Booking::where('hotel_id', $hotelId)->where('status', 'checked_out')->count(),
        ];
    }

    public function index()
    {
        $hotel = $this->getReceptionistHotel();

        $bookings = Booking::where('hotel_id', $hotel->id)
            ->with(['user', 'bookingItems.room'])
            ->orderBy('check_in_date', 'desc')
            ->paginate(15);

        return response()->json([
            'bookings' => $bookings,
            'hotel' => $hotel,
        ] + $this->stats($hotel->id));
    }

    public function show(Booking $booking)
    {
        $hotel = $this->getReceptionistHotel();

        if ($booking->hotel_id !== $hotel->id) {
            abort(403, 'This booking does not belong to your hotel.');
        }

        $booking->load(['user', 'bookingItems.room', 'reviews']);

        return response()->json([
            'booking' => $booking,
            'hotel' => $hotel,
        ]);
    }

    public function filterByStatus($status)
    {
        $hotel = $this->getReceptionistHotel();

        $validStatuses = ['pending', 'checked_in', 'checked_out', 'cancelled'];

        if (!in_array($status, $validStatuses)) {
            abort(400, 'Invalid booking status.');
        }

        $bookings = Booking::where('hotel_id', $hotel->id)
            ->where('status', $status)
            ->with(['user', 'bookingItems.room'])
            ->orderBy('check_in_date', 'desc')
            ->paginate(15);

        return response()->json([
            'bookings' => $bookings,
            'hotel' => $hotel,
            'currentStatus' => $status,
        ] + $this->stats($hotel->id));
    }

    public function create()
    {
        $hotel = $this->getReceptionistHotel();

        $availableRooms = $hotel->rooms()
            ->where('status', 'Available')
            ->get();

        return response()->json([
            'hotel' => $hotel,
            'availableRooms' => $availableRooms,
        ]);
    }

    public function store(Request $request)
    {
        $hotel = $this->getReceptionistHotel();

        $validated = $request->validate([
            'guest_name' => 'required|string|max:255',
            'guest_email' => 'required|email|max:255',
            'guest_phone' => 'required|string|max:20',
            'guest_address' => 'required|string|max:255',
            'check_in_date' => 'required|date|after_or_equal:today',
            'check_out_date' => 'required|date|after:check_in_date',
            'room_ids' => 'required|array|min:1',
            'room_ids.*' => 'exists:rooms,id',
            'special_requests' => 'nullable|string|max:500',
            'notes' => 'nullable|string|max:500',
        ]);

        try {
            $guest = User::where('email', $validated['guest_email'])->first();

            if (!$guest) {
                $guest = User::create([
                    'name' => $validated['guest_name'],
                    'email' => $validated['guest_email'],
                    'password' => Hash::make(Str::password()),
                    'phone' => $validated['guest_phone'],
                    'address' => $validated['guest_address'],
                    'role_id' => Role::where('slug', 'guest')->first()->id,
                    'email_verified_at' => now(),
                ]);
            }
            // ponytail: existing user found by email is used as-is — do not overwrite
            // their profile fields, that would let a receptionist clobber an unrelated account.

            $rooms = Room::whereIn('id', $validated['room_ids'])
                ->where('hotel_id', $hotel->id)
                ->get();

            $checkInDate = \Carbon\Carbon::parse($validated['check_in_date']);
            $checkOutDate = \Carbon\Carbon::parse($validated['check_out_date']);
            $nights = $checkOutDate->diffInDays($checkInDate);

            $totalAmount = $rooms->sum(function ($room) use ($nights) {
                return $room->price_per_night * $nights;
            });

            $booking = Booking::create([
                'user_id' => $guest->id,
                'hotel_id' => $hotel->id,
                'check_in_date' => $validated['check_in_date'],
                'check_out_date' => $validated['check_out_date'],
                'status' => 'pending',
                'payment_status' => 'pending',
                'total_amount' => abs($totalAmount),
                'special_requests' => $validated['special_requests'] ?? null,
                'notes' => $validated['notes'] ?? null,
            ]);

            foreach ($rooms as $room) {
                $booking->bookingItems()->create([
                    'room_id' => $room->id,
                    'price_per_night' => $room->price_per_night,
                    'quantity' => $nights,
                ]);

                $room->update(['status' => 'Reserved']);
            }

            return response()->json([
                'message' => 'Booking created successfully. Guest: ' . $guest->name,
                'booking' => $booking,
            ], 201);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error creating booking: ' . $e->getMessage()], 500);
        }
    }

    public function confirmBooking(Booking $booking)
    {
        $hotel = $this->getReceptionistHotel();

        if ($booking->hotel_id !== $hotel->id) {
            abort(403, 'This booking does not belong to your hotel.');
        }

        if ($booking->status !== 'pending') {
            return response()->json(['message' => 'Only pending bookings can be confirmed.'], 422);
        }

        try {
            $booking->update(['status' => 'confirmed']);

            $booking->bookingItems()->each(function ($item) {
                $item->room->update(['status' => 'Reserved']);
            });

            return response()->json(['message' => 'Booking confirmed successfully.', 'booking' => $booking]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error confirming booking: ' . $e->getMessage()], 500);
        }
    }

    public function cancelBooking(Booking $booking)
    {
        $hotel = $this->getReceptionistHotel();

        if ($booking->hotel_id !== $hotel->id) {
            abort(403, 'This booking does not belong to your hotel.');
        }

        if ($booking->status !== 'pending') {
            return response()->json(['message' => 'Only pending bookings can be cancelled. Bookings cannot be cancelled after check-in.'], 422);
        }

        try {
            $booking->bookingItems()->each(function ($item) {
                if ($item->room->status === 'Reserved') {
                    $item->room->update(['status' => 'Available']);
                }
            });

            $booking->update(['status' => 'cancelled']);

            return response()->json(['message' => 'Booking cancelled successfully. Rooms released.']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error cancelling booking: ' . $e->getMessage()], 500);
        }
    }
}
