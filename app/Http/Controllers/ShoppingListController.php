<?php

namespace App\Http\Controllers;

use App\Models\ShoppingList;
use Illuminate\Http\Request;

class ShoppingListController extends Controller
{
    public function create(Request $request)
    {
        $validatedData = $request->validate([
            'products.*.name' => 'required',
            'products.*.price' => 'required|numeric',
            'products.*.unit_of_measurement' => 'required',
            'products.*.category_id' => 'required|exists:categories,id',
            'products.*.thumbnail' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $products = [];

        foreach ($validatedData['products'] as $productData) {
            if ($request->hasFile('products.*.thumbnail')) {
                $thumbnail = $request->file('products.*.thumbnail');
                $thumbnailPath = $thumbnail->store('thumbnails', 'public');
                $productData['thumbnail_url'] = asset('storage/' . $thumbnailPath);
            }

            $product = ShoppingList::create($productData);
            $products[] = $product;
        }

        return response()->json($products, 201);
    }
    //
}
