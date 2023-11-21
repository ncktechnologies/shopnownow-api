<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Admin;
use Illuminate\Support\Facades\Hash;
use Exception;

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

        $validatedData['password'] = Hash::make($validatedData['password']);
        $admin = Admin::create($validatedData);
        return response()->json($admin, 201);
    }

    public function login(Request $request)
    {
        $validatedData = $request->validate([
            'email' => 'required',
            'password' => 'required',
        ]);

        try {
            $admin = Admin::where('email', $validatedData['email'])->first();
            if ($admin) {
                $checkPassword = Hash::check($validatedData['password'], $admin->password);
                if ($checkPassword) {
                    $tokenResult = $admin->createToken('authToken');
                    $token = $tokenResult->token;
                    $accessToken = $tokenResult->accessToken;

                    $data = [
                            'admin_id' => $admin->id,
                            'name' => $admin->name,
                            'email' => $admin->email,
                            'access_token' => $accessToken,
                        ];
                    return response()->json(['success' => true, 'message' => 'Admin successfully logged in!', 'data' => $data], 200);
                }
                return response()->json(['success' => false, 'message' => 'Incorrect password!'], 400);
            }
            return response()->json(['success' => false, 'message' => 'Incorrect email address!'], 400);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => "Couldn't log in admin: " . $e->getMessage()], 400);
        }
    }

}
