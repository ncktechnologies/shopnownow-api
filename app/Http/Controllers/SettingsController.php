<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Setting;

class SettingsController extends Controller
{
    public function update(Request $request, $key)
    {
        $setting = Setting::where('key', $key)->first();

        $data = $request->validate([
            'value' => 'required|numeric',
        ]);
        if ($setting) {
            $setting->value = $data['value'];
            $setting->save();

            return response()->json(['message' => 'Setting updated successfully', 200]);
        } else {
            return response()->json(['message' => 'Setting not updated', 404]);
        }
    }

    public function show($key)
    {
        $setting = Setting::find($key);

        if ($setting) {
            return response()->json(['setting' => $setting]);
        } else {
            return response()->json(['message' => 'Setting not found'], 404);
        }
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
