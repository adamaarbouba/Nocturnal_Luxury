<?php

namespace App\Http\Controllers\Receptionist;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\HotelReceptionist;
use App\Models\Room;
use App\Models\RefundRequest;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $receptionistData = HotelReceptionist::where('user_id', Auth::id())
            ->with('hotel')
            ->first();

        if (!$receptionistData || $receptionistData->status === 'inactive') {
            return redirect()->route('staff.hotels.index');
        }

        $hotel = $receptionistData->hotel;

        // Get today's check-ins (pending bookings with check-in date <= today)
        $todayCheckIns = Booking::where('hotel_id', $hotel->id)
            ->where('status', 'pending')
            ->where('check_in_date', '<=', today())
            ->count();

        // Get today's check-outs (checked-in bookings with check-out date <= today)
        $todayCheckOuts = Booking::where('hotel_id', $hotel->id)
            ->where('status', 'checked_in')
            ->where('check_out_date', '<=', today())
            ->count();

        $occupiedRooms = Room::where('hotel_id', $hotel->id)
            ->where('status', 'Occupied')
            ->count();

        // Get pending refund requests for this hotel
        $pendingRefundRequests = RefundRequest::whereHas('booking', function($query) use ($hotel) {
                $query->where('hotel_id', $hotel->id);
            })
            ->where('status', 'pending')
            ->with(['booking.user', 'booking.hotel'])
            ->get();

        return view('receptionist.dashboard', [
            'hotel' => $hotel,
            'todayCheckIns' => $todayCheckIns,
            'todayCheckOuts' => $todayCheckOuts,
            'occupiedRooms' => $occupiedRooms,
            'receptionistStatus' => $receptionistData->status,
            'pendingRefundRequests' => $pendingRefundRequests,
        ]);
    }
}
