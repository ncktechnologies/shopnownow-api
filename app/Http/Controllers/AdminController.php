<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Order;

class AdminController extends Controller
{
    public function users()
    {
        $users = User::all();
        return response()->json($users);
    }

    public function orders()
    {
        $orders = Order::all();
        return response()->json($orders);
    }
}
