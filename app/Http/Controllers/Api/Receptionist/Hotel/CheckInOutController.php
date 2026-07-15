<?php

namespace App\Http\Controllers\Api\Receptionist\Hotel;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\HotelReceptionist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckInOutController extends Controller
{
    /**
     * Get the receptionist's assigned hotel
     */
    private function getReceptionistHotel()
    {
        $receptionist = HotelReceptionist::where('user_id', Auth::id())->first();

        if (!$receptionist || $receptionist->status === 'inactive') {
            abort(403, 'You are not assigned to a hotel or your status is inactive.');
        }

        return $receptionist->hotel;
    }

    public function indexCheckIn()
    {
        $hotel = $this->getReceptionistHotel();

        $pendingCheckIns = Booking::where('hotel_id', $hotel->id)
            ->whereIn('status', ['pending', 'confirmed'])
            ->where('check_in_date', '<=', today())
            ->with(['user', 'bookingItems.room'])
            ->orderBy('check_in_date', 'asc')
            ->paginate(10);

        return response()->json([
            'pendingCheckIns' => $pendingCheckIns,
            'hotel' => $hotel,
        ]);
    }

    public function showCheckIn(Booking $booking)
    {
        $hotel = $this->getReceptionistHotel();

        if ($booking->hotel_id !== $hotel->id) {
            abort(403, 'This booking does not belong to your hotel.');
        }

        if (!in_array($booking->status, ['pending', 'confirmed'])) {
            abort(400, 'This booking is not pending check-in.');
        }

        return response()->json([
            'booking' => $booking->load(['user', 'bookingItems.room']),
            'hotel' => $hotel,
        ]);
    }

    public function processCheckIn(Booking $booking, Request $request)
    {
        $hotel = $this->getReceptionistHotel();

        if ($booking->hotel_id !== $hotel->id) {
            abort(403, 'This booking does not belong to your hotel.');
        }

        if (!in_array($booking->status, ['pending', 'confirmed'])) {
            return response()->json(['message' => 'This booking is not pending check-in.'], 422);
        }

        if ($booking->check_in_date > today()) {
            return response()->json(['message' => 'Cannot check-in before the check-in date.'], 422);
        }

        try {
            $booking->update([
                'status' => 'checked_in',
                'notes' => $request->input('notes', $booking->notes),
            ]);

            $booking->bookingItems()->each(function ($item) {
                $item->room->update(['status' => 'Occupied']);
            });

            return response()->json(['message' => "Guest {$booking->user->name} checked in successfully."]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error during check-in: ' . $e->getMessage()], 500);
        }
    }

    public function indexCheckOut()
    {
        $hotel = $this->getReceptionistHotel();

        $pendingCheckOuts = Booking::where('hotel_id', $hotel->id)
            ->where('status', 'checked_in')
            ->with(['user', 'bookingItems.room'])
            ->orderBy('check_out_date', 'asc')
            ->paginate(10);

        return response()->json([
            'pendingCheckOuts' => $pendingCheckOuts,
            'hotel' => $hotel,
        ]);
    }

    public function showCheckOut(Booking $booking)
    {
        $hotel = $this->getReceptionistHotel();

        if ($booking->hotel_id !== $hotel->id) {
            abort(403, 'This booking does not belong to your hotel.');
        }

        if ($booking->status !== 'checked_in') {
            abort(400, 'This booking is not currently checked in.');
        }

        $booking->load(['user', 'bookingItems.room', 'payments']);

        return response()->json([
            'booking' => $booking,
            'hotel' => $hotel,
            'remainingBalance' => $booking->remainingBalance(),
        ]);
    }

    public function processCheckOut(Booking $booking, Request $request)
    {
        $hotel = $this->getReceptionistHotel();

        if ($booking->hotel_id !== $hotel->id) {
            abort(403, 'This booking does not belong to your hotel.');
        }

        if ($booking->status !== 'checked_in') {
            return response()->json(['message' => 'This booking is not checked in.'], 422);
        }

        $remainingBalance = $booking->remainingBalance();
        if ($remainingBalance > 0) {
            return response()->json(['message' => "Cannot check out guest. Outstanding balance of \${$remainingBalance} must be settled first."], 422);
        }

        try {
            $booking->update([
                'status' => 'checked_out',
                'notes' => $request->input('notes', $booking->notes),
            ]);

            $booking->bookingItems()->each(function ($item) {
                $item->room->update(['status' => 'Cleaning']);
            });

            return response()->json(['message' => "Guest {$booking->user->name} checked out successfully. Rooms marked for cleaning."]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error during check-out: ' . $e->getMessage()], 500);
        }
    }
}
