<?php

namespace App\Http\Controllers\Api\Receptionist;

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
            // Web version redirects to staff.hotels.index; the SPA handles this flag.
            return response()->json(['redirect' => '/staff/hotels'], 403);
        }

        $hotel = $receptionistData->hotel;

        $todayCheckIns = Booking::where('hotel_id', $hotel->id)
            ->whereIn('status', ['pending', 'confirmed'])
            ->where('check_in_date', '<=', today())
            ->count();

        $todayCheckOuts = Booking::where('hotel_id', $hotel->id)
            ->where('status', 'checked_in')
            ->where('check_out_date', '<=', today())
            ->count();

        $occupiedRooms = Room::where('hotel_id', $hotel->id)
            ->where('status', 'Occupied')
            ->count();

        $pendingRefundRequests = RefundRequest::whereHas('booking', function ($query) use ($hotel) {
                $query->where('hotel_id', $hotel->id);
            })
            ->where('status', 'pending')
            ->with(['booking.user', 'booking.hotel'])
            ->get();

        return response()->json([
            'hotel' => $hotel,
            'todayCheckIns' => $todayCheckIns,
            'todayCheckOuts' => $todayCheckOuts,
            'occupiedRooms' => $occupiedRooms,
            'receptionistStatus' => $receptionistData->status,
            'pendingRefundRequests' => $pendingRefundRequests,
        ]);
    }
}
