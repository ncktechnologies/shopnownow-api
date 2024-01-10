<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Product;
use App\Models\Category;
use App\Models\Band;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{

    public function index()
    {
        try {
            $orders = Order::where('user_id', Auth::id())
                ->where('status', '!=', 'pending') // Exclude orders with a status of 'pending'
                ->orderBy('created_at', 'desc')
                ->get();

            foreach ($orders as $order) {
                $productIds = json_decode($order->product_ids);
                $quantities = json_decode($order->quantities);

                $products = Product::with('category.band')->find($productIds);
                $totalPrice = 0;
                foreach ($products as $index => $product) {
                    $product->quantity = $quantities[$index];
                    $product->band = $product->category->band;
                    $product->category = $product->category;
                    $totalPrice += $product->price * $product->quantity;
                }
                $order->products = $products;
                $order->total_order_price = (string)($totalPrice + $order->delivery_fee + $order->tax);
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
            'coupon_code' => 'string',
            'scheduled_date' => 'date',
            'discount_applied' => 'boolean',
        ]);

        // Extract product IDs and quantities from the products array
        $products = collect($validatedData['products']);
        $productIds = $products->pluck('id');
        $quantities = $products->pluck('quantity');

        // Check if all products are within the same band
        $bandId = Product::find($productIds[0])->category->band_id;
        foreach ($productIds as $productId) {
            $productBandId = Product::find($productId)->category->band_id;
            if ($productBandId !== $bandId) {
                return response()->json(['message' => 'All products must be within the same band'], 400);
            }
        }

        // Add product IDs and quantities to the validated data
        $validatedData['product_ids'] = json_encode($productIds);
        $validatedData['quantities'] = json_encode($quantities);

            // Find the product
            $productModel = Product::find($products[0]['id']);

            // Get the category id from the Product model
            $categoryId = $productModel->category_id;

            // Find the product's category
            $category = Category::find($categoryId);

            // Find the band associated with the category
            $band = Band::find($category->band_id);

            // Check if a discount is not applied and the total price of the order is less than the band's minimum
            if (!$validatedData['discount_applied'] && $validatedData['price'] < $band->minimum) {
                return response()->json(['message' => 'The total price of the order must be greater than or equal to ' . $band->minimum], 400);
            }

        // Generate an order ID
        $lastOrder = Order::orderBy('created_at', 'desc')->first();
        $orderId = $lastOrder ? $lastOrder->id + 1 : 1;
        $validatedData['order_id'] = '#' . str_pad($orderId, 7, '0', STR_PAD_LEFT);

        // Create a new order
        $order = Order::create($validatedData);

        // You may choose to send a response with the newly created order's data
        return response()->json(['message' => 'Order created successfully', 'order' => $order], 201);
    }

    public function reorder($id)
    {
        try {
            // Get the authenticated user
            $authUser = Auth::user();

            // Find the order
            $oldOrder = Order::find($id);

            if (!$oldOrder) {
                return response()->json(['message' => 'Order not found'], 404);
            }

            // Check if the user_id of the order matches the authenticated user's ID
            if ($oldOrder->user_id != $authUser->id) {
                return response()->json(['message' => 'You do not have permission to reorder this order'], 403);
            }

            // Extract the details from the old order
            $data = [
                'user_id' => $oldOrder->user_id,
                'product_ids' => $oldOrder->product_ids,
                'quantities' => $oldOrder->quantities, // Add this line
                'products' => json_decode($oldOrder->products, true),
                'price' => $oldOrder->price,
                'tax' => $oldOrder->tax,
                'status' => 'pending', // Set the status to 'pending'
                'delivery_info' => $oldOrder->delivery_info,
                'payment_type' => $oldOrder->payment_type,
                'recipient_name' => $oldOrder->recipient_name,
                'recipient_phone' => $oldOrder->recipient_phone,
                'recipient_email' => $oldOrder->recipient_email,
                'delivery_fee' => $oldOrder->delivery_fee,
                'delivery_time_slot' => $oldOrder->delivery_time_slot,
                'scheduled_date' => $oldOrder->scheduled_date,
            ];

            // Generate an order ID
            $lastOrder = Order::orderBy('created_at', 'desc')->first();
            $orderId = $lastOrder ? $lastOrder->id + 1 : 1;
            $order_id = '#' . str_pad($orderId, 7, '0', STR_PAD_LEFT);

            // Add order_id to the data array
            $data['order_id'] = $order_id;

            // Create a new order with the same details
            $newOrder = Order::create($data);

            return response()->json(['message' => 'Order placed successfully', 'order' => $newOrder], 201);
        } catch (\Exception $e) {
            return response()->json(['message' => 'An error occurred while placing the order', 'error' => $e->getMessage()], 500);
        }
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
            $orders = Order::orderBy('created_at', 'desc')->get();

            foreach ($orders as $order) {
                $productIds = json_decode($order->product_ids);
                $quantities = json_decode($order->quantities);

                $products = Product::find($productIds);
                $totalPrice = 0;
                foreach ($products as $index => $product) {
                    $product->quantity = $quantities[$index];
                    $totalPrice += $product->price * $product->quantity;
                }
                $order->products = $products;
                $order->total_price = $totalPrice + $order->tax + $order->delivery_fee;
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
        $totalPrice = 0;
        foreach ($products as $index => $product) {
            $product->quantity = $quantities[$index];
            $totalPrice += $product->price * $product->quantity;
        }
        $order->products = $products;
        $order->total_price = $totalPrice + $order->tax + $order->delivery_fee;

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

    public function filterOrders(Request $request)
    {
        try {
            $orders = Order::query();

            if ($request->has('start_date') && $request->has('end_date')) {
                $orders = $orders->whereBetween('created_at', [$request->start_date, $request->end_date]);
            }

            if ($request->has('status')) {
                $orders = $orders->where('status', $request->status);
            }

            $orders = $orders->get();

            return response()->json(['message' => 'Orders retrieved successfully', 'orders' => $orders], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'An error occurred while retrieving the orders', 'error' => $e->getMessage()], 500);
        }
    }
}
