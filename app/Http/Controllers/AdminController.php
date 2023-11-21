<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Admin;

class AdminController extends Controller
{
    public function admin()
    {
        $admins = Admin::all();
        return response()->json($admins);
    }

    public function signup(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required',
            'email' => 'required|unique:admins',
            'password' => 'required',
            'role' => 'required',
        ]);

        $validatedData['password'] = bcrypt($validatedData['password']);
        $admin = Admin::create($validatedData);
        return response()->json($admin, 201);
    }

    public function login(Request $request)
    {
        $validatedData = $request->validate([
            'email' => 'required',
            'password' => 'required',
        ]);

        if (!$token = auth('admin')->attempt($validatedData)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return $this->respondWithToken($token);
    }

}
