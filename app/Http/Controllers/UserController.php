<?php

namespace App\Http\Controllers;

use App\Models\User; // Make sure to import the User model
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    //
    public function store(Request $request)
    {
        // Validate the incoming request data
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'phone_number' => 'nullable|string',
            'password' => 'required|string|min:6',
        ]);

        // Create a new user
        $user = User::create($validatedData);

        // You may choose to send a response with the newly created user's data
        return response()->json(['message' => 'User created successfully', 'user' => $user], 201);
    }

    public function show(User $user)
    {
        // Return the user's details as a JSON response
        return response()->json(['user' => $user]);
    }

    public function profile(Request $request)
    {
        // Return the user's details as a JSON response
        return response()->json(['user' => $request->user()]);
    }

    public function update(Request $request, $userId)
    {
        // Validate the incoming request data
        $validatedData = $request->validate([
            'name' => 'string|max:255',
            'email' => 'email|unique:users,email,' . $userId, // Exclude the current user's email
            'phone_number' => 'nullable|string',
        ]);

        // Update the user's attributes
        $user->update($validatedData);

        return response()->json(['message' => 'User updated successfully', 'user' => $user]);
    }


    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:8|confirmed',
        ]);

        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json(['message' => 'Current password does not match'], 400);
        }

        $user->password = Hash::make($request->new_password);
        $user->save();

        return response()->json(['message' => 'Password changed successfully'], 200);
    }

    public function destroy(User $user)
    {
        // Delete the user
        $user->delete();

        return response()->json(['message' => 'User deleted successfully']);
    }
}
