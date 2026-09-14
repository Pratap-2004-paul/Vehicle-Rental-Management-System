<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    // GET /api/admin/stats — powers the Admin Dashboard
    public function stats()
    {
        $totalVehicles = Vehicle::count();
        $totalBookings = Booking::count();
        $totalCustomers = User::where('role', 'customer')->count();
        $totalRevenue = Booking::where('status', '!=', 'cancelled')->sum('total_amount');

        // Revenue grouped by month (last 6 months) for the chart
        $monthlyRevenue = Booking::selectRaw("DATE_FORMAT(created_at, '%b') as month, SUM(total_amount) as total")
            ->where('status', '!=', 'cancelled')
            ->groupBy('month')
            ->orderBy('created_at')
            ->limit(6)
            ->pluck('total', 'month');

        $recentBookings = Booking::with(['user', 'vehicle'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return response()->json([
            'total_vehicles' => $totalVehicles,
            'total_bookings' => $totalBookings,
            'total_customers' => $totalCustomers,
            'total_revenue' => $totalRevenue,
            'monthly_revenue' => $monthlyRevenue,
            'recent_bookings' => $recentBookings,
        ]);
    }

     // GET /api/admin/customers
    public function customers()
    {
        $customers = User::where('role', 'customer')
            ->withCount('bookings')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($customers);
    }

    // PUT /api/admin/customers/{id}/status
    public function updateCustomerStatus(Request $request, $id)
    {
        $customer = User::where('role', 'customer')->find($id);

        if (! $customer) {
            return response()->json(['message' => 'Customer not found'], 404);
        }

        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'status' => 'required|in:pending,approved,blocked',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $customer->update(['status' => $request->status]);

        return response()->json(['message' => 'Customer status updated', 'customer' => $customer]);
    }

        // GET /api/admin/payments
    public function payments()
    {
        $payments = \App\Models\Payment::with(['booking.user', 'booking.vehicle'])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($payments);
    }
}