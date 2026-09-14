<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class PaymentController extends Controller
{
    // GET /api/bookings/{bookingId}/payment — fetch booking + existing payment (if any) for the payment page
    public function show(Request $request, $bookingId)
    {
        $booking = Booking::with(['vehicle', 'payment'])->find($bookingId);

        if (! $booking) {
            return response()->json(['message' => 'Booking not found'], 404);
        }

        $user = $request->user();
        if ($user->role !== 'admin' && $booking->user_id !== $user->id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        return response()->json($booking);
    }

    // POST /api/bookings/{bookingId}/payment — pay for a booking
    public function store(Request $request, $bookingId)
    {
        $booking = Booking::find($bookingId);

        if (! $booking) {
            return response()->json(['message' => 'Booking not found'], 404);
        }

        $user = $request->user();
        if ($booking->user_id !== $user->id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $validator = Validator::make($request->all(), [
            'method' => 'required|in:upi,card,net_banking,cash_on_delivery',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // NOTE: this is a simulated payment (no real payment gateway wired in).
        // For a real project you'd integrate Razorpay/Stripe here instead of marking it paid directly.
        $payment = Payment::updateOrCreate(
            ['booking_id' => $booking->id],
            [
                'amount' => $booking->total_amount,
                'method' => $request->method,
                'status' => $request->method === 'cash_on_delivery' ? 'pending' : 'paid',
                'transaction_id' => 'TXN' . strtoupper(Str::random(10)),
            ]
        );

        $booking->update(['status' => 'upcoming']);

        return response()->json([
            'message' => 'Payment recorded',
            'payment' => $payment,
            'booking' => $booking->fresh('vehicle'),
        ], 201);
    }
}
