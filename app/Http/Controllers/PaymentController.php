<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Payment;
use App\Models\Order;

class PaymentController extends Controller
{
    public function confirmPayment(Request $request)
    {
        try {
            // Validate the incoming request data
            $validatedData = $request->validate([
                'user_id' => 'required|integer',
                'amount' => 'required|numeric',
                'status' => 'required|string',
                'order_id' => 'required|string',
                'reference' => 'required|string',
                'payment_type' => 'required|string',
                'payment_gateway' => 'required|string',
                'payment_gateway_reference' => 'required|string',
            ]);

            // Create a new payment
            $payment = Payment::create($validatedData);

            // Find the order and update its status
            $order = Order::where('order_id', $validatedData['order_id'])->first();
            if ($order) {
                $order->status = 'paid';
                $order->save();
            }

            // You may choose to send a response with the newly created payment's data
            return response()->json(['message' => 'Payment confirmed successfully', 'payment' => $payment], 201);
        } catch (\Exception $e) {
            return response()->json(['message' => 'An error occurred while confirming the payment', 'error' => $e->getMessage()], 500);
        }
    }

    public function show(Payment $payment)
    {
        // Return the payment's details as a JSON response
        return response()->json(['payment' => $payment]);
    }
}
