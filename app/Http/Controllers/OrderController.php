<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{

    public function index()
    {
        try {
            $orders = Order::where('user_id', Auth::id())->get();

            foreach ($orders as $order) {
                $productIds = json_decode($order->product_ids);
                $quantities = json_decode($order->quantities);

                $products = Product::find($productIds);
                foreach ($products as $index => $product) {
                    $product->quantity = $quantities[$index];
                }
                $order->products = $products;
            }

            return response()->json(['message' => 'Orders retrieved successfully', 'orders' => $orders], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'An error occurred while retrieving the orders', 'error' => $e->getMessage()], 500);
        }
    }
    //add order functions for process, show, update and destroy
    public function store(Request $request)
    {
        // Validate the incoming request data
        $validatedData = $request->validate([
            'user_id' => 'integer',
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
            'delivery_fee'=> 'required|numeric',
            'delivery_time_slot' => 'required|string',
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
        if (Auth::id() !== $order->user_id) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $productIds = json_decode($order->product_ids);
        $quantities = json_decode($order->quantities);

        $products = Product::find($productIds);
        foreach ($products as $index => $product) {
            $product->quantity = $quantities[$index];
        }

        return response()->json(['order' => $order, 'products' => $products]);
    }

    public function update(Request $request, Order $order)
    {
        if (Auth::id() !== $order->user_id) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $validatedData = $request->validate([
            'status' => 'required|string',
        ]);

        $order->status = $validatedData['status'];
        $order->save();

        return response()->json(['message' => 'Order status updated successfully', 'order' => $order]);
    }

    public function destroy(Order $order)
    {
        if (Auth::id() !== $order->user_id) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $order->delete();

        return response()->json(['message' => 'Order deleted successfully']);
    }


    public function getAllOrdersAdmin(){
        try {
            $orders = Order::get();

            foreach ($orders as $order) {
                $productIds = json_decode($order->product_ids);
                $quantities = json_decode($order->quantities);

                $products = Product::find($productIds);
                foreach ($products as $index => $product) {
                    $product->quantity = $quantities[$index];
                }
                $order->products = $products;
            }

            return response()->json(['message' => 'Orders retrieved successfully', 'orders' => $orders], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'An error occurred while retrieving the orders', 'error' => $e->getMessage()], 500);
        }
    }

    public function getOneOrderAdmin(Order $order)
    {
        $productIds = json_decode($order->product_ids);
        $quantities = json_decode($order->quantities);

        $products = Product::find($productIds);
        foreach ($products as $index => $product) {
            $product->quantity = $quantities[$index];
        }

        return response()->json(['order' => $order, 'products' => $products]);
    }

    public function updateOrderAdmin(Request $request, Order $order)
    {
        $validatedData = $request->validate([
            'status' => 'required|string',
        ]);

        $order->status = $validatedData['status'];
        $order->save();

        return response()->json(['message' => 'Order status updated successfully', 'order' => $order]);
    }

    public function deleteOrderAdmin(Order $order)
    {
        $order->delete();

        return response()->json(['message' => 'Order deleted successfully']);
    }
}
