<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Setting;

class SettingsController extends Controller
{
    public function update(Request $request, $key)
    {
        $data = $request->validate([
            'value' => 'required|numeric',
        ]);

        $setting = Setting::where('key', $key)->firstOrFail();
        $setting->update($data);

        return response()->json(['message' => 'Setting updated successfully'], 200);
    }

    public function show($key)
    {
        $setting = Setting::where('key', $key)->firstOrFail();

        return response()->json(['setting' => $setting]);
    }

    public function index()
    {
        $settings = Setting::all();

        return response()->json(['settings' => $settings]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'key' => 'required|string',
            'value' => 'required|string',
        ]);

        $setting = Setting::create($data);

        return response()->json(['setting' => $setting]);
    }
}
