<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Vehicle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class BookingController extends Controller
{
    // GET /api/bookings — logged-in customer's own bookings ("My Bookings" page)
    // Admins hitting this get ALL bookings ("Manage Bookings" page)
    public function index(Request $request)
    {
        $user = $request->user();

        $query = Booking::with(['vehicle', 'user']);

        if ($user->role !== 'admin') {
            $query->where('user_id', $user->id);
        }

        $bookings = $query->orderBy('created_at', 'desc')->get();

        return response()->json($bookings);
    }

    // POST /api/bookings — create a new booking (Booking page → "Continue to Book")
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'vehicle_id' => 'required|exists:vehicles,id',
            'pickup_location' => 'required|string|max:255',
            'pickup_date' => 'required|date|after_or_equal:today',
            'return_date' => 'required|date|after:pickup_date',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $vehicle = Vehicle::find($request->vehicle_id);

        if ($vehicle->status !== 'available') {
            return response()->json(['message' => 'This vehicle is not available right now'], 409);
        }

        $pickup = Carbon::parse($request->pickup_date);
        $return = Carbon::parse($request->return_date);
        $days = max(1, $pickup->diffInDays($return));
        $totalAmount = $days * $vehicle->price_per_day;

        $booking = Booking::create([
            'user_id' => $request->user()->id,
            'vehicle_id' => $vehicle->id,
            'pickup_location' => $request->pickup_location,
            'pickup_date' => $request->pickup_date,
            'return_date' => $request->return_date,
            'total_amount' => $totalAmount,
            'status' => 'pending',
        ]);

        return response()->json([
            'message' => 'Booking created',
            'booking' => $booking->load('vehicle'),
        ], 201);
    }

    // GET /api/bookings/{id}
    public function show(Request $request, $id)
    {
        $booking = Booking::with(['vehicle', 'user', 'payment'])->find($id);

        if (! $booking) {
            return response()->json(['message' => 'Booking not found'], 404);
        }

        $user = $request->user();
        if ($user->role !== 'admin' && $booking->user_id !== $user->id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        return response()->json($booking);
    }

    // PUT /api/bookings/{id}/status — admin approves/cancels/completes a booking
    public function updateStatus(Request $request, $id)
    {
        $booking = Booking::find($id);
        if (! $booking) {
            return response()->json(['message' => 'Booking not found'], 404);
        }

        $validator = Validator::make($request->all(), [
            'status' => 'required|in:pending,approved,upcoming,completed,cancelled',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $booking->update(['status' => $request->status]);

        return response()->json(['message' => 'Booking status updated', 'booking' => $booking]);
    }

    // DELETE /api/bookings/{id} — customer cancels their own upcoming booking
    public function destroy(Request $request, $id)
    {
        $booking = Booking::find($id);
        if (! $booking) {
            return response()->json(['message' => 'Booking not found'], 404);
        }

        $user = $request->user();
        if ($user->role !== 'admin' && $booking->user_id !== $user->id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $booking->update(['status' => 'cancelled']);

        return response()->json(['message' => 'Booking cancelled']);
    }
}
