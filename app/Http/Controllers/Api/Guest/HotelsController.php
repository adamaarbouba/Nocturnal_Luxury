<?php

namespace App\Http\Controllers\Api\Guest;

use App\Http\Controllers\Controller;
use App\Models\Hotel;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class HotelsController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Hotel::where('status', 'approved')
            ->where('is_verified', true);

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('city', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->filled('min_price') && $request->filled('max_price')) {
            $minPrice = (float) $request->input('min_price');
            $maxPrice = (float) $request->input('max_price');

            $query->whereHas('rooms', function ($q) use ($minPrice, $maxPrice) {
                $q->whereBetween('price_per_night', [$minPrice, $maxPrice])
                    ->where('status', 'Available');
            });
        }

        if ($request->filled('city')) {
            $query->where('city', $request->input('city'));
        }

        // Per-card stats the Blade view computed inline
        $hotels = $query
            ->withCount(['rooms as available_rooms_count' => fn ($q) => $q->where('status', 'Available')])
            ->withMin(['rooms as min_price' => fn ($q) => $q->where('status', 'Available')], 'price_per_night')
            ->paginate(12);

        $cities = Hotel::where('status', 'approved')
            ->where('is_verified', true)
            ->distinct()
            ->pluck('city')
            ->sort()
            ->values();

        return response()->json([
            'hotels' => $hotels,
            'cities' => $cities,
        ]);
    }

    public function show(Hotel $hotel): JsonResponse
    {
        $hotel->load('reviews.user');
        $availableRooms = $hotel->rooms()
            ->where('status', 'Available')
            ->get();

        return response()->json([
            'hotel' => $hotel,
            'availableRooms' => $availableRooms,
        ]);
    }
}
