<?php

namespace App\Http\Controllers\Guest;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class ReviewController extends Controller
{
    public function index(): View
    {
        $user = auth()->user();

        // Get all reviews written by the guest
        $reviews = Review::where('user_id', $user->id)
            ->with('hotel', 'room', 'booking')
            ->orderBy('created_at', 'desc')
            ->paginate(12);

        return view('guest.reviews.index', [
            'reviews' => $reviews,
        ]);
    }

    public function create(Booking $booking): View | RedirectResponse
    {
        // Verify the booking belongs to the logged-in user
        if ($booking->user_id !== auth()->id()) {
            abort(403, 'Unauthorized');
        }

        // Check if booking has been completed/checked out
        if (!in_array($booking->status, ['completed', 'checked_out'])) {
            abort(403, 'Can only review completed bookings');
        }

        // Check if review already exists for this booking
        $existingReview = Review::where('booking_id', $booking->id)->first();
        if ($existingReview) {
            return redirect()->route('guest.reviews.show', $existingReview)
                ->with('info', 'You have already reviewed this booking');
        }

        $booking->load('hotel', 'bookingItems.room');

        return view('guest.reviews.create', [
            'booking' => $booking,
        ]);
    }

    public function store(Request $request, Booking $booking): RedirectResponse
    {
        // Verify the booking belongs to the logged-in user
        if ($booking->user_id !== auth()->id()) {
            abort(403, 'Unauthorized');
        }

        // Validate the form submission
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

        // Create the review
        $review = Review::create([
            'booking_id' => $booking->id,
            'user_id' => auth()->id(),
            'hotel_id' => $booking->hotel_id,
            'room_id' => $room?->id,
            'rating' => $validated['rating'],

            'comment' => $validated['comment'],
        ]);

        return redirect()->route('guest.reviews.show', $review)
            ->with('success', 'Thank you for your review!');
    }

    public function show(Review $review): View
    {
        // Verify the review belongs to the logged-in user
        if ($review->user_id !== auth()->id()) {
            abort(403, 'Unauthorized');
        }

        $review->load('hotel', 'room', 'booking');

        return view('guest.reviews.show', [
            'review' => $review,
        ]);
    }
}
