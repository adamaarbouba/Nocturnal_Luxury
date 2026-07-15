<?php

namespace App\Http\Controllers\Api\Owner;

use App\Http\Controllers\Controller;
use App\Models\Hotel;
use App\Models\HotelReceptionist;
use App\Models\HotelStaff;
use App\Models\StaffApplication;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StaffController extends Controller
{
    /**
     * List all staff for a specific hotel
     */
    public function index(Hotel $hotel): JsonResponse
    {
        if ($hotel->owner_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        $hotel->load([
            'staff' => function ($query) {
                $query->whereNull('banned_at');
            },
            'receptionists.user' => function ($query) {
                $query->whereNull('banned_at');
            },
        ]);

        return response()->json(['hotel' => $hotel]);
    }

    /**
     * All staff applications for owner's hotels
     */
    public function applications(): JsonResponse
    {
        $user = Auth::user();
        $hotelIds = Hotel::where('owner_id', $user->id)->pluck('id');

        $pendingApplications = StaffApplication::whereIn('hotel_id', $hotelIds)
            ->pending()
            ->with('user', 'hotel')
            ->orderBy('created_at', 'desc')
            ->get();

        $reviewedApplications = StaffApplication::whereIn('hotel_id', $hotelIds)
            ->whereIn('status', ['approved', 'rejected'])
            ->with('user', 'hotel', 'reviewer')
            ->orderBy('reviewed_at', 'desc')
            ->take(20)
            ->get();

        return response()->json([
            'pendingApplications' => $pendingApplications,
            'reviewedApplications' => $reviewedApplications,
        ]);
    }

    /**
     * Approve a staff application
     */
    public function approveApplication(Request $request, StaffApplication $staffApplication): JsonResponse
    {
        $user = Auth::user();

        $hotel = Hotel::where('id', $staffApplication->hotel_id)
            ->where('owner_id', $user->id)
            ->firstOrFail();

        if ($staffApplication->status !== 'pending') {
            return response()->json(['message' => 'This application has already been processed.'], 422);
        }

        $validated = $request->validate([
            'hourly_rate' => 'required|numeric|min:0.01',
        ]);

        if ($staffApplication->role === 'receptionist') {
            $existingAssignment = HotelReceptionist::where('user_id', $staffApplication->user_id)->first();
            if ($existingAssignment) {
                return response()->json(['message' => 'This receptionist is already assigned to another hotel.'], 422);
            }
        }

        $staffApplication->update([
            'status' => 'approved',
            'hourly_rate' => $validated['hourly_rate'],
            'reviewed_by' => $user->id,
            'reviewed_at' => now(),
        ]);

        if ($staffApplication->role === 'receptionist') {
            HotelReceptionist::create([
                'user_id' => $staffApplication->user_id,
                'hotel_id' => $hotel->id,
                'status' => 'active',
            ]);
        } else {
            // cleaner or inspector
            HotelStaff::create([
                'user_id' => $staffApplication->user_id,
                'hotel_id' => $hotel->id,
                'role' => $staffApplication->role,
                'hourly_rate' => $validated['hourly_rate'],
                'is_available' => true,
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Application approved! ' . $staffApplication->user->name . ' is now assigned to ' . $hotel->name . '.',
        ]);
    }

    /**
     * Reject a staff application
     */
    public function rejectApplication(Request $request, StaffApplication $staffApplication): JsonResponse
    {
        $user = Auth::user();

        Hotel::where('id', $staffApplication->hotel_id)
            ->where('owner_id', $user->id)
            ->firstOrFail();

        if ($staffApplication->status !== 'pending') {
            return response()->json(['message' => 'This application has already been processed.'], 422);
        }

        $staffApplication->update([
            'status' => 'rejected',
            'reviewed_by' => $user->id,
            'reviewed_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Application rejected.',
        ]);
    }

    /**
     * Remove a staff member from a hotel
     */
    public function removeStaff(Hotel $hotel, User $user): JsonResponse
    {
        if ($hotel->owner_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        $removed = HotelStaff::where('hotel_id', $hotel->id)
            ->where('user_id', $user->id)
            ->delete();

        if (!$removed) {
            $removed = HotelReceptionist::where('hotel_id', $hotel->id)
                ->where('user_id', $user->id)
                ->delete();
        }

        if ($removed) {
            return response()->json([
                'success' => true,
                'message' => $user->name . ' has been removed from ' . $hotel->name . '.',
            ]);
        }

        return response()->json(['message' => 'Staff member not found.'], 404);
    }

    /**
     * Update hourly wage for a staff member
     */
    public function updateWage(Request $request, Hotel $hotel, User $user): JsonResponse
    {
        if ($hotel->owner_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        $validated = $request->validate([
            'hourly_rate' => 'required|numeric|min:0.01',
        ]);

        $staffRecord = HotelStaff::where('hotel_id', $hotel->id)
            ->where('user_id', $user->id)
            ->first();

        if (!$staffRecord) {
            return response()->json(['message' => 'Staff record not found.'], 404);
        }

        $staffRecord->update(['hourly_rate' => $validated['hourly_rate']]);

        return response()->json([
            'success' => true,
            'message' => 'Hourly rate updated for ' . $user->name . '.',
        ]);
    }
}
