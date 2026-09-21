<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Vehicle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class VehicleController extends Controller
{
    // GET /api/vehicles?type=Car&fuel_type=Petrol&transmission=Manual&min_price=0&max_price=10000&search=innova
    public function index(Request $request)
    {
        $query = Vehicle::query();

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }
        if ($request->filled('fuel_type')) {
            $query->where('fuel_type', $request->fuel_type);
        }
        if ($request->filled('transmission')) {
            $query->where('transmission', $request->transmission);
        }
        if ($request->filled('min_price')) {
            $query->where('price_per_day', '>=', $request->min_price);
        }
        if ($request->filled('max_price')) {
            $query->where('price_per_day', '<=', $request->max_price);
        }
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }
        // Only show available vehicles to normal browsing, unless explicitly asked for all
        if (! $request->boolean('all')) {
            $query->where('status', '!=', 'unavailable');
        }

        $perPage = min((int) $request->input('per_page', 12), 100); // cap at 100 to avoid abuse
$vehicles = $query->orderBy('created_at', 'desc')->paginate($perPage);

        return response()->json($vehicles);
    }

    // POST /api/vehicles (admin only)
    public function store(Request $request)
{
    $validator = Validator::make($request->all(), [
        'name' => 'required|string|max:255',
        'type' => 'required|string',
        'fuel_type' => 'required|string',
        'transmission' => 'required|string',
        'price_per_day' => 'required|numeric|min:0',
        'seats' => 'nullable|integer',
        'model_year' => 'nullable|integer',
        'description' => 'nullable|string',
        'features' => 'nullable|array',
        'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
        'status' => 'nullable|in:available,maintenance,unavailable',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'errors' => $validator->errors()
        ], 422);
    }

    $data = $validator->validated();

    // Upload vehicle image
    if ($request->hasFile('image')) {
        $path = $request->file('image')->store('vehicles', 'public');
        $data['image'] = '/storage/' . $path;
    }

    $vehicle = Vehicle::create($data);

    return response()->json([
        'message' => 'Vehicle added',
        'vehicle' => $vehicle
    ], 201);
}


    // GET /api/vehicles/{id}
    public function show($id)
    {
        $vehicle = Vehicle::find($id);

        if (! $vehicle) {
            return response()->json(['message' => 'Vehicle not found'], 404);
        }

        return response()->json($vehicle);
    }

    // PUT /api/vehicles/{id} (admin only)
    public function update(Request $request, $id)
{
    $vehicle = Vehicle::find($id);

    if (! $vehicle) {
        return response()->json([
            'message' => 'Vehicle not found'
        ], 404);
    }

    $validator = Validator::make($request->all(), [
        'name' => 'sometimes|string|max:255',
        'type' => 'sometimes|string',
        'fuel_type' => 'sometimes|string',
        'transmission' => 'sometimes|string',
        'price_per_day' => 'sometimes|numeric|min:0',
        'seats' => 'nullable|integer',
        'model_year' => 'nullable|integer',
        'description' => 'nullable|string',
        'features' => 'nullable|array',
        'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
        'status' => 'nullable|in:available,maintenance,unavailable',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'errors' => $validator->errors()
        ], 422);
    }

    $data = $validator->validated();

    // Upload new vehicle image
    if ($request->hasFile('image')) {
        $path = $request->file('image')->store('vehicles', 'public');
        $data['image'] = '/storage/' . $path;
    }

    $vehicle->update($data);

    return response()->json([
        'message' => 'Vehicle updated',
        'vehicle' => $vehicle
    ]);
}

    // DELETE /api/vehicles/{id} (admin only)
    public function destroy($id)
    {
        $vehicle = Vehicle::find($id);
        if (! $vehicle) {
            return response()->json(['message' => 'Vehicle not found'], 404);
        }

        $vehicle->delete();

        return response()->json(['message' => 'Vehicle deleted']);
    }
}
