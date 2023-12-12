<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Setting;

class SettingsController extends Controller
{
    public function update(Request $request)
    {
        $key = $request->get('key');
        $setting = Setting::where('key', $key)->first();

        if ($setting) {
            $setting->value = $request->get('value');
            $setting->save();

            return response()->json(['message' => 'Setting updated successfully']);
        } else {
            return response()->json(['message' => $request], 404);
        }
    }

    public function show($key)
    {
        $setting = Setting::where('key', $key)->first();

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
