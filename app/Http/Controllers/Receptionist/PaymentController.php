<?php

namespace App\Http\Controllers\Receptionist;

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

    /**
     * Show payment form for a booking
     */
    public function showPaymentForm(Booking $booking)
    {
        $hotel = $this->getReceptionistHotel();

        if ($booking->hotel_id !== $hotel->id) {
            abort(403, 'This booking does not belong to your hotel.');
        }

        $booking->load(['user', 'payments' => function ($query) {
            $query->orderBy('payment_date', 'desc');
        }]);

        $amountPaid = $booking->amountPaid();
        $amountRefunded = $booking->amountRefunded();
        $amountNetPaid = $booking->amountNetPaid();
        $remainingBalance = $booking->remainingBalance();

        return view('receptionist.payments.form', [
            'booking' => $booking,
            'hotel' => $hotel,
            'amountPaid' => $amountPaid,
            'amountRefunded' => $amountRefunded,
            'amountNetPaid' => $amountNetPaid,
            'remainingBalance' => $remainingBalance,
        ]);
    }

    /**
     * Record a payment
     */
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

        // Validate payment amount doesn't exceed remaining balance (for payments)
        if ($validated['type'] === 'payment') {
            $remainingBalance = $booking->remainingBalance();
            if ($validated['amount'] > $remainingBalance) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', "Payment amount exceeds remaining balance of \${$remainingBalance}.");
            }
        } else {
            // For refunds, can't refund more than was paid
            $amountPaid = $booking->amountPaid();
            if ($validated['amount'] > $amountPaid) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', "Refund amount exceeds total paid of \${$amountPaid}.");
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

            // Update payment_status based on remaining balance
            $remainingBalance = $booking->remainingBalance();
            if ($remainingBalance <= 0) {
                $booking->update(['payment_status' => 'paid']);
            } elseif (($booking->amountPaid() > 0 && $remainingBalance > 0)) {
                $booking->update(['payment_status' => 'partial']);
            }

            $actionType = $validated['type'] === 'payment' ? 'Payment recorded' : 'Refund recorded';
            return redirect()->route('receptionist.bookings.show', $booking)
                ->with('success', "{$actionType} successfully. Amount: \${$validated['amount']}");
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error recording payment: ' . $e->getMessage());
        }
    }

    /**
     * Delete a payment (only by receptionist who recorded it or admin)
     */
    public function deletePayment(Booking $booking, Payment $payment)
    {
        $hotel = $this->getReceptionistHotel();

        if ($booking->hotel_id !== $hotel->id) {
            abort(403, 'This booking does not belong to your hotel.');
        }

        if ($payment->booking_id !== $booking->id) {
            abort(403, 'This payment does not belong to this booking.');
        }

        // Only allow deletion by the receptionist who recorded it
        if ($payment->processed_by !== Auth::id()) {
            abort(403, 'You can only delete payments you recorded.');
        }

        try {
            $amount = $payment->amount;
            $type = $payment->type;
            $payment->delete();

            // Recalculate payment_status
            $remainingBalance = $booking->remainingBalance();
            if ($remainingBalance >= $booking->total_amount) {
                $booking->update(['payment_status' => 'pending']);
            } elseif ($booking->amountPaid() > 0 && $remainingBalance > 0) {
                $booking->update(['payment_status' => 'partial']);
            } elseif ($remainingBalance <= 0) {
                $booking->update(['payment_status' => 'paid']);
            }

            $actionType = $type === 'payment' ? 'Payment' : 'Refund';
            return redirect()->route('receptionist.bookings.show', $booking)
                ->with('success', "{$actionType} deleted. Amount: \${$amount}");
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error deleting payment: ' . $e->getMessage());
        }
    }

    /**
     * Approve a refund request.
     */
    public function approveRefund(RefundRequest $refundRequest)
    {
        $hotel = $this->getReceptionistHotel();
        $booking = $refundRequest->booking;

        if ($booking->hotel_id !== $hotel->id) {
            abort(403, 'This booking does not belong to your hotel.');
        }

        if ($refundRequest->status !== 'pending') {
            return redirect()->back()->with('error', 'This refund request has already been processed.');
        }

        try {
            // Create the actual refund payment
            Payment::create([
                'booking_id' => $booking->id,
                'amount' => $refundRequest->amount,
                'payment_method' => 'credit_card',
                'type' => 'refund',
                'notes' => 'Approved refund request: ' . $refundRequest->reason,
                'processed_by' => Auth::id(),
                'payment_date' => now(),
            ]);

            // Update refund request status
            $refundRequest->update([
                'status' => 'approved',
                'processed_by' => Auth::id(),
                'processed_at' => now(),
            ]);

            // Recalculate booking status
            $remainingBalance = $booking->remainingBalance();
            if ($remainingBalance >= $booking->total_amount) {
                $booking->update(['payment_status' => 'pending']);
            } elseif ($booking->amountPaid() > 0 && $remainingBalance > 0) {
                $booking->update(['payment_status' => 'partial']);
            }

            return redirect()->route('receptionist.dashboard')
                ->with('success', "Refund of \${$refundRequest->amount} approved successfully.");
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error approving refund: ' . $e->getMessage());
        }
    }

    /**
     * Deny a refund request.
     */
    public function denyRefund(RefundRequest $refundRequest)
    {
        $hotel = $this->getReceptionistHotel();

        if ($refundRequest->booking->hotel_id !== $hotel->id) {
            abort(403, 'This booking does not belong to your hotel.');
        }

        if ($refundRequest->status !== 'pending') {
            return redirect()->back()->with('error', 'This refund request has already been processed.');
        }

        try {
            $refundRequest->update([
                'status' => 'denied',
                'processed_by' => Auth::id(),
                'processed_at' => now(),
            ]);

            return redirect()->route('receptionist.dashboard')
                ->with('success', "Refund request denied.");
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error denying refund: ' . $e->getMessage());
        }
    }
}
