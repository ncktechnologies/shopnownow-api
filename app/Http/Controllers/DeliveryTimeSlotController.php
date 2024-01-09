<?php

namespace App\Http\Controllers;

use App\Models\DeliveryTimeSlot;
use Illuminate\Http\Request;

class DeliveryTimeSlotController extends Controller
{
    public function index()
    {
        $timeSlots = DeliveryTimeSlot::get();
        return response()->json(['timeSlots' => $timeSlots]);
    }

    public function indexByBand(Request $request)
    {
        $bandId = $request->get('band_id');
        $timeSlots = DeliveryTimeSlot::when($bandId, function ($query, $bandId) {
            return $query->where('band_id', $bandId);
        })
        ->where('is_available', 1)
        ->get();

        return response()->json(['timeSlots' => $timeSlots]);
    }

    public function indexByBandEnforced(Request $request)
    {
        $request->validate([
            'band_id' => 'required|integer',
        ]);

        $bandId = $request->get('band_id');
        $timeSlots = DeliveryTimeSlot::where('band_id', $bandId)
            ->where('is_available', 1)
            ->get();

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

    public function hide(DeliveryTimeSlot $timeSlot)
    {
        $timeSlot->is_available = false;
        $timeSlot->save();
        return response()->json(['timeSlot' => $timeSlot]);
    }

    public function unhide(DeliveryTimeSlot $timeSlot)
    {
        $timeSlot->is_available = true;
        $timeSlot->save();
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
