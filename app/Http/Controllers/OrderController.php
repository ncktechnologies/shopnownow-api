<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;

class OrderController extends Controller
{
    //add order functions for process, show, update and destroy
    public function store(Request $request)
    {
        // Validate the incoming request data
        $validatedData = $request->validate([
            'user_id' => 'required|integer',
            'products.*.id' => 'required|integer',
            'products.*.quantity' => 'required|integer',
            'price' => 'required|numeric',
            'tax' => 'numeric',
            'status' => 'required|string',
            'delivery_info'=> 'required|string',
            'payment_type'=> 'required|string',
            'recipient_name'=> 'required|string',
            'recipient_phone'=> 'required|string',
            'recipient_email'=> 'required|string',
        ]);

        // Extract product IDs and quantities from the products array
        $products = collect($validatedData['products']);
        $productIds = $products->pluck('id');
        $quantities = $products->pluck('quantity');

        // Add product IDs and quantities to the validated data
        $validatedData['product_ids'] = json_encode($productIds);
        $validatedData['quantities'] = json_encode($quantities);

        // Generate an order ID
        $lastOrder = Order::orderBy('created_at', 'desc')->first();
        $orderId = $lastOrder ? $lastOrder->id + 1 : 1;
        $validatedData['order_id'] = '#' . str_pad($orderId, 7, '0', STR_PAD_LEFT);

        // Create a new order
        $order = Order::create($validatedData);

        // You may choose to send a response with the newly created order's data
        return response()->json(['message' => 'Order created successfully', 'order' => $order], 201);
    }

    public function show(Order $order)
    {
        // Return the order's details as a JSON response
        return response()->json(['order' => $order]);
    }

    public function update(Request $request, Order $order)
    {
        // Validate the incoming request data
        $validatedData = $request->validate([
            'status' => 'required|string',
        ]);

        // Update the order's status
        $order->status = $validatedData['status'];
        $order->save();

        return response()->json(['message' => 'Order status updated successfully', 'order' => $order]);
    }

    public function destroy(Order $order)
    {
        // Delete the order
        $order->delete();

        return response()->json(['message' => 'Order deleted successfully']);
    }
}
