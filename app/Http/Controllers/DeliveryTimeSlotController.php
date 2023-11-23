<?php

namespace App\Http\Controllers;

use App\Models\DeliveryTimeSlot;
use Illuminate\Http\Request;

class DeliveryTimeSlotController extends Controller
{
    public function index()
    {
        $timeSlots = DeliveryTimeSlot::where('is_available', true)->get();
        return response()->json(['timeSlots' => $timeSlots]);
    }

    public function store(Request $request)
    {
        $timeSlot = DeliveryTimeSlot::create($request->all());
        return response()->json(['timeSlot' => $timeSlot], 201);
    }

    public function show(DeliveryTimeSlot $timeSlot)
    {
        return response()->json(['timeSlot' => $timeSlot]);
    }

    public function update(Request $request, DeliveryTimeSlot $timeSlot)
    {
        $timeSlot->update($request->all());
        return response()->json(['timeSlot' => $timeSlot]);
    }

    public function destroy(DeliveryTimeSlot $timeSlot)
    {
        $timeSlot->delete();
        return response()->json(['message' => 'Time slot deleted']);
    }
}
