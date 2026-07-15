<?php

namespace App\Http\Controllers\Api\Inspector;

use App\Http\Controllers\Controller;
use App\Models\CleaningLog;
use App\Models\HotelStaff;
use App\Models\InspectionRequest;
use App\Models\MaintenanceRequest;
use App\Models\Room;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(): JsonResponse
    {
        $user = auth()->user();

        $staffRoles = HotelStaff::where('user_id', $user->id)
            ->where('role', 'inspector')
            ->with('hotel')
            ->get();

        if ($staffRoles->isEmpty()) {
            // Web redirects to staff.hotels.index; SPA handles the redirect client-side.
            return response()->json(['staffRoles' => [], 'roomsNeedingInspection' => []]);
        }

        $hotelIds = $staffRoles->pluck('hotel_id');

        $roomsNeedingInspection = Room::whereIn('hotel_id', $hotelIds)
            ->where('status', 'Inspection')
            ->with([
                'hotel',
                'cleaningLogs' => fn ($q) => $q->where('type', 'cleaning')->latest()->with('user'),
            ])
            ->orderBy('hotel_id')
            ->orderBy('room_number')
            ->get();

        return response()->json([
            'roomsNeedingInspection' => $roomsNeedingInspection,
            'staffRoles' => $staffRoles,
        ]);
    }

    public function showInspectionForm(Room $room): JsonResponse
    {
        $user = auth()->user();

        $staffRole = HotelStaff::where('user_id', $user->id)
            ->where('role', 'inspector')
            ->where('hotel_id', $room->hotel_id)
            ->first();

        if (!$staffRole) {
            return response()->json(['error' => 'You do not have permission to access this room.'], 403);
        }

        if ($room->status !== 'Inspection') {
            return response()->json(['error' => 'This room is not pending inspection.'], 403);
        }

        $cleaningHistory = CleaningLog::where('room_id', $room->id)
            ->where('type', 'cleaning')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->with('user')
            ->get();

        $inspectionHistory = InspectionRequest::where('room_id', $room->id)
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->with('inspector')
            ->get();

        return response()->json([
            'room' => $room->load('hotel'),
            'cleaningHistory' => $cleaningHistory,
            'inspectionHistory' => $inspectionHistory,
        ]);
    }

    public function completeInspection(Request $request, Room $room): JsonResponse
    {
        $validated = $request->validate([
            'action' => 'required|in:approved,rejected,maintenance',
            'notes' => 'nullable|string|max:1000',
            'severity' => 'nullable|in:minor,moderate,severe',
            'priority' => 'nullable|in:urgent,normal,low',
        ]);

        $user = auth()->user();

        $staffRole = HotelStaff::where('user_id', $user->id)
            ->where('role', 'inspector')
            ->where('hotel_id', $room->hotel_id)
            ->first();

        if (!$staffRole) {
            return response()->json(['error' => 'You do not have permission to inspect this room.'], 403);
        }

        if ($room->status !== 'Inspection') {
            return response()->json(['error' => 'This room is not pending inspection.'], 403);
        }

        $action = $validated['action'];
        $notes = $validated['notes'] ?? '';
        $severity = $validated['severity'] ?? 'minor';
        $priority = $validated['priority'] ?? 'normal';

        if ($action === 'approved') {
            $newStatus = 'Available';
            $successMsg = 'Room ' . $room->room_number . ' approved for guests. Room is available!';

            InspectionRequest::create([
                'room_id' => $room->id,
                'inspector_id' => $user->id,
                'status' => 'approved',
                'issue_description' => $notes ?: 'Room approved by inspector. Ready for guests.',
                'severity' => 'minor',
            ]);
        } elseif ($action === 'maintenance') {
            $newStatus = 'Maintenance';
            $successMsg = 'Room ' . $room->room_number . ' sent to maintenance. Owner will review.';

            MaintenanceRequest::create([
                'room_id' => $room->id,
                'hotel_id' => $room->hotel_id,
                'created_by_inspector_id' => $user->id,
                'issue_description' => $notes ?: 'Maintenance needed - see inspection notes',
                'priority' => $priority,
                'status' => 'pending',
            ]);

            InspectionRequest::create([
                'room_id' => $room->id,
                'inspector_id' => $user->id,
                'status' => 'rejected',
                'issue_description' => $notes ?: 'Maintenance required',
                'severity' => $severity,
            ]);
        } else {
            $newStatus = 'Cleaning';
            $successMsg = 'Room ' . $room->room_number . ' rejected. Sent back for re-cleaning.';

            InspectionRequest::create([
                'room_id' => $room->id,
                'inspector_id' => $user->id,
                'status' => 'rejected',
                'issue_description' => $notes ?: 'Issues found during inspection',
                'severity' => $severity,
            ]);

            CleaningLog::create([
                'room_id' => $room->id,
                'user_id' => $user->id,
                'type' => 'inspection',
                'message' => 'Inspection rejected by ' . $user->name . '. Issues found: ' . ($notes ?: 'See inspection notes'),
                'notes' => $notes,
                'action' => 're-clean',
            ]);
        }

        $room->update(['status' => $newStatus]);

        return response()->json(['message' => $successMsg]);
    }
}
