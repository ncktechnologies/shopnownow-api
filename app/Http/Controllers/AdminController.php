<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Admin;

class AdminController extends Controller
{
    public function admins()
    {
        $admins = Admin::all();
        return response()->json($admins);
    }

    public function signup(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required',
            'email' => 'required|unique:admin',
            'password' => 'required',
            'role' => 'required',
        ]);

        $validatedData['password'] = bcrypt($validatedData['password']);
        $admin = Admin::create($validatedData);
        return response()->json($admin, 201);
    }

}
