<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Otp;
use Illuminate\Support\Facades\Mail;


class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|max:55',
            'email' => 'email|required|unique:users',
            'password' => 'required|confirmed',
            'phone_number' => 'nullable', // Add this line if you want to send an OTP to the user's phone number
        ]);

        $validatedData['password'] = bcrypt($request->password);

        $user = User::create($validatedData);

        // $accessToken = $user->createToken('authToken')->accessToken;
        $token = $user->createToken('authToken')->plainTextToken;


        // Generate a random 6-digit OTP
        $otp = rand(1000, 9999);

        Otp::create([
            'user_id' => $user->id,
            'otp' => $otp,
        ]);
        // Send the OTP to the user's email
        Mail::raw("Your OTP is: $otp", function ($message) use ($user) {
            $message->to($user->email);
            $message->subject('OTP for registration');
        });

        return response([ 'user' => $user, 'access_token' => $token]);
    }

    public function forgot_password(Request $request)
    {
        $validatedData = $request->validate([
            'email' => 'email|required'
        ]);

        $user = User::where('email', $validatedData['email'])->first();

        if (!$user) {
            return response(['message' => 'User not found']);
        }

        $otp = rand(1000, 9999);

        // Create a new OTP using the Otp model
        Otp::create([
            'user_id' => $user->id,
            'otp' => $otp,
        ]);

        // Send OTP to user email
        Mail::raw("Your OTP is: $otp", function ($message) use ($user) {
            $message->to($user->email);
            $message->subject('OTP for registration');
        });

        return response(['message' => 'OTP sent to your email']);
    }

    public function reset_password(Request $request)
    {
        $validatedData = $request->validate([
            'email' => 'email|required',
            'otp' => 'required',
            'password' => 'required|confirmed'
        ]);

        $user = User::where('email', $validatedData['email'])->first();

        if (!$user) {
            return response(['message' => 'User not found']);
        }

        // Check the otps table using the Otp model
        $otp = Otp::where('user_id', $user->id)->latest()->first();

        if ($otp && $otp->otp == $validatedData['otp']) {
            $user->password = bcrypt($request->password);
            $user->save();

            // Optionally, delete the used OTP
            $otp->delete();

            return response(['message' => 'Password reset successfully']);
        } else {
            return response(['message' => 'Invalid OTP']);
        }
    }


    public function resend_otp(Request $request)
    {
        $validatedData = $request->validate([
            'email' => 'email|required'
        ]);

        $user = User::where('email', $validatedData['email'])->first();

        if (!$user) {
            return response(['message' => 'User not found']);
        }

        $otp = rand(1000, 9999);

        Otp::create([
            'user_id' => $user->id,
            'otp' => $otp,
        ]);

        //send otp to user email

        return response(['message' => 'OTP sent to your email']);
    }

    public function verify_otp(Request $request)
    {
        $validatedData = $request->validate([
            'email' => 'email|required',
            'otp' => 'required'
        ]);

        $user = User::where('email', $validatedData['email'])->first();

        if (!$user) {
            return response(['message' => 'User not found']);
        }

        $otp = Otp::where('user_id', $user->id)->latest()->first();

        if ($otp && $otp->otp == $validatedData['otp']) {
            $otp->delete(); // delete the OTP after successful verification

            // Update the email_verified_at and verified columns
            $user->email_verified_at = now();
            $user->verified = true;
            $user->save();

            return response(['message' => 'OTP verified']);
        } else {
            return response(['message' => 'Invalid OTP']);
        }
    }

    public function logout(Request $request)
    {
        $request->user()->token()->revoke();

        return response(['message' => 'Logged out']);
    }


    public function login(Request $request)
    {
        $loginData = $request->validate([
            'email' => 'email|required',
            'password' => 'required'
        ]);

        if (!Auth::attempt($loginData)) {
            return response(['message' => 'Invalid Credentials'], 401);
        }
        $user = User::where('email', $loginData['email'])->first();

        // $accessToken = $user->createToken('authToken')->accessToken;
        $token = $user->createToken('authToken')->plainTextToken;

        return response(['user' => auth()->user(), 'access_token' => $token]);
    }
}
