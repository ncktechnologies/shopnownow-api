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
use Carbon\Carbon;
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

        $totalValueOfOrders = $orders->sum('price');
        $totalValueOfPayments = $payments->sum('amount');

        $today = Carbon::today();
        $ordersToday = $orders->where('created_at', '>=', $today);
        $paymentsToday = $payments->where('created_at', '>=', $today);
        $usersToday = $users->where('created_at', '>=', $today);

        $totalOrdersToday = $ordersToday->count();
        $totalValueOfOrdersToday = $ordersToday->sum('price');
        $newUsersToday = $usersToday->count();
        $totalValueOfPaymentsToday = $paymentsToday->sum('amount');

        $total = [
            'admins' => $admins->count(),
            'users' => $users->count(),
            'products' => $products->count(),
            'orders' => $orders->count(),
            'categories' => $categories->count(),
            'bands' => $bands->count(),
            'payments' => $payments->count(),
            'special_requests' => $special_requests->count(),
            'total_value_of_orders' => $totalValueOfOrders,
            'total_value_of_payments' => $totalValueOfPayments,
            'total_orders_today' => $totalOrdersToday,
            'total_value_of_orders_today' => $totalValueOfOrdersToday,
            'new_users_today' => $newUsersToday,
            'total_value_of_payments_today' => $totalValueOfPaymentsToday,
        ];

        return response()->json($total);
    }

}
