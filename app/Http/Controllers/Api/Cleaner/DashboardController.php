<?php

namespace App\Http\Controllers\Api\Cleaner;

use App\Http\Controllers\Controller;
use App\Models\CleaningLog;
use App\Models\HotelStaff;
use App\Models\Room;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(): JsonResponse
    {
        $user = auth()->user();

        $staffRoles = HotelStaff::where('user_id', $user->id)
            ->where('role', 'cleaner')
            ->with('hotel')
            ->get();

        if ($staffRoles->isEmpty()) {
            // Web redirects to staff.hotels.index; SPA handles the redirect client-side.
            return response()->json(['staffRoles' => [], 'roomsNeedsCleaning' => []]);
        }

        $hotelIds = $staffRoles->pluck('hotel_id');

        $roomsNeedsCleaning = Room::whereIn('hotel_id', $hotelIds)
            ->where('status', 'Cleaning')
            ->with(['hotel', 'bookingItems.booking.user'])
            ->orderBy('hotel_id')
            ->orderBy('room_number')
            ->get();

        return response()->json([
            'roomsNeedsCleaning' => $roomsNeedsCleaning,
            'staffRoles' => $staffRoles,
        ]);
    }

    public function showCompletionForm(Room $room): JsonResponse
    {
        $user = auth()->user();

        $staffRole = HotelStaff::where('user_id', $user->id)
            ->where('role', 'cleaner')
            ->where('hotel_id', $room->hotel_id)
            ->first();

        if (!$staffRole) {
            return response()->json(['error' => 'You do not have permission to access this room.'], 403);
        }

        if ($room->status !== 'Cleaning') {
            return response()->json(['error' => 'This room is not in a cleanable status.'], 403);
        }

        $cleaningHistory = CleaningLog::where('room_id', $room->id)
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->with('user')
            ->get();

        return response()->json([
            'room' => $room,
            'cleaningHistory' => $cleaningHistory,
        ]);
    }

    public function completeRoom(Request $request, Room $room): JsonResponse
    {
        $validated = $request->validate([
            'action' => 'required|in:finished,re-clean',
            'notes' => 'nullable|string|max:1000',
        ]);

        $user = auth()->user();

        $staffRole = HotelStaff::where('user_id', $user->id)
            ->where('role', 'cleaner')
            ->where('hotel_id', $room->hotel_id)
            ->first();

        if (!$staffRole) {
            return response()->json(['error' => 'You do not have permission to complete this room.'], 403);
        }

        if ($room->status !== 'Cleaning') {
            return response()->json(['error' => 'This room is not in a cleanable status.'], 403);
        }

        $action = $validated['action'];
        $notes = $validated['notes'] ?? '';

        if ($action === 'finished') {
            $message = 'Cleaning completed by ' . $user->name . '.';
            $newStatus = 'Inspection';
            $successMsg = 'Room ' . $room->room_number . ' marked as complete. Ready for inspection.';
        } else {
            $message = 'Re-cleaning needed by ' . $user->name . '.';
            $newStatus = 'Cleaning';
            $successMsg = 'Room ' . $room->room_number . ' marked for re-cleaning. Your notes have been recorded.';
        }

        CleaningLog::create([
            'room_id' => $room->id,
            'user_id' => $user->id,
            'type' => 'cleaning',
            'message' => $message,
            'notes' => $notes,
            'action' => $action,
        ]);

        $room->update(['status' => $newStatus]);

        return response()->json(['message' => $successMsg]);
    }
}
