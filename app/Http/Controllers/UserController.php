<?php

namespace App\Http\Controllers;

use App\Models\User; // Make sure to import the User model
use Illuminate\Http\Request;

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

    public function update(Request $request, User $user)
    {
        // Validate the incoming request data
        $validatedData = $request->validate([
            'name' => 'string|max:255',
            'email' => 'email|unique:users,email,' . $user->id, // Exclude the current user's email
            'phone_number' => 'nullable|string',
        ]);

        // Update the user's attributes
        $user->update($validatedData);

        return response()->json(['message' => 'User updated successfully', 'user' => $user]);
    }

    public function destroy(User $user)
    {
        // Delete the user
        $user->delete();

        return response()->json(['message' => 'User deleted successfully']);
    }
}
