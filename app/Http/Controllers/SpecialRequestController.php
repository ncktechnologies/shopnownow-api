<?php

namespace App\Http\Controllers;

use App\Models\SpecialRequest;
use Illuminate\Http\Request;

class SpecialRequestController extends Controller
{
    public function index()
    {
        try {
            $requests = SpecialRequest::orderBy('created_at', 'desc')->get();
            return response()->json(['requests' => $requests], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'An error occurred while retrieving the requests', 'error' => $e->getMessage()], 500);
        }
    }
    
    public function filterRequestsByDate(Request $request)
    {
        try {
            $requests = SpecialRequest::query();

            if ($request->has('start_date') && $request->has('end_date')) {
                $requests = $requests->whereBetween('created_at', [$request->start_date, $request->end_date]);
            }

            $requests = $requests->orderBy('created_at', 'desc')->get();

            return response()->json(['requests' => $requests], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'An error occurred while retrieving the requests', 'error' => $e->getMessage()], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'request' => 'required|string',
                'comment' => 'required|string',
            ]);

            $specialRequest = SpecialRequest::create($validatedData);
            return response()->json(['message' => 'Request created successfully', 'request' => $specialRequest], 201);
        } catch (\Exception $e) {
            return response()->json(['message' => 'An error occurred while creating the request', 'error' => $e->getMessage()], 500);
        }
    }

    public function show($requestID)
    {
        try {
            $specialRequest = SpecialRequest::findOrFail($requestID);
            return response()->json(['request' => $specialRequest], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'An error occurred while retrieving the request', 'error' => $e->getMessage()], 500);
        }
    }

    public function destroy($requestID)
    {
        try {
            $specialRequest = SpecialRequest::findOrFail($requestID);
            $specialRequest->delete();
            return response()->json(['message' => 'Request deleted successfully'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'An error occurred while deleting the request', 'error' => $e->getMessage()], 500);
        }
    }
}
