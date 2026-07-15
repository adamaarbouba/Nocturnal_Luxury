<?php

namespace App\Http\Controllers\Api\Guest;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ReviewController extends Controller
{
    public function index(): JsonResponse
    {
        $user = auth()->user();

        $reviews = Review::where('user_id', $user->id)
            ->with('hotel', 'room', 'booking')
            ->orderBy('created_at', 'desc')
            ->paginate(12);

        return response()->json(['reviews' => $reviews]);
    }

    public function create(Booking $booking): JsonResponse
    {
        if ($booking->user_id !== auth()->id()) {
            abort(403, 'Unauthorized');
        }

        if (!in_array($booking->status, ['completed', 'checked_out'])) {
            abort(403, 'Can only review completed bookings');
        }

        // The web controller redirects to the existing review; the SPA does that client-side.
        $existingReview = Review::where('booking_id', $booking->id)->first();
        if ($existingReview) {
            return response()->json([
                'existingReviewId' => $existingReview->id,
                'message' => 'You have already reviewed this booking',
            ]);
        }

        $booking->load('hotel', 'bookingItems.room');

        return response()->json(['booking' => $booking]);
    }

    public function store(Request $request, Booking $booking): JsonResponse
    {
        if ($booking->user_id !== auth()->id()) {
            abort(403, 'Unauthorized');
        }

        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'required|string|min:10|max:1000',
        ], [
            'rating.required' => 'Overall rating is required',
            'rating.min' => 'Rating must be at least 1 star',
            'rating.max' => 'Rating cannot exceed 5 stars',
            'comment.min' => 'Review comment must be at least 10 characters',
            'comment.max' => 'Review comment cannot exceed 1000 characters',
        ]);

        $room = $booking->bookingItems->first()?->room;

        $review = Review::create([
            'booking_id' => $booking->id,
            'user_id' => auth()->id(),
            'hotel_id' => $booking->hotel_id,
            'room_id' => $room?->id,
            'rating' => $validated['rating'],
            'comment' => $validated['comment'],
        ]);

        return response()->json([
            'message' => 'Thank you for your review!',
            'review' => $review,
        ], 201);
    }

    public function show(Review $review): JsonResponse
    {
        if ($review->user_id !== auth()->id()) {
            abort(403, 'Unauthorized');
        }

        $review->load('hotel', 'room', 'booking');

        return response()->json(['review' => $review]);
    }
}
