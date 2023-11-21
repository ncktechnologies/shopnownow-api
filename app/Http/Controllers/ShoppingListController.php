<?php

namespace App\Http\Controllers;

use App\Models\ShoppingList;
use Illuminate\Http\Request;

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

}
