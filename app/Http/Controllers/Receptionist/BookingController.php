<?php

namespace App\Http\Controllers\Receptionist;

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

    /**
     * Display all bookings for the receptionist's hotel
     */
    public function index()
    {
        $hotel = $this->getReceptionistHotel();

        // Get all bookings for this hotel with status filter
        $bookings = Booking::where('hotel_id', $hotel->id)
            ->with(['user', 'bookingItems.room'])
            ->orderBy('check_in_date', 'desc')
            ->paginate(15);

        // Get booking stats
        $pendingBookings = Booking::where('hotel_id', $hotel->id)
            ->where('status', 'pending')
            ->count();

        $checkedInBookings = Booking::where('hotel_id', $hotel->id)
            ->where('status', 'checked_in')
            ->count();

        $checkedOutBookings = Booking::where('hotel_id', $hotel->id)
            ->where('status', 'checked_out')
            ->count();

        return view('receptionist.bookings.index', [
            'bookings' => $bookings,
            'hotel' => $hotel,
            'pendingBookings' => $pendingBookings,
            'checkedInBookings' => $checkedInBookings,
            'checkedOutBookings' => $checkedOutBookings,
        ]);
    }

    /**
     * Display a specific booking with full details
     */
    public function show(Booking $booking)
    {
        $hotel = $this->getReceptionistHotel();

        // Verify booking belongs to this hotel
        if ($booking->hotel_id !== $hotel->id) {
            abort(403, 'This booking does not belong to your hotel.');
        }

        $booking->load(['user', 'bookingItems.room', 'reviews']);

        return view('receptionist.bookings.show', [
            'booking' => $booking,
            'hotel' => $hotel,
        ]);
    }

    /**
     * Filter bookings by status
     */
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

        $pendingBookings = Booking::where('hotel_id', $hotel->id)
            ->where('status', 'pending')
            ->count();

        $checkedInBookings = Booking::where('hotel_id', $hotel->id)
            ->where('status', 'checked_in')
            ->count();

        $checkedOutBookings = Booking::where('hotel_id', $hotel->id)
            ->where('status', 'checked_out')
            ->count();

        return view('receptionist.bookings.index', [
            'bookings' => $bookings,
            'hotel' => $hotel,
            'currentStatus' => $status,
            'pendingBookings' => $pendingBookings,
            'checkedInBookings' => $checkedInBookings,
            'checkedOutBookings' => $checkedOutBookings,
            'checkedInBookings' => $checkedInBookings,
        ]);
    }

    /**
     * Display form to create a new booking
     */
    public function create()
    {
        $hotel = $this->getReceptionistHotel();

        // Get available rooms for this hotel
        $availableRooms = $hotel->rooms()
            ->where('status', 'Available')
            ->get();

        return view('receptionist.bookings.create', [
            'hotel' => $hotel,
            'availableRooms' => $availableRooms,
        ]);
    }

    /**
     * Store a new booking created by receptionist
     */
    public function store(Request $request)
    {
        $hotel = $this->getReceptionistHotel();

        // Validate request
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
            // Check if guest exists by email, if not create new guest user
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
            } else {
                // Update existing guest info if different
                $guest->update([
                    'name' => $validated['guest_name'],
                    'phone' => $validated['guest_phone'],
                    'address' => $validated['guest_address'],
                ]);
            }

            // Calculate total amount from selected rooms
            $rooms = Room::whereIn('id', $validated['room_ids'])
                ->where('hotel_id', $hotel->id)
                ->get();

            $checkInDate = \Carbon\Carbon::parse($validated['check_in_date']);
            $checkOutDate = \Carbon\Carbon::parse($validated['check_out_date']);
            $nights = $checkOutDate->diffInDays($checkInDate);

            $totalAmount = $rooms->sum(function ($room) use ($nights) {
                return $room->price_per_night * $nights;
            });

            // Create booking
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

            // Create booking items for each room
            foreach ($rooms as $room) {
                $booking->bookingItems()->create([
                    'room_id' => $room->id,
                    'price_per_night' => $room->price_per_night,
                    'quantity' => $nights,
                ]);

                // Mark room as Reserved when booking is created
                $room->update(['status' => 'Reserved']);
            }

            return redirect()->route('receptionist.bookings.show', $booking)
                ->with('success', 'Booking created successfully. Guest: ' . $guest->name);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error creating booking: ' . $e->getMessage());
        }
    }

    /**
     * Confirm a pending booking
     */
    public function confirmBooking(Booking $booking)
    {
        $hotel = $this->getReceptionistHotel();

        if ($booking->hotel_id !== $hotel->id) {
            abort(403, 'This booking does not belong to your hotel.');
        }

        if ($booking->status !== 'pending') {
            return redirect()->back()->with('error', 'Only pending bookings can be confirmed.');
        }

        try {
            $booking->update(['status' => 'confirmed']);

            // Update rooms to Reserved status
            $booking->bookingItems()->each(function ($item) {
                $item->room->update(['status' => 'Reserved']);
            });

            return redirect()->route('receptionist.bookings.show', $booking)
                ->with('success', 'Booking confirmed successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error confirming booking: ' . $e->getMessage());
        }
    }

    /**
     * Cancel a booking
     */
    public function cancelBooking(Booking $booking)
    {
        $hotel = $this->getReceptionistHotel();

        if ($booking->hotel_id !== $hotel->id) {
            abort(403, 'This booking does not belong to your hotel.');
        }

        // Only allow cancelling pending bookings
        if ($booking->status !== 'pending') {
            return redirect()->back()->with('error', 'Only pending bookings can be cancelled. Bookings cannot be cancelled after check-in.');
        }

        try {
            // Release reserved rooms back to available
            $booking->bookingItems()->each(function ($item) {
                if ($item->room->status === 'Reserved') {
                    $item->room->update(['status' => 'Available']);
                }
            });

            $booking->update(['status' => 'cancelled']);

            return redirect()->route('receptionist.bookings.index')
                ->with('success', 'Booking cancelled successfully. Rooms released.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error cancelling booking: ' . $e->getMessage());
        }
    }
}
