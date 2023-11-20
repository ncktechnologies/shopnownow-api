<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;


class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|max:55',
            'email' => 'email|required|unique:users',
            'password' => 'required|confirmed'
        ]);

        $validatedData['password'] = bcrypt($request->password);

        $user = User::create($validatedData);

        $accessToken = $user->createToken('authToken')->accessToken;

        return response([ 'user' => $user, 'access_token' => $accessToken]);
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

        $otp = rand(100000, 999999);

        $user->otp = $otp;
        $user->save();

        //send otp to user email

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

        if ($user->otp == $validatedData['otp']) {
            $user->password = bcrypt($request->password);
            $user->save();

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

        $otp = rand(100000, 999999);

        $user->otp = $otp;
        $user->save();

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

        if ($user->otp == $validatedData['otp']) {
            $user->otp_verified = true;
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
            return response(['message' => 'Invalid Credentials']);
        }
        $user = User::where('email', $loginData['email'])->first();


        // $accessToken = $user->createToken('authToken')->accessToken;
        $token = $user->createToken('authToken')->plainTextToken;


        return response(['user' => auth()->user(), 'access_token' => $token]);
    }
}
