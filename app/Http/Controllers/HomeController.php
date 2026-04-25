<?php

namespace App\Http\Controllers;

use App\Models\Hotel;
use App\Models\Booking;
use App\Models\Room;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    public function index()
    {
        $hotels = Hotel::query()
            ->where('status', 'approved')
            ->where('is_verified', true)
            ->with(['rooms' => function ($query) {
                $query->where('status', '!=', 'Disabled');
            }, 'reviews'])
            ->get()
            ->map(function ($hotel) {
                $totalRooms = $hotel->rooms->count();

                if ($totalRooms > 0) {
                    $occupiedRooms = Booking::whereHas('bookingItems', function ($query) use ($hotel) {
                        $query->whereIn('room_id', $hotel->rooms->pluck('id'));
                    })
                        ->where(function ($query) {
                            $query->whereIn('status', ['pending', 'checked_in'])
                                ->whereDate('check_in_date', '<=', now())
                                ->whereDate('check_out_date', '>', now());
                        })
                        ->distinct('id')
                        ->count();

                    $hotel->occupation_rate = ($occupiedRooms / $totalRooms) * 100;
                } else {
                    $hotel->occupation_rate = 0;
                }

                return $hotel;
            })
            ->sortByDesc('occupation_rate')
            ->take(2)
            ->values();

        return view('welcome', compact('hotels'));
    }
}
