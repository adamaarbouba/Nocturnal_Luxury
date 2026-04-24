<?php

namespace App\Http\Controllers\Inspector;

use App\Http\Controllers\Controller;
use App\Models\HotelStaff;
use App\Models\Room;
use App\Models\InspectionRequest;
use App\Models\MaintenanceRequest;
use App\Models\CleaningLog;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Show the inspector dashboard with rooms pending inspection
     */
    public function index(): View
    {
        $user = auth()->user();

        // Get all inspector's hotel assignments
        $staffRoles = HotelStaff::where('user_id', $user->id)
            ->where('role', 'inspector')
            ->with('hotel')
            ->get();

        if ($staffRoles->isEmpty()) {
            // Inspector not assigned to any hotel
            return redirect()->route('staff.hotels.index');
        }

        $hotelIds = $staffRoles->pluck('hotel_id');

        // Get all rooms in 'Inspection' status for all assigned hotels
        $roomsNeedingInspection = Room::whereIn('hotel_id', $hotelIds)
            ->where('status', 'Inspection')
            ->with(['hotel', 'bookingItems.booking'])
            ->orderBy('hotel_id')
            ->orderBy('room_number')
            ->get();

        return view('inspector.dashboard', [
            'roomsNeedingInspection' => $roomsNeedingInspection,
            'staffRoles' => $staffRoles,
        ]);
    }

    /**
     * Show the inspection form where inspector can accept or reject a room
     */
    public function showInspectionForm(Room $room): View
    {
        $user = auth()->user();

        // Verify inspector is assigned to this room's hotel
        $staffRole = HotelStaff::where('user_id', $user->id)
            ->where('role', 'inspector')
            ->where('hotel_id', $room->hotel_id)
            ->first();

        if (!$staffRole) {
            abort(403, 'You do not have permission to access this room.');
        }

        // Only allow inspecting rooms in 'Inspection' status
        if ($room->status !== 'Inspection') {
            abort(403, 'This room is not pending inspection.');
        }

        // Get cleaning history for this room
        $cleaningHistory = CleaningLog::where('room_id', $room->id)
            ->where('type', 'cleaning')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->with('user')
            ->get();

        // Get inspection history for this room
        $inspectionHistory = InspectionRequest::where('room_id', $room->id)
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->with('inspector')
            ->get();

        return view('inspector.inspect-room', [
            'room' => $room,
            'cleaningHistory' => $cleaningHistory,
            'inspectionHistory' => $inspectionHistory,
        ]);
    }

    /**
     * Process inspection result - either approve or reject
     */
    public function completeInspection(Request $request, Room $room): RedirectResponse
    {
        $validated = $request->validate([
            'action' => 'required|in:approved,rejected,maintenance',
            'notes' => 'nullable|string|max:1000',
            'severity' => 'nullable|in:minor,moderate,severe',
            'priority' => 'nullable|in:urgent,normal,low',
        ]);

        $user = auth()->user();

        // Verify inspector is assigned to this room's hotel
        $staffRole = HotelStaff::where('user_id', $user->id)
            ->where('role', 'inspector')
            ->where('hotel_id', $room->hotel_id)
            ->first();

        if (!$staffRole) {
            return redirect()->route('inspector.dashboard')->with('error', 'You do not have permission to inspect this room.');
        }

        // Only allow inspecting rooms in 'Inspection' status
        if ($room->status !== 'Inspection') {
            return redirect()->route('inspector.dashboard')->with('error', 'This room is not pending inspection.');
        }

        $action = $validated['action'];
        $notes = $validated['notes'] ?? '';
        $severity = $validated['severity'] ?? 'minor';
        $priority = $validated['priority'] ?? 'normal';

        // Create inspection request log
        if ($action === 'approved') {
            $newStatus = 'Available';
            $successMsg = 'Room ' . $room->room_number . ' approved for guests. Room is available!';

            InspectionRequest::create([
                'room_id' => $room->id,
                'inspector_id' => $user->id,
                'status' => 'approved',
                'issue_description' => $notes ?: 'Room approved by inspector. Ready for guests.',
                'severity' => 'minor', // Approved rooms have no severity
            ]);
        } elseif ($action === 'maintenance') {
            $newStatus = 'Maintenance';
            $successMsg = 'Room ' . $room->room_number . ' sent to maintenance. Owner will review.';

            // Create maintenance request for owner
            MaintenanceRequest::create([
                'room_id' => $room->id,
                'hotel_id' => $room->hotel_id,
                'created_by_inspector_id' => $user->id,
                'issue_description' => $notes ?: 'Maintenance needed - see inspection notes',
                'priority' => $priority,
                'status' => 'pending',
            ]);

            // Log the inspection decision
            InspectionRequest::create([
                'room_id' => $room->id,
                'inspector_id' => $user->id,
                'status' => 'rejected',
                'issue_description' => $notes ?: 'Maintenance required',
                'severity' => $severity,
            ]);
        } else {
            // rejected action - room goes back to cleaning
            $newStatus = 'Cleaning';
            $successMsg = 'Room ' . $room->room_number . ' rejected. Sent back for re-cleaning.';

            InspectionRequest::create([
                'room_id' => $room->id,
                'inspector_id' => $user->id,
                'status' => 'rejected',
                'issue_description' => $notes ?: 'Issues found during inspection',
                'severity' => $severity,
            ]);

            // Create a cleaning log to track the rejection
            CleaningLog::create([
                'room_id' => $room->id,
                'user_id' => $user->id,
                'type' => 'inspection',
                'message' => 'Inspection rejected by ' . $user->name . '. Issues found: ' . ($notes ?: 'See inspection notes'),
                'notes' => $notes,
                'action' => 're-clean',
            ]);
        }

        // Update room status
        $room->update(['status' => $newStatus]);

        return redirect()->route('inspector.dashboard')->with('success', $successMsg);
    }
}
