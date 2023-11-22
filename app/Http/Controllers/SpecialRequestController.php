<?php

namespace App\Http\Controllers;

use App\Models\SpecialRequest;
use Illuminate\Http\Request;

class SpecialRequestController extends Controller
{
    public function index()
    {
        $requests = SpecialRequest::all();
        return response()->json(['requests' => $requests], 200);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'request' => 'required|string',
            'message' => 'required|string',
        ]);

        $specialRequest = SpecialRequest::create($validatedData);
        return response()->json(['message' => 'Request created successfully', 'request' => $specialRequest], 201);
    }

    public function show(SpecialRequest $specialRequest)
    {
        return response()->json(['request' => $specialRequest], 200);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(SpecialRequest $specialRequest)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, SpecialRequest $specialRequest)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SpecialRequest $specialRequest)
    {
        //
    }
}
