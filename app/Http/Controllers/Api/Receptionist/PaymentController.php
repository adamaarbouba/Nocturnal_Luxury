<?php

namespace App\Http\Controllers\Api\Receptionist;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Payment;
use App\Models\RefundRequest;
use App\Models\HotelReceptionist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PaymentController extends Controller
{
    /**
     * Get the receptionist's assigned hotel
     */
    private function getReceptionistHotel()
    {
        $receptionist = HotelReceptionist::where('user_id', Auth::id())
            ->with('hotel')
            ->first();

        if (!$receptionist || $receptionist->status === 'inactive') {
            abort(403, 'You are not assigned to a hotel or your status is inactive.');
        }

        return $receptionist->hotel;
    }

    public function showPaymentForm(Booking $booking)
    {
        $hotel = $this->getReceptionistHotel();

        if ($booking->hotel_id !== $hotel->id) {
            abort(403, 'This booking does not belong to your hotel.');
        }

        $booking->load(['user', 'payments' => function ($query) {
            $query->with('processedBy')->orderBy('payment_date', 'desc');
        }]);

        return response()->json([
            'booking' => $booking,
            'hotel' => $hotel,
            'amountPaid' => $booking->amountPaid(),
            'amountRefunded' => $booking->amountRefunded(),
            'amountNetPaid' => $booking->amountNetPaid(),
            'remainingBalance' => $booking->remainingBalance(),
        ]);
    }

    public function recordPayment(Booking $booking, Request $request)
    {
        $hotel = $this->getReceptionistHotel();

        if ($booking->hotel_id !== $hotel->id) {
            abort(403, 'This booking does not belong to your hotel.');
        }

        $validated = $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'type' => 'required|in:payment,refund',
            'notes' => 'nullable|string|max:500',
        ]);

        if ($validated['type'] === 'payment') {
            $remainingBalance = $booking->remainingBalance();
            if ($validated['amount'] > $remainingBalance) {
                return response()->json(['message' => "Payment amount exceeds remaining balance of \${$remainingBalance}."], 422);
            }
        } else {
            $amountPaid = $booking->amountPaid();
            if ($validated['amount'] > $amountPaid) {
                return response()->json(['message' => "Refund amount exceeds total paid of \${$amountPaid}."], 422);
            }
        }

        try {
            Payment::create([
                'booking_id' => $booking->id,
                'amount' => $validated['amount'],
                'payment_method' => 'cash',
                'type' => $validated['type'],
                'notes' => $validated['notes'],
                'processed_by' => Auth::id(),
                'payment_date' => now(),
            ]);

            $remainingBalance = $booking->remainingBalance();
            if ($remainingBalance <= 0) {
                $booking->update(['payment_status' => 'paid']);
            } elseif (($booking->amountPaid() > 0 && $remainingBalance > 0)) {
                $booking->update(['payment_status' => 'partial']);
            }

            $actionType = $validated['type'] === 'payment' ? 'Payment recorded' : 'Refund recorded';
            return response()->json(['message' => "{$actionType} successfully. Amount: \${$validated['amount']}"]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error recording payment: ' . $e->getMessage()], 500);
        }
    }

    public function deletePayment(Booking $booking, Payment $payment)
    {
        $hotel = $this->getReceptionistHotel();

        if ($booking->hotel_id !== $hotel->id) {
            abort(403, 'This booking does not belong to your hotel.');
        }

        if ($payment->booking_id !== $booking->id) {
            abort(403, 'This payment does not belong to this booking.');
        }

        if ($payment->processed_by !== Auth::id()) {
            abort(403, 'You can only delete payments you recorded.');
        }

        try {
            $amount = $payment->amount;
            $type = $payment->type;
            $payment->delete();

            $remainingBalance = $booking->remainingBalance();
            if ($remainingBalance >= $booking->total_amount) {
                $booking->update(['payment_status' => 'pending']);
            } elseif ($booking->amountPaid() > 0 && $remainingBalance > 0) {
                $booking->update(['payment_status' => 'partial']);
            } elseif ($remainingBalance <= 0) {
                $booking->update(['payment_status' => 'paid']);
            }

            $actionType = $type === 'payment' ? 'Payment' : 'Refund';
            return response()->json(['message' => "{$actionType} deleted. Amount: \${$amount}"]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error deleting payment: ' . $e->getMessage()], 500);
        }
    }

    public function approveRefund(RefundRequest $refundRequest)
    {
        $hotel = $this->getReceptionistHotel();
        $booking = $refundRequest->booking;

        if ($booking->hotel_id !== $hotel->id) {
            abort(403, 'This booking does not belong to your hotel.');
        }

        if ($refundRequest->status !== 'pending') {
            return response()->json(['message' => 'This refund request has already been processed.'], 422);
        }

        try {
            Payment::create([
                'booking_id' => $booking->id,
                'amount' => $refundRequest->amount,
                'payment_method' => 'credit_card',
                'type' => 'refund',
                'notes' => 'Approved refund request: ' . $refundRequest->reason,
                'processed_by' => Auth::id(),
                'payment_date' => now(),
            ]);

            $refundRequest->update([
                'status' => 'approved',
                'processed_by' => Auth::id(),
                'processed_at' => now(),
            ]);

            $remainingBalance = $booking->remainingBalance();
            if ($remainingBalance >= $booking->total_amount) {
                $booking->update(['payment_status' => 'pending']);
            } elseif ($booking->amountPaid() > 0 && $remainingBalance > 0) {
                $booking->update(['payment_status' => 'partial']);
            }

            return response()->json(['message' => "Refund of \${$refundRequest->amount} approved successfully."]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error approving refund: ' . $e->getMessage()], 500);
        }
    }

    public function denyRefund(RefundRequest $refundRequest)
    {
        $hotel = $this->getReceptionistHotel();

        if ($refundRequest->booking->hotel_id !== $hotel->id) {
            abort(403, 'This booking does not belong to your hotel.');
        }

        if ($refundRequest->status !== 'pending') {
            return response()->json(['message' => 'This refund request has already been processed.'], 422);
        }

        try {
            $refundRequest->update([
                'status' => 'denied',
                'processed_by' => Auth::id(),
                'processed_at' => now(),
            ]);

            return response()->json(['message' => 'Refund request denied.']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error denying refund: ' . $e->getMessage()], 500);
        }
    }
}
