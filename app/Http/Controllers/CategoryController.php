<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use Exception;

class CategoryController extends Controller
{
    public function indexAdmin()
    {
        try {
            $categories = Category::with('band')->orderBy('created_at', 'desc')->get();
            return response()->json($categories);
        } catch (Exception $e) {
            return response()->json(['message' => 'An error occurred while retrieving the categories', 'error' => $e->getMessage()], 500);
        }
    }

    public function index()
    {
        try {
            $categories = Category::with('band')
                ->where('hidden', 0)
                ->orderBy('order', 'asc') // Sort by 'order' in ascending order
                ->get();
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
            'order' => 'nullable|integer',
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

    public function update(Request $request, $categoryId)
    {
        try {
            $request->validate([
                'name' => 'sometimes|required',
                'tax' => 'sometimes|required|numeric',
                'delivery_option' => 'sometimes|required|boolean',
                'discount_option' => 'sometimes|required|boolean',
                'discount_type' => 'nullable|in:percentage,fixed',
                'discount_value' => 'nullable|numeric',
                'thumbnail' => 'nullable|image',
                'band_id' => 'sometimes|required|exists:bands,id',
                'order' => 'nullable|integer',

            ]);

            $data = $request->all();

            if ($request->hasFile('thumbnail')) {
                $thumbnailPath = $request->file('thumbnail')->store('thumbnails', 'public');
                $data['thumbnail'] = asset('storage/' . $thumbnailPath);
            }

            $category = Category::findOrFail($categoryId);
            $category->update($data);

            return response()->json($category);
        } catch (\Exception $e) {
            return response()->json(['message' => 'An error occurred while updating the category', 'error' => $e->getMessage()], 500);
        }
    }

    public function show($id)
    {
        try {
            $category = Category::with('band')->find($id);

            // Check if the category exists
            if (!$category) {
                return response()->json(['message' => 'Category not found'], 404);
            }

            return response()->json($category);
        } catch (Exception $e) {
            return response()->json(['message' => 'An error occurred while retrieving the category', 'error' => $e->getMessage()], 500);
        }
    }

    public function hide($categoryId)
    {
        try {
            $category = Category::findOrFail($categoryId);
            $category->update(['hidden' => !$category->hidden]);

            return response()->json($category);
        } catch (\Exception $e) {
            return response()->json(['message' => 'An error occurred while updating the category visibility', 'error' => $e->getMessage()], 500);
        }
    }
}
