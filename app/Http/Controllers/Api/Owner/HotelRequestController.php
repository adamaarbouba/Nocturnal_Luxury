<?php

namespace App\Http\Controllers\Api\Owner;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreHotelRequestRequest;
use App\Models\HotelRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class HotelRequestController extends Controller
{
    /**
     * Store hotel request
     */
    public function store(StoreHotelRequestRequest $request): JsonResponse
    {
        HotelRequest::create([
            'owner_id' => Auth::id(),
            'name' => $request->name,
            'description' => $request->description,
            'phone' => $request->phone,
            'email' => $request->email,
            'address' => $request->address,
            'city' => $request->city,
            'country' => $request->country,

            'status' => 'pending',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Hotel request submitted! Waiting for admin approval.',
        ]);
    }

    /**
     * View owner's hotel requests
     */
    public function myRequests(): JsonResponse
    {
        $requests = HotelRequest::where('owner_id', Auth::id())
            ->with('reviewer')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return response()->json($requests);
    }
}
