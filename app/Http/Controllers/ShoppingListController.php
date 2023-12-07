<?php

namespace App\Http\Controllers;

use App\Models\ShoppingList;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ShoppingListController extends Controller
{

    public function createList(Request $request)
    {
        try {
            // Get the authenticated user using Laravel's authentication
            $user = auth()->user();

            // Check if a user is returned
            if (!$user) {
                return response()->json(['message' => 'User not authenticated'], 401);
            }

            // Validate the incoming request data
            $validatedData = $request->validate([
                'products.*.id' => 'required|integer',
                'products.*.quantity' => 'required|integer',
            ]);

            // Extract product IDs and quantities from the products array
            $products = collect($validatedData['products']);
            $productIds = $products->pluck('id');
            $quantities = $products->pluck('quantity');

            // Add product IDs and quantities to the validated data
            $validatedData['product_ids'] = json_encode($productIds);
            $validatedData['quantities'] = json_encode($quantities);

            // Associate the user ID with the shopping list
            $validatedData['user_id'] = $user->id;

            // Create a new shopping list
            $shoppingList = ShoppingList::create($validatedData);

            // You may choose to send a response with the newly created shopping list's data
            return response()->json(['message' => 'Shopping list created successfully', 'shopping list' => $shoppingList], 201);
        } catch (\Exception $e) {
            return response()->json(['message' => 'An error occurred while creating the shopping list', 'error' => $e->getMessage()], 500);
        }
    }

    public function place($list_id)
    {
        try {
            $shoppingList = ShoppingList::findOrFail($list_id);
            // Assuming you have an Order model and it has a relationship with ShoppingList
            $order = new Order();
            $order->shopping_list_id = $shoppingList->id;
            $order->save();

            return response()->json(['message' => 'Order placed successfully', 'order' => $order], 201);
        } catch (\Exception $e) {
            return response()->json(['message' => 'An error occurred while placing the order', 'error' => $e->getMessage()], 500);
        }
    }


    public function show($list_id)
    {
        try {
            $shoppingList = ShoppingList::findOrFail($list_id);

            if (Auth::id() !== $shoppingList->user_id) {
                return response()->json(['message' => 'Unauthorized'], 401);
            }

            $productIds = json_decode($shoppingList->product_ids);
            $quantities = json_decode($shoppingList->quantities);

            $products = Product::with('category.band')->find($productIds);
            foreach ($products as $index => $product) {
                $product->quantity = $quantities[$index];
                $product->band = $product->category->band;
                $product->category = $product->category;
            }
            $shoppingList->product_ids = $products;

            return response()->json(['message' => 'Shopping list retrieved successfully', 'shopping list' => $shoppingList], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'An error occurred while retrieving the shopping list', 'error' => $e->getMessage()], 500);
        }
    }

    public function index()
    {
        try {
            $shoppingLists = ShoppingList::where('user_id', Auth::id())->get();

            foreach ($shoppingLists as $shoppingList) {
                $productIds = json_decode($shoppingList->product_ids);
                $quantities = json_decode($shoppingList->quantities);

                $products = Product::with('category.band')->find($productIds);
                foreach ($products as $index => $product) {
                    $product->quantity = $quantities[$index];
                    $product->band = $product->category->band;
                    $product->category = $product->category;
                }
                $shoppingList->product_ids = $products;
            }

            return response()->json(['message' => 'Shopping lists retrieved successfully', 'shopping lists' => $shoppingLists], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'An error occurred while retrieving the shopping lists', 'error' => $e->getMessage()], 500);
        }
    }

}
