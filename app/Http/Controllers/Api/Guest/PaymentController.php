<?php

namespace App\Http\Controllers\Api\Guest;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Payment;
use App\Models\RefundRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\JsonResponse;

class PaymentController extends Controller
{
    public function showPaymentForm(Booking $booking): JsonResponse
    {
        if ($booking->user_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        $booking->load(['hotel', 'payments', 'bookingItems.room']);

        return response()->json([
            'booking' => $booking,
            'remainingBalance' => $booking->remainingBalance(),
            'amountPaid' => $booking->amountPaid(),
        ]);
    }

    public function processPayment(Request $request, Booking $booking): JsonResponse
    {
        if ($booking->user_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        $request->validate([
            'amount' => 'required|numeric|min:1',
            'cardholder_name' => 'required|string|max:255',
            'card_number' => 'required|string|size:16',
            'expiry_date' => 'required|string|regex:/^\d{2}\/\d{2}$/',
            'cvv' => 'required|string|size:3',
        ]);

        $remainingBalance = $booking->remainingBalance();
        $paymentAmount = $request->amount;

        if ($paymentAmount > $remainingBalance) {
            return response()->json([
                'message' => "Payment amount exceeds remaining balance of \${$remainingBalance}",
            ], 422);
        }

        try {
            Payment::create([
                'booking_id' => $booking->id,
                'amount' => $paymentAmount,
                'payment_method' => 'credit_card',
                'type' => 'payment',
                'notes' => 'Online payment by guest',
                'processed_by' => Auth::id(),
                'payment_date' => now(),
            ]);

            $newRemainingBalance = $booking->remainingBalance();
            if ($newRemainingBalance <= 0) {
                $booking->update([
                    'payment_status' => 'paid',
                    'status' => 'confirmed',
                ]);
            } else {
                $booking->update(['payment_status' => 'partial']);
            }

            return response()->json([
                'message' => 'Payment successful! Your booking is now ' . $booking->status . '.',
                'booking' => $booking,
            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Payment failed: ' . $e->getMessage()], 500);
        }
    }

    public function requestRefund(Request $request, Booking $booking): JsonResponse
    {
        if ($booking->user_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        $request->validate([
            'amount' => 'required|numeric|min:1',
            'reason' => 'required|string|min:10|max:1000',
        ]);

        $amountPaid = $booking->amountPaid();

        if ($request->amount > $amountPaid) {
            return response()->json([
                'message' => "Refund amount exceeds total amount paid of \${$amountPaid}",
            ], 422);
        }

        $existingRequest = RefundRequest::where('booking_id', $booking->id)
            ->where('status', 'pending')
            ->first();

        if ($existingRequest) {
            return response()->json([
                'message' => 'You already have a pending refund request for this booking.',
            ], 422);
        }

        try {
            RefundRequest::create([
                'booking_id' => $booking->id,
                'user_id' => Auth::id(),
                'amount' => $request->amount,
                'reason' => $request->reason,
                'status' => 'pending',
            ]);

            return response()->json([
                'message' => 'Refund request submitted. A receptionist will review it shortly.',
            ], 201);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Could not submit refund request: ' . $e->getMessage()], 500);
        }
    }
}
