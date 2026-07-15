<?php

namespace App\Http\Controllers\Api\Staff;

use App\Http\Controllers\Controller;
use App\Models\Hotel;
use App\Models\HotelReceptionist;
use App\Models\HotelStaff;
use App\Models\StaffApplication;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HotelBrowseController extends Controller
{
    private function getStaffRole(): string
    {
        return Auth::user()->role->slug;
    }

    public function index(): JsonResponse
    {
        $user = Auth::user();
        $role = $this->getStaffRole();

        $hotels = Hotel::where('status', 'approved')
            ->where('is_verified', true)
            ->withCount('rooms')
            ->get();

        $myApplications = StaffApplication::where('user_id', $user->id)
            ->pluck('status', 'hotel_id');

        $workingAtHotels = [];

        if ($role === 'receptionist') {
            $existing = HotelReceptionist::where('user_id', $user->id)->first();
            if ($existing) {
                $workingAtHotels[] = $existing->hotel_id;
            }
        } else {
            $workingAtHotels = HotelStaff::where('user_id', $user->id)
                ->where('role', $role)
                ->pluck('hotel_id')
                ->toArray();
        }

        $isReceptionistBlocked = false;
        if ($role === 'receptionist') {
            $isReceptionistBlocked = HotelReceptionist::where('user_id', $user->id)->exists();
        }

        return response()->json(compact('hotels', 'myApplications', 'workingAtHotels', 'role', 'isReceptionistBlocked'));
    }

    public function apply(Hotel $hotel): JsonResponse
    {
        $user = Auth::user();
        $role = $this->getStaffRole();

        if ($role === 'receptionist') {
            $existing = HotelReceptionist::where('user_id', $user->id)->first();
            if ($existing) {
                return response()->json(['error' => 'You are already assigned to a hotel. Receptionists can only work at one hotel.'], 409);
            }
        }

        if ($role === 'receptionist') {
            $alreadyWorking = HotelReceptionist::where('user_id', $user->id)
                ->where('hotel_id', $hotel->id)->exists();
        } else {
            $alreadyWorking = HotelStaff::where('user_id', $user->id)
                ->where('hotel_id', $hotel->id)
                ->where('role', $role)->exists();
        }

        if ($alreadyWorking) {
            return response()->json(['error' => 'You are already working at this hotel.'], 409);
        }

        $existingApplication = StaffApplication::where('user_id', $user->id)
            ->where('hotel_id', $hotel->id)
            ->where('role', $role)
            ->where('status', 'pending')
            ->exists();

        if ($existingApplication) {
            return response()->json(['error' => 'You already have a pending application for this hotel.'], 409);
        }

        return response()->json(['hotel' => $hotel->loadCount('rooms'), 'role' => $role]);
    }

    public function storeApplication(Request $request, Hotel $hotel): JsonResponse
    {
        $user = Auth::user();
        $role = $this->getStaffRole();

        $validated = $request->validate([
            'message' => 'nullable|string|max:500',
        ]);

        if ($role === 'receptionist') {
            if (HotelReceptionist::where('user_id', $user->id)->exists()) {
                return response()->json(['error' => 'Receptionists can only work at one hotel.'], 409);
            }
        }

        $existing = StaffApplication::where('user_id', $user->id)
            ->where('hotel_id', $hotel->id)
            ->where('role', $role)
            ->where('status', 'pending')
            ->exists();

        if ($existing) {
            return response()->json(['error' => 'You already have a pending application for this hotel.'], 409);
        }

        StaffApplication::create([
            'user_id' => $user->id,
            'hotel_id' => $hotel->id,
            'role' => $role,
            'status' => 'pending',
            'message' => $validated['message'],
        ]);

        return response()->json(['message' => 'Application submitted! The hotel owner will review your application.'], 201);
    }

    public function myApplications(): JsonResponse
    {
        $applications = StaffApplication::where('user_id', Auth::id())
            ->with('hotel', 'reviewer')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json(['applications' => $applications]);
    }
}
