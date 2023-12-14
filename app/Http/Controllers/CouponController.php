<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Coupon;

class CouponController extends Controller
{
    public function create(Request $request)
    {
        try {
            // Validate the incoming request data
            $validatedData = $request->validate([
                'code' => 'required|string|unique:coupons',
                'type' => 'required|string|in:amount,percentage',
                'value' => 'required|numeric',
                'start_date' => 'required|date',
                'end_date' => 'required|date|after_or_equal:start_date',
            ]);

            // Create a new coupon
            $coupon = Coupon::create($validatedData);

            // You may choose to send a response with the newly created coupon's data
            return response()->json(['message' => 'Coupon created successfully', 'coupon' => $coupon], 201);
        } catch (\Exception $e) {
            return response()->json(['message' => 'An error occurred while creating the coupon', 'error' => $e->getMessage()], 500);
        }
    }


    public function loadCoupon(Request $request)
    {
        try {
            // Validate the incoming request data
            $validatedData = $request->validate([
                'code' => 'required|string',
            ]);

            // Find the coupon by its code
            $coupon = Coupon::where('code', $validatedData['code'])->first();

            // Check if the coupon exists
            if (!$coupon) {
                return response()->json(['message' => 'Coupon not found'], 404);
            }

            // Check if the coupon has expired
            $currentDate = date('Y-m-d');
            if ($coupon->start_date > $currentDate || $coupon->end_date < $currentDate) {
                return response()->json(['message' => 'Coupon has expired'], 400);
            }

            // Return the coupon data
            return response()->json(['coupon' => $coupon], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'An error occurred while loading the coupon', 'error' => $e->getMessage()], 500);
        }
    }

    //List all coupons
    public function list()
    {
        try {
            // Get all coupons sorted by created_at in descending order
            $coupons = Coupon::orderBy('created_at', 'desc')->get();

            // Return the coupons data
            return response()->json(['coupons' => $coupons], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'An error occurred while loading the coupons', 'error' => $e->getMessage()], 500);
        }
    }

    public function show(Coupon $coupon)
    {
        return response()->json(['coupon' => $coupon], 200);
    }

    public function update(Request $request, Coupon $coupon)
    {
        $validatedData = $request->validate([
            'code' => 'string|unique:coupons,code,' . $coupon->id,
            'type' => 'string|in:amount,percentage',
            'value' => 'numeric',
            'start_date' => 'date',
            'end_date' => 'date|after_or_equal:start_date',
        ]);

        $coupon->update($validatedData);

        return response()->json(['message' => 'Coupon updated successfully', 'coupon' => $coupon], 200);
    }

    public function hide(Coupon $coupon)
    {
        $coupon->update(['hidden' => true]);

        return response()->json(['message' => 'Coupon hidden successfully'], 200);
    }

    public function destroy(Coupon $coupon)
    {
        $coupon->delete();

        return response()->json(['message' => 'Coupon deleted successfully'], 200);
    }
}
