<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Payment;
use App\Models\Order;
use App\Models\User;
use App\Models\Transaction;

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

            // Get the user
            $user = User::find($validatedData['user_id']);

            // Check if the user's balance is more than the payment amount
            if ($user->wallet >= $validatedData['amount']) {
                // Debit the user's wallet
                $user->wallet -= $validatedData['amount'];
                $user->save();

                // Create a new transaction history
                Transaction::create([
                    'user_id' => $user->id,
                    'amount' => $validatedData['amount'],
                    'type' => 'debit',
                    'reference' => 'Wallet Debit',
                    'message' => 'Payment for order ' . $validatedData['order_id'],
                    'status' => 'success'
                ]);

                // Create a new payment
                $payment = Payment::create($validatedData);

                // Find the order and update its status
                $order = Order::where('id', $validatedData['order_id'])->first();
                if ($order) {
                    $order->status = 'paid';
                    $order->save();
                }

                return response()->json(['message' => 'Payment confirmed successfully', 'payment' => $payment], 201);
            } else {
                if ($user->wallet > 0) {
                    // Debit the user's wallet completely
                    $remainingAmount = $validatedData['amount'] - $user->wallet;

                    // Create a new transaction history
                    Transaction::create([
                        'user_id' => $user->id,
                        'amount' => $user->wallet,
                        'type' => 'debit',
                        'reference' => 'Wallet Debit',
                        'message' => 'Payment for order ' . $validatedData['order_id'],
                        'status' => 'success'
                    ]);

                    $user->wallet = 0;
                    $user->save();
                }

                // Create a new payment
                $payment = Payment::create($validatedData);

                // Find the order and update its status
                $order = Order::where('id', $validatedData['order_id'])->first();
                if ($order) {
                    $order->status = 'paid';
                    $order->save();
                }

                // You may choose to send a response with the newly created payment's data
                return response()->json(['message' => 'Payment confirmed successfully', 'payment' => $payment], 201);
            }
        } catch (\Exception $e) {
            return response()->json(['message' => 'An error occurred while confirming the payment', 'error' => $e->getMessage()], 500);
        }
    }

    // public function confirmPayment(Request $request)
    // {
    //     try {
    //         // Validate the incoming request data
    //         $validatedData = $request->validate([
    //             'user_id' => 'required|integer',
    //             'amount' => 'required|numeric',
    //             'status' => 'required|string',
    //             'order_id' => 'required|string',
    //             'reference' => 'required|string',
    //             'payment_type' => 'required|string',
    //             'payment_gateway' => 'required|string',
    //             'payment_gateway_reference' => 'required|string',
    //         ]);

    //         // Create a new payment
    //         $payment = Payment::create($validatedData);

    //         // Find the order and update its status
    //         $order = Order::where('id', $validatedData['order_id'])->first();
    //         if ($order) {
    //             $order->status = 'paid';
    //             $order->save();
    //         }

    //         // You may choose to send a response with the newly created payment's data
    //         return response()->json(['message' => 'Payment confirmed successfully', 'payment' => $payment], 201);
    //     } catch (\Exception $e) {
    //         return response()->json(['message' => 'An error occurred while confirming the payment', 'error' => $e->getMessage()], 500);
    //     }
    // }

public function loadPayment($id)
{
    try {
        // Find the payment by its ID
        $payment = Payment::find($id);

        // Check if the payment exists
        if (!$payment) {
            return response()->json(['message' => 'Payment not found'], 404);
        }

        // Return the payment data
        return response()->json(['payment' => $payment], 200);
    } catch (\Exception $e) {
        return response()->json(['message' => 'An error occurred while loading the payment', 'error' => $e->getMessage()], 500);
    }
}
}
