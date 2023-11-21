<?php

namespace App\Http\Controllers;

use App\Models\ShoppingList;
use Illuminate\Http\Request;

class ShoppingListController extends Controller
{

    //create a function called createList to allow user store product ids as a shopping list as well as quantity of each product
    public function createList(Request $request)
    {
        // Validate the incoming request data
        $validatedData = $request->validate([
            'user_id' => 'required|integer',
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

        // Create a new shopping list
        $shoppingList = ShoppingList::create($validatedData);

        // You may choose to send a response with the newly created shopping list's data
        return response()->json(['message' => 'Shopping list created successfully', 'shopping list' => $shoppingList], 201);
    }
}
