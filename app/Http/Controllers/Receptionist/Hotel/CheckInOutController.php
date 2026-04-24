<?php

namespace App\Http\Controllers\Receptionist\Hotel;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\HotelReceptionist;
use App\Models\Room;
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

    /**
     * Display pending check-ins for the hotel
     */
    public function indexCheckIn()
    {
        $hotel = $this->getReceptionistHotel();

        $pendingCheckIns = Booking::where('hotel_id', $hotel->id)
            ->where('status', 'pending')
            ->where('check_in_date', '<=', today())
            ->with(['user', 'bookingItems.room'])
            ->orderBy('check_in_date', 'asc')
            ->paginate(10);

        return view('receptionist.check-in.index', [
            'pendingCheckIns' => $pendingCheckIns,
            'hotel' => $hotel,
        ]);
    }

    /**
     * Show check-in form for a specific booking
     */
    public function showCheckIn(Booking $booking)
    {
        $hotel = $this->getReceptionistHotel();

        // Verify booking belongs to this hotel
        if ($booking->hotel_id !== $hotel->id) {
            abort(403, 'This booking does not belong to your hotel.');
        }

        // Verify booking is pending
        if ($booking->status !== 'pending') {
            abort(400, 'This booking is not pending check-in.');
        }

        return view('receptionist.check-in.show', [
            'booking' => $booking->load(['user', 'bookingItems.room']),
            'hotel' => $hotel,
        ]);
    }

    /**
     * Process check-in
     */
    public function processCheckIn(Booking $booking, Request $request)
    {
        $hotel = $this->getReceptionistHotel();

        // Verify booking belongs to this hotel
        if ($booking->hotel_id !== $hotel->id) {
            abort(403, 'This booking does not belong to your hotel.');
        }

        // Verify booking is pending
        if ($booking->status !== 'pending') {
            return redirect()
                ->route('receptionist.check-in.index')
                ->with('error', 'This booking is not pending check-in.');
        }

        // Check if check-in date is today or earlier
        if ($booking->check_in_date > today()) {
            return redirect()
                ->back()
                ->with('error', 'Cannot check-in before the check-in date.');
        }

        try {
            // Update booking with status and receptionist notes
            $booking->update([
                'status' => 'checked_in',
                'notes' => $request->input('notes', $booking->notes), // Use provided notes or keep existing
            ]);

            // Update room statuses from Reserved/Available to Occupied
            $booking->bookingItems()->each(function ($item) {
                $item->room->update(['status' => 'Occupied']);
            });

            return redirect()
                ->route('receptionist.check-in.index')
                ->with('success', "Guest {$booking->user->name} checked in successfully.");
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Error during check-in: ' . $e->getMessage());
        }
    }

    /**
     * Display pending check-outs for the hotel
     */
    public function indexCheckOut()
    {
        $hotel = $this->getReceptionistHotel();

        $pendingCheckOuts = Booking::where('hotel_id', $hotel->id)
            ->where('status', 'checked_in')
            ->with(['user', 'bookingItems.room'])
            ->orderBy('check_out_date', 'asc')
            ->paginate(10);

        return view('receptionist.check-out.index', [
            'pendingCheckOuts' => $pendingCheckOuts,
            'hotel' => $hotel,
        ]);
    }

    /**
     * Show check-out form for a specific booking
     */
    public function showCheckOut(Booking $booking)
    {
        $hotel = $this->getReceptionistHotel();

        // Verify booking belongs to this hotel
        if ($booking->hotel_id !== $hotel->id) {
            abort(403, 'This booking does not belong to your hotel.');
        }

        // Verify booking is checked in
        if ($booking->status !== 'checked_in') {
            abort(400, 'This booking is not currently checked in.');
        }

        $booking->load(['user', 'bookingItems.room', 'payments']);
        $remainingBalance = $booking->remainingBalance();

        return view('receptionist.check-out.show', [
            'booking' => $booking,
            'hotel' => $hotel,
            'remainingBalance' => $remainingBalance,
        ]);
    }

    /**
     * Process check-out
     */
    public function processCheckOut(Booking $booking, Request $request)
    {
        $hotel = $this->getReceptionistHotel();

        // Verify booking belongs to this hotel
        if ($booking->hotel_id !== $hotel->id) {
            abort(403, 'This booking does not belong to your hotel.');
        }

        // Verify booking is checked in
        if ($booking->status !== 'checked_in') {
            return redirect()
                ->route('receptionist.check-out.index')
                ->with('error', 'This booking is not checked in.');
        }

        // Check if payment is settled
        $remainingBalance = $booking->remainingBalance();
        if ($remainingBalance > 0) {
            return redirect()
                ->back()
                ->with('error', "Cannot check out guest. Outstanding balance of \${$remainingBalance} must be settled first.");
        }

        try {
            // Update booking with status and receptionist notes
            $booking->update([
                'status' => 'checked_out',
                'notes' => $request->input('notes', $booking->notes), // Use provided notes or keep existing
            ]);

            // Update room statuses from Occupied to Cleaning
            $booking->bookingItems()->each(function ($item) {
                $item->room->update(['status' => 'Cleaning']);
            });

            return redirect()
                ->route('receptionist.check-out.index')
                ->with('success', "Guest {$booking->user->name} checked out successfully. Rooms marked for cleaning.");
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Error during check-out: ' . $e->getMessage());
        }
    }
}
