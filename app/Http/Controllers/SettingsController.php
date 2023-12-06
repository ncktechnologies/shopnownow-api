<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Setting;

class SettingsController extends Controller
{
    public function update(Request $request, $key)
    {
        $setting = Setting::where('key', $key)->first();
        $setting->value = $request->get('value');
        $setting->save();

        return response()->json(['message' => 'Setting updated successfully']);
    }
}
