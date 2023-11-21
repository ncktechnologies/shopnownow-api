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

            // Return the coupon data
            return response()->json(['coupon' => $coupon], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'An error occurred while loading the coupon', 'error' => $e->getMessage()], 500);
        }
    }
}
