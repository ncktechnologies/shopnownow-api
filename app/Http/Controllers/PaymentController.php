<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Payment;

class PaymentController extends Controller
{
    public function process(Request $request)
    {
        // Implement your payment processing logic here
        return response()->json(['message' => 'Payment processed successfully']);
    }

    public function show(Payment $payment)
    {
        // Return the payment's details as a JSON response
        return response()->json(['payment' => $payment]);
    }
}
