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
use Exception;
use Illuminate\Support\Facades\DB;
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

    public function getAnalytics(){
    // 1. Top selling products (Top 1000)
    // 1. Top selling products (Top 1000)
    $topSellingProducts = DB::table('orders')
        ->select(DB::raw('json_extract(product_ids, "$[*]") as product_id'), DB::raw('count(*) as total'))
        ->groupBy('product_id')
        ->orderBy('total', 'desc')
        ->limit(1000)
        ->get();

    // 2. Total sales for each product (total sold and amount)
    $totalSalesPerProduct = DB::table('orders')
        ->select(DB::raw('json_extract(product_ids, "$[*]") as product_id'), DB::raw('count(*) as total_sold'), DB::raw('sum(price) as total_amount'))
        ->groupBy('product_id')
        ->get();

    $topSellingLocations = DB::table('orders')
        ->select('delivery_info', DB::raw('count(*) as total'))
        ->groupBy('delivery_info')
        ->orderBy('total', 'desc')
        ->get();

    // 4. Average order value (Total value of orders/divided by the number of orders)
    $averageOrderValue = DB::table('orders')->avg('price');

    // 5. Total Revenue (Today, this week, this month, last 30 days, last 90days, all time, this year)
    $totalRevenueToday = DB::table('orders')->whereDate('created_at', '=', date('Y-m-d'))->sum('price');
    $totalRevenueThisWeek = DB::table('orders')->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->sum('price');
    $totalRevenueThisMonth = DB::table('orders')->whereMonth('created_at', '=', date('m'))->sum('price');
    $totalRevenueLast30Days = DB::table('orders')->whereBetween('created_at', [Carbon::now()->subDays(30), Carbon::now()])->sum('price');
    $totalRevenueLast90Days = DB::table('orders')->whereBetween('created_at', [Carbon::now()->subDays(90), Carbon::now()])->sum('price');
    $totalRevenueAllTime = DB::table('orders')->sum('price');
    $totalRevenueThisYear = DB::table('orders')->whereYear('created_at', '=', date('Y'))->sum('price');

    // Add these to your total array
    $total['top_selling_products'] = $topSellingProducts;
    $total['total_sales_per_product'] = $totalSalesPerProduct;
    $total['top_selling_locations'] = $topSellingLocations;
    $total['average_order_value'] = $averageOrderValue;
    $total['total_revenue_today'] = $totalRevenueToday;
    $total['total_revenue_this_week'] = $totalRevenueThisWeek;
    $total['total_revenue_this_month'] = $totalRevenueThisMonth;
    $total['total_revenue_last_30_days'] = $totalRevenueLast30Days;
    $total['total_revenue_last_90_days'] = $totalRevenueLast90Days;
    $total['total_revenue_all_time'] = $totalRevenueAllTime;
    $total['total_revenue_this_year'] = $totalRevenueThisYear;

    return response()->json($total);
    }

    public function getTotalRevenue($period)
    {
        switch ($period) {
            case 'today':
                $totalRevenue = $orders->whereDate('created_at', '=', date('Y-m-d'))->sum('price');
                break;
            case 'this_week':
                $totalRevenue = $orders->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->sum('price');
                break;
            case 'this_month':
                $totalRevenue = $orders->whereMonth('created_at', '=', date('m'))->sum('price');
                break;
            case 'last_30_days':
                $totalRevenue = $orders->whereBetween('created_at', [Carbon::now()->subDays(30), Carbon::now()])->sum('price');
                break;
            case 'last_90_days':
                $totalRevenue = $orders->whereBetween('created_at', [Carbon::now()->subDays(90), Carbon::now()])->sum('price');
                break;
            case 'all_time':
                $totalRevenue = $orders->sum('price');
                break;
            case 'this_year':
                $totalRevenue = $orders->whereYear('created_at', '=', date('Y'))->sum('price');
                break;
            default:
                throw new Exception('Invalid period');
        }

        return response()->json(['total_revenue' => $totalRevenue]);
    }

    public function create(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:admins',
            'password' => 'required|string|min:8',
        ]);

        $data['password'] = Hash::make($data['password']);

        $admin = Admin::create($data);

        return response()->json(['admin' => $admin], 201);
    }


    public function delete(Admin $admin)
    {
        $admin->delete();

        return response()->json(['message' => 'Admin deleted successfully']);
    }

}
