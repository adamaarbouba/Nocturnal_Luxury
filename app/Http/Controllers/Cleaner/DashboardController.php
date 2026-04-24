<?php

namespace App\Http\Controllers\Cleaner;

use App\Http\Controllers\Controller;
use App\Models\HotelStaff;
use App\Models\Room;
use App\Models\CleaningLog;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Show the cleaner dashboard with rooms needing cleaning
     */
    public function index(): View
    {
        $user = auth()->user();

        // Get all cleaner's hotel assignments
        $staffRoles = HotelStaff::where('user_id', $user->id)
            ->where('role', 'cleaner')
            ->with('hotel')
            ->get();

        if ($staffRoles->isEmpty()) {
            // Cleaner not assigned to any hotel
            return redirect()->route('staff.hotels.index');
        }

        $hotelIds = $staffRoles->pluck('hotel_id');

        // Get all rooms in 'Cleaning' status for all assigned hotels
        $roomsNeedsCleaning = Room::whereIn('hotel_id', $hotelIds)
            ->where('status', 'Cleaning')
            ->with(['hotel', 'bookingItems.booking'])
            ->orderBy('hotel_id')
            ->orderBy('room_number')
            ->get();

        return view('cleaner.dashboard', [
            'roomsNeedsCleaning' => $roomsNeedsCleaning,
            'staffRoles' => $staffRoles,
        ]);
    }

    /**
     * Show the cleaning completion form where cleaner can add notes
     */
    public function showCompletionForm(Room $room): View
    {
        $user = auth()->user();

        // Verify cleaner is assigned to this room's hotel
        $staffRole = HotelStaff::where('user_id', $user->id)
            ->where('role', 'cleaner')
            ->where('hotel_id', $room->hotel_id)
            ->first();

        if (!$staffRole) {
            abort(403, 'You do not have permission to access this room.');
        }

        // Only allow completing rooms in 'Cleaning' status
        if ($room->status !== 'Cleaning') {
            abort(403, 'This room is not in a cleanable status.');
        }

        // Get previous cleaning and inspection logs for this room
        $cleaningHistory = CleaningLog::where('room_id', $room->id)
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->with('user')
            ->get();

        return view('cleaner.complete-room', [
            'room' => $room,
            'cleaningHistory' => $cleaningHistory,
        ]);
    }

    /**
     * Process room completion - either mark as finished (Inspection) or re-clean (stays Cleaning)
     */
    public function completeRoom(Request $request, Room $room): RedirectResponse
    {
        $validated = $request->validate([
            'action' => 'required|in:finished,re-clean',
            'notes' => 'nullable|string|max:1000',
        ]);

        $user = auth()->user();

        // Verify cleaner is assigned to this room's hotel
        $staffRole = HotelStaff::where('user_id', $user->id)
            ->where('role', 'cleaner')
            ->where('hotel_id', $room->hotel_id)
            ->first();

        if (!$staffRole) {
            return redirect()->route('cleaner.dashboard')->with('error', 'You do not have permission to complete this room.');
        }

        // Only allow completing rooms in 'Cleaning' status
        if ($room->status !== 'Cleaning') {
            return redirect()->route('cleaner.dashboard')->with('error', 'This room is not in a cleanable status.');
        }

        $action = $validated['action'];
        $notes = $validated['notes'] ?? '';

        // Determine the message based on action
        if ($action === 'finished') {
            $message = 'Cleaning completed by ' . $user->name . '.';
            $newStatus = 'Inspection';
            $successMsg = 'Room ' . $room->room_number . ' marked as complete. Ready for inspection.';
        } else {
            // re-clean action
            $message = 'Re-cleaning needed by ' . $user->name . '.';
            $newStatus = 'Cleaning'; // Stays in Cleaning
            $successMsg = 'Room ' . $room->room_number . ' marked for re-cleaning. Your notes have been recorded.';
        }

        // Create cleaning log entry with separate notes storage
        CleaningLog::create([
            'room_id' => $room->id,
            'user_id' => $user->id,
            'type' => 'cleaning',
            'message' => $message,
            'notes' => $notes,
            'action' => $action,
        ]);

        // Update room status
        $room->update(['status' => $newStatus]);

        return redirect()->route('cleaner.dashboard')->with('success', $successMsg);
    }
}
