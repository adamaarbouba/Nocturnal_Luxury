<?php

namespace App\Http\Controllers\Api\Owner;

use App\Http\Controllers\Controller;
use App\Models\Room;
use App\Models\Booking;
use App\Models\HotelRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Owner dashboard data (JSON mirror of Owner\DashboardController@index)
     */
    public function index(): JsonResponse
    {
        $user = Auth::user();

        $hotels = $user->ownedHotels()->with('rooms')->get();

        $totalHotels = $hotels->count();
        $totalRooms = $hotels->sum(fn($hotel) => $hotel->rooms->count());

        $roomStatusCounts = [
            'available' => Room::whereIn('hotel_id', $hotels->pluck('id'))->where('status', 'Available')->count(),
            'occupied' => Room::whereIn('hotel_id', $hotels->pluck('id'))->where('status', 'Occupied')->count(),
            'cleaning' => Room::whereIn('hotel_id', $hotels->pluck('id'))->where('status', 'Cleaning')->count(),
            'inspection' => Room::whereIn('hotel_id', $hotels->pluck('id'))->where('status', 'Inspection')->count(),
            'maintenance' => Room::whereIn('hotel_id', $hotels->pluck('id'))->where('status', 'Maintenance')->count(),
            'disabled' => Room::whereIn('hotel_id', $hotels->pluck('id'))->where('status', 'Disabled')->count(),
        ];

        $occupiedRooms = $roomStatusCounts['occupied'];
        $reservedRooms = Room::whereIn('hotel_id', $hotels->pluck('id'))->where('status', 'Reserved')->count();
        $availableRoomsTotal = $totalRooms - $roomStatusCounts['disabled'];
        $occupancyRate = $availableRoomsTotal > 0 ? round((($occupiedRooms + $reservedRooms) / $availableRoomsTotal) * 100) : 0;

        $totalBookings = Booking::whereIn('hotel_id', $hotels->pluck('id'))->count();

        $stats = [
            'totalHotels' => $totalHotels,
            'totalRooms' => $totalRooms,
            'totalBookings' => $totalBookings,
            'occupancyRate' => $occupancyRate,
            'roomStatusCounts' => $roomStatusCounts,
        ];

        $recentHotels = $hotels->map(function ($hotel) {
            $totalRoomsInHotel = $hotel->rooms->count();
            $occupiedRooms = $hotel->rooms->where('status', 'Occupied')->count();
            $reservedRooms = $hotel->rooms->where('status', 'Reserved')->count();
            $disabledRooms = $hotel->rooms->where('status', 'Disabled')->count();
            $availableRoomsInHotel = $totalRoomsInHotel - $disabledRooms;
            $occupancyRatePerHotel = $availableRoomsInHotel > 0 ? round((($occupiedRooms + $reservedRooms) / $availableRoomsInHotel) * 100) : 0;

            return [
                'id' => $hotel->id,
                'name' => $hotel->name,
                'location' => $hotel->city . ', ' . $hotel->country,
                'rooms' => $totalRoomsInHotel,
                'bookings' => Booking::where('hotel_id', $hotel->id)->count(),
                'occupancy_rate' => $occupancyRatePerHotel,
            ];
        })->sortByDesc('bookings')->take(10)->values();

        $hotelRequests = [
            'pending' => HotelRequest::where('owner_id', $user->id)->where('status', 'pending')->count(),
            'approved' => HotelRequest::where('owner_id', $user->id)->where('status', 'approved')->count(),
            'rejected' => HotelRequest::where('owner_id', $user->id)->where('status', 'rejected')->count(),
        ];

        return response()->json([
            'stats' => $stats,
            'recentHotels' => $recentHotels,
            'hotelRequests' => $hotelRequests,
        ]);
    }
}
