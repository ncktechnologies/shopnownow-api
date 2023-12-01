<?php

namespace App\Http\Controllers;
use App\Models\Band;

use Illuminate\Http\Request;

class BandController extends Controller
{
    public function index()
    {
        $bands = Band::all();
        return response()->json($bands);
    }

    public function create(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'description' => 'nullable',
            'minimum' => 'nullable',
        ]);

        $band = Band::create($request->all());

        return response()->json($band, 201);
    }

    public function show(Band $band)
    {
        return response()->json($band);
    }

    public function update(Request $request, Band $band)
    {
        $request->validate([
            'name' => 'required',
            'description' => 'nullable',
            'minimum' => 'nullable',
        ]);

        $band->update($request->all());

        return response()->json($band);
    }

    public function hide(Band $band)
    {
        $band->update(['hidden' => true]);

        return response()->json($band);
    }
}
