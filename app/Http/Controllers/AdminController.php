<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Admin;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Product;
use App\Models\Order;
use App\Models\Category;
use App\Models\Band;
use App\Models\Payment;
use App\Models\SpecialRequest;
use Illuminate\Support\Facades\Auth;
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
        $loginData = $request->validate([
            'email' => 'email|required',
            'password' => 'required'
        ]);

        $admin = Admin::where('email', $loginData['email'])->first();

        if (!$admin || !Hash::check($loginData['password'], $admin->password)) {
            return response(['message' => 'Invalid Credentials'], 401);
        }

        $token = $admin->createToken('authToken')->accessToken;

        return response(['admin' => $admin, 'access_token' => $token]);
    }

    public function stats(){
        $admins = Admin::all();
        $users = User::all();
        $products = Product::all();
        $orders = Order::all();
        $categories = Category::all();
        $bands = Band::all();
        $payments = Payment::all();
        $special_requests = SpecialRequest::all();
        $total = [
            'admins' => $admins->count(),
            'users' => $users->count(),
            'products' => $products->count(),
            'orders' => $orders->count(),
            'categories' => $categories->count(),
            'bands' => $bands->count(),
            'payments' => $payments->count(),
            'special_requests' => $special_requests->count(),

        ];
        return response()->json($total);
    }

}
