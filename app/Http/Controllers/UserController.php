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

    public function index()
    {
        // Fetch all users with their orders, sorted by created_at in descending order
        $users = User::with('orders')->orderBy('created_at', 'desc')->get();

        // Return the users as a JSON response
        return response()->json(['users' => $users]);
    }

    public function show($id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

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
        try {
            // Validate the incoming request data
            $validatedData = $request->validate([
                'name' => 'string|max:255',
                'phone_number' => 'nullable|string',
            ]);

            // Fetch the user using the provided ID
            $user = User::findOrFail($userId);

            // Update the user's attributes
            $user->update($validatedData);

            return response()->json(['message' => 'User updated successfully', 'user' => $user]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'An error occurred while updating the user', 'error' => $e->getMessage()], 500);
        }
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
