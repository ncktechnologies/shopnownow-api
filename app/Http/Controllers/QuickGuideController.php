<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\QuickGuide;
use Illuminate\Support\Facades\Storage;

class QuickGuideController extends Controller
{
    public function index()
    {
        return QuickGuide::where('is_hidden', false)->get();
    }

    public function indexAdmin()
    {
        return QuickGuide::all();
        }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required',
            'body' => 'required',
            'image' => 'required|image',
        ]);

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imagePath = $image->store('quickguides', 'public');
            $data['image_path'] = asset('storage/' . $imagePath);
        }

        $guide = QuickGuide::create($data);

        return response(['quick_guide' => $guide]);
    }

    public function update(Request $request, QuickGuide $guide)
    {
        $data = $request->validate([
            'title' => 'required',
            'body' => 'required',
            'image' => 'image',
        ]);

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imagePath = $image->store('quickguides', 'public');
            $data['image_path'] = asset('storage/' . $imagePath);
        }

        $guide->update($data);

        return response(['quick_guide' => $guide]);
    }
    public function toggleVisibility(QuickGuide $guide)
    {
        $guide->update(['is_hidden' => !$guide->is_hidden]);

        return response(['quick_guide' => $guide]);
    }
}
