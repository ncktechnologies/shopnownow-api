<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\QuickGuide;

class QuickGuideController extends Controller
{
    public function index()
    {
        return QuickGuide::where('is_hidden', false)->get();
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required',
            'body' => 'required',
            'image' => 'required|image',
        ]);

        $data['image_path'] = $request->file('image')->store('quickguides');

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
            $data['image_path'] = $request->file('image')->store('quickguides');
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
