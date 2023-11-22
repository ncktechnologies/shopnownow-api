<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use Exception;

class CategoryController extends Controller
{
    public function index()
    {
        try {
            $categories = Category::all();
            return response()->json($categories);
        } catch (Exception $e) {
            return response()->json(['message' => 'An error occurred while retrieving the categories', 'error' => $e->getMessage()], 500);
        }
    }

    public function create(Request $request)
    {
        try{
        $request->validate([
            'name' => 'required',
            'tax' => 'required|numeric',
            'delivery_option' => 'required|boolean',
            'discount_option' => 'required|boolean',
            'discount_type' => 'nullable|in:percentage,fixed',
            'discount_value' => 'nullable|numeric',
            'thumbnail' => 'nullable|image',
            'band_id' => 'required|exists:bands,id', // Add this line
        ]);

        $data = $request->all();

        if ($request->hasFile('thumbnail')) {
            $thumbnailPath = $request->file('thumbnail')->store('thumbnails', 'public');
            $data['thumbnail'] = asset('storage/' . $thumbnailPath);
        }

            $category = Category::create($data);

            return response()->json($category, 201);
        } catch (Exception $e) {
            return response()->json(['message' => 'An error occurred while creating the category', 'error' => $e->getMessage()], 500);
        }
    }

    public function update(Request $request, Category $category)
    {
        try {
            $request->validate([
                'name' => 'required',
                'tax' => 'required|numeric',
                'delivery_option' => 'required|boolean',
                'discount_option' => 'required|boolean',
                'discount_type' => 'nullable|in:percentage,fixed',
                'discount_value' => 'nullable|numeric',
                'thumbnail' => 'nullable|image',
                'band_id' => 'required|exists:bands,id', // Add this line
            ]);

            $data = $request->all();

            if ($request->hasFile('thumbnail')) {
                $thumbnailPath = $request->file('thumbnail')->store('thumbnails', 'public');
                $data['thumbnail'] = asset('storage/' . $thumbnailPath);
            }

            $category->update($data);

            return response()->json($category);
        } catch (\Exception $e) {
            return response()->json(['message' => 'An error occurred while updating the category', 'error' => $e->getMessage()], 500);
        }
    }

    public function show(Category $category)
    {
        try {
            return response()->json($category);
        } catch (Exception $e) {
            return response()->json(['message' => 'An error occurred while retrieving the category', 'error' => $e->getMessage()], 500);
        }
    }


    public function hide(Category $category)
    {
        try {
            $category->update(['hidden' => !$category->hidden]);

            return response()->json($category);
        } catch (Exception $e) {
            return response()->json(['message' => 'An error occurred while updating the category visibility', 'error' => $e->getMessage()], 500);
        }
    }
}
