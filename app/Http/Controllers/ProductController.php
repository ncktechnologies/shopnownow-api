<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $products = Product::orderBy('created_at', 'desc')->get();
        return response()->json($products);
    }

    /**
     * Show the form for creating a new resource.
     */


    /**
     * Store a newly created resource in storage.
     */
    public function create(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required',
            'price' => 'required|numeric',
            'unit_of_measurement' => 'required',
            'category_id' => 'required|exists:categories,id',
            'thumbnail' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        if ($request->hasFile('thumbnail')) {
            $thumbnail = $request->file('thumbnail');
            $thumbnailPath = $thumbnail->store('thumbnails', 'public');
            $validatedData['thumbnail_url'] = asset('storage/' . $thumbnailPath);
        }

        $product = Product::create($validatedData);
        return response()->json($product, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $product = Product::findOrFail($id);
            return response()->json(['message' => 'Product retrieved successfully', 'product' => $product], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'An error occurred while retrieving the product', 'error' => $e->getMessage()], 500);
        }
    }

    public function searchByCategory($query, $categoryId)
    {
        try {
            $products = Product::where('name', 'LIKE', "%{$query}%")
                ->where('category_id', $categoryId)
                ->orderByRaw("CASE WHEN name LIKE '{$query}' THEN 0
                                   WHEN name LIKE '{$query}%' THEN 1
                                   WHEN name LIKE '%{$query}' THEN 2
                                   ELSE 3 END, name")
                ->take(20)
                ->get();

            return response()->json(['message' => 'Products retrieved successfully', 'products' => $products], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'An error occurred while retrieving the products', 'error' => $e->getMessage()], 500);
        }
    }

    public function search($query)
    {
        try {
            $products = Product::where('name', 'LIKE', "%{$query}%")
            ->orderByRaw("CASE WHEN name LIKE '{$query}' THEN 0
                                   WHEN name LIKE '{$query}%' THEN 1
                                   WHEN name LIKE '%{$query}' THEN 2
                                   ELSE 3 END, name")
            ->take(20)
                ->get();

            return response()->json(['message' => 'Products retrieved successfully', 'products' => $products], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'An error occurred while retrieving the products', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
