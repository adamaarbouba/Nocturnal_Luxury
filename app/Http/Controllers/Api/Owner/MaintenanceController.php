<?php

namespace App\Http\Controllers\Api\Owner;

use App\Http\Controllers\Controller;
use App\Models\Hotel;
use App\Models\MaintenanceRequest;
use App\Models\Room;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MaintenanceController extends Controller
{
    /**
     * Maintenance dashboard data for owner
     */
    public function index(): JsonResponse
    {
        $user = auth()->user();

        $hotels = Hotel::where('owner_id', $user->id)->get();
        $hotelIds = $hotels->pluck('id');

        $maintenanceRequests = MaintenanceRequest::whereIn('hotel_id', $hotelIds)
            ->with(['room', 'hotel', 'inspector'])
            ->orderBy('created_at', 'desc')
            ->get();

        $maintenanceRooms = Room::whereIn('hotel_id', $hotelIds)
            ->where('status', 'Maintenance')
            ->with('hotel')
            ->orderBy('room_number')
            ->get();

        return response()->json([
            'maintenanceRequests' => $maintenanceRequests,
            'maintenanceRooms' => $maintenanceRooms,
            'hotels' => $hotels,
        ]);
    }

    /**
     * Maintenance details
     */
    public function show(MaintenanceRequest $maintenanceRequest): JsonResponse
    {
        $user = auth()->user();

        $hotel = Hotel::where('id', $maintenanceRequest->hotel_id)
            ->where('owner_id', $user->id)
            ->firstOrFail();

        $maintenanceRequest->load(['room', 'hotel', 'inspector']);

        return response()->json([
            'maintenanceRequest' => $maintenanceRequest,
            'hotel' => $hotel,
        ]);
    }

    /**
     * Transition maintenance room to next status
     */
    public function transition(Request $request, MaintenanceRequest $maintenanceRequest): JsonResponse
    {
        $user = auth()->user();

        $hotel = Hotel::where('id', $maintenanceRequest->hotel_id)
            ->where('owner_id', $user->id)
            ->firstOrFail();

        $validated = $request->validate([
            'next_status' => 'required|in:Cleaning,Available',
            'completion_notes' => 'nullable|string|max:1000',
        ]);

        $room = $maintenanceRequest->room;
        $newStatus = $validated['next_status'];
        $completionNotes = $validated['completion_notes'] ?? '';

        $maintenanceRequest->update([
            'status' => 'completed',
            'completion_notes' => $completionNotes,
            'completed_at' => now(),
        ]);

        $room->update(['status' => $newStatus]);

        $message = 'Room ' . $room->room_number . ' moved to ' . $newStatus;
        if ($completionNotes) {
            $message .= '. Notes: ' . $completionNotes;
        }

        return response()->json([
            'success' => true,
            'message' => $message,
        ]);
    }
}
