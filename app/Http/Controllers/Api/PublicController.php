<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Hotel;

class PublicController extends Controller
{
    // JSON version of HomeController@index (welcome page data)
    public function home()
    {
        $hotels = Hotel::query()
            ->where('status', 'approved')
            ->where('is_verified', true)
            ->with(['rooms' => function ($query) {
                $query->where('status', '!=', 'Disabled');
            }, 'reviews.user'])
            ->get()
            ->map(function ($hotel) {
                $totalRooms = $hotel->rooms->count();

                if ($totalRooms > 0) {
                    $occupiedRooms = Booking::whereHas('bookingItems', function ($query) use ($hotel) {
                        $query->whereIn('room_id', $hotel->rooms->pluck('id'));
                    })
                        ->where(function ($query) {
                            $query->whereIn('status', ['pending', 'confirmed', 'checked_in'])
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

        $reviews = $hotels
            ->flatMap(function ($hotel) {
                return $hotel->reviews->map(fn ($review) => [
                    'rating' => $review->rating,
                    'comment' => $review->comment,
                    'user_name' => $review->user->name ?? 'Guest',
                    'hotel_name' => $hotel->name,
                    'created_at' => $review->created_at,
                ]);
            })
            ->sortByDesc('created_at')
            ->take(3)
            ->values();

        return response()->json([
            'hotels' => $hotels->map(fn ($hotel) => [
                'id' => $hotel->id,
                'name' => $hotel->name,
                'city' => $hotel->city,
                'country' => $hotel->country,
                'description' => $hotel->description,
                'occupation_rate' => $hotel->occupation_rate,
                'avg_rating' => $hotel->reviews->avg('rating') ?? 0,
                'review_count' => $hotel->reviews->count(),
                'room_count' => $hotel->rooms->count(),
                'min_price' => $hotel->rooms->min('price_per_night'),
                'max_price' => $hotel->rooms->max('price_per_night'),
            ]),
            'reviews' => $reviews,
        ]);
    }
}
