<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DeliveryLocation;

class DeliveryLocationController extends Controller
{

    public function index()
    {
        $locations = DeliveryLocation::all();
        return response()->json(['locations' => $locations], 200);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'band_id' => 'required|integer|exists:bands,id',
            'location' => 'required|string',
            'price' => 'required|numeric',
            'hidden' => 'boolean',
        ]);

        $location = DeliveryLocation::create($validatedData);
        return response()->json(['message' => 'Location created successfully', 'location' => $location], 201);
    }

    public function show($id)
    {
        $location = DeliveryLocation::findOrFail($id);
        return response()->json(['location' => $location], 200);
    }

    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'band_id' => 'integer|exists:bands,id',
            'location' => 'string',
            'price' => 'numeric',
            'hidden' => 'boolean',
        ]);

        $location = DeliveryLocation::findOrFail($id);
        $location->update($validatedData);
        return response()->json(['message' => 'Location updated successfully', 'location' => $location], 200);
    }

    public function destroy($id)
    {
        $location = DeliveryLocation::findOrFail($id);
        $location->delete();
        return response()->json(['message' => 'Location deleted successfully'], 200);
    }

    public function getByBandId($band_id)
    {
        $locations = DeliveryLocation::where('band_id', $band_id)
                                     ->where('hidden', '!=', true)
                                     ->get();
        return response()->json(['locations' => $locations], 200);
    }
}
