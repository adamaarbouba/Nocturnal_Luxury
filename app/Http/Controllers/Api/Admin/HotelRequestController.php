<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\HotelRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HotelRequestController extends Controller
{
    /**
     * All hotel requests grouped by status (JSON mirror of Admin\HotelRequestController@index)
     */
    public function index(): JsonResponse
    {
        $pendingRequests = HotelRequest::pending()
            ->with('owner')
            ->orderBy('created_at', 'desc')
            ->paginate(10, ['*'], 'pending_page');

        $approvedRequests = HotelRequest::approved()
            ->with('owner', 'reviewer')
            ->orderBy('reviewed_at', 'desc')
            ->paginate(10, ['*'], 'approved_page');

        $rejectedRequests = HotelRequest::rejected()
            ->with('owner', 'reviewer')
            ->orderBy('reviewed_at', 'desc')
            ->paginate(10, ['*'], 'rejected_page');

        return response()->json([
            'pendingRequests' => $pendingRequests,
            'approvedRequests' => $approvedRequests,
            'rejectedRequests' => $rejectedRequests,
        ]);
    }

    /**
     * Show a single hotel request
     */
    public function show(HotelRequest $hotelRequest): JsonResponse
    {
        $hotelRequest->load('owner', 'reviewer');

        return response()->json(['request' => $hotelRequest]);
    }

    /**
     * Approve a hotel request
     */
    public function approve(Request $request, HotelRequest $hotelRequest): JsonResponse
    {
        $validated = $request->validate([
            'admin_notes' => 'nullable|string|max:1000',
        ]);

        if ($hotelRequest->status !== 'pending') {
            return response()->json(['message' => 'This request has already been processed.'], 409);
        }

        $hotelRequest->approve(Auth::id(), $validated['admin_notes'] ?? null);

        return response()->json(['message' => 'Hotel request approved successfully! Hotel has been created.']);
    }

    /**
     * Reject a hotel request
     */
    public function reject(Request $request, HotelRequest $hotelRequest): JsonResponse
    {
        $validated = $request->validate([
            'admin_notes' => 'required|string|min:10|max:1000',
        ]);

        if ($hotelRequest->status !== 'pending') {
            return response()->json(['message' => 'This request has already been processed.'], 409);
        }

        $hotelRequest->reject(Auth::id(), $validated['admin_notes']);

        return response()->json(['message' => 'Hotel request has been rejected.']);
    }
}
