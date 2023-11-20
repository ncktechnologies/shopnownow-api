<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::all();
        return response()->json($categories);
    }

    public function create(Request $request)
    {
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
            $path = $request->file('thumbnail')->store('thumbnails', 'public');
            $data['thumbnail'] = asset($path);
        }

        $category = Category::create($data);

        return response()->json($category, 201);
    }

    public function update(Request $request, Category $category)
    {
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
            $path = $request->file('thumbnail')->store('thumbnails', 'public');
            $data['thumbnail'] = asset($path);
        }

        $category->update($data);

        return response()->json($category);
    }

    public function show(Category $category)
    {
        return response()->json($category);
    }

    public function hide(Category $category)
    {
        $category->update(['hidden' => !$category->hidden]);

        return response()->json($category);
    }
}
