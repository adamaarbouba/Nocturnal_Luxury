<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreHotelRequestRequest;
use App\Models\HotelRequest;
use Illuminate\Support\Facades\Auth;

class HotelRequestController extends Controller
{
    /**
     * Show form to request a new hotel
     */
    public function create()
    {
        return view('owner.hotel-requests.create');
    }

    /**
     * Store hotel request
     */
    public function store(StoreHotelRequestRequest $request)
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

        return redirect()
            ->route('owner.dashboard')
            ->with('success', 'Hotel request submitted! Waiting for admin approval.');
    }

    /**
     * View owner's hotel requests
     */
    public function myRequests()
    {
        $requests = HotelRequest::where('owner_id', Auth::id())
            ->with('reviewer')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('owner.hotel-requests.my-requests', [
            'requests' => $requests,
        ]);
    }
}
