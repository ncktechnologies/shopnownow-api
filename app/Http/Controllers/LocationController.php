<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Location;

class LocationController extends Controller
{
    public function index()
    {
        return Location::all();
    }
    
    public function indexAdmin()
    {
        return Location::all();
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $location = Location::create($validatedData);

        return response()->json(['message' => 'Location created', 'location' => $location], 201);
    }

    public function show($id)
    {
        return Location::findOrFail($id);
    }

    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $location = Location::findOrFail($id);
        $location->update($validatedData);

        return response()->json(['message' => 'Location updated', 'location' => $location]);
    }

    public function hide($id)
    {
        $location = Location::findOrFail($id);
        $location->update(['hidden' => !$location->hidden]);

        $message = $location->hidden ? 'Location hidden' : 'Location visible';

        return response()->json(['message' => $message, 'location' => $location]);
    }

    public function destroy($id)
    {
        $location = Location::findOrFail($id);
        $location->delete();

        return response()->json(['message' => 'Location deleted']);
    }
}
