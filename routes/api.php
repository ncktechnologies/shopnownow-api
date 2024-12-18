<?php

use Illuminate\Http\Request;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ShoppingListController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\BandController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\WalletController;
use App\Http\Controllers\CouponController;
use App\Http\Controllers\DeliveryLocationController;
use App\Http\Controllers\SpecialRequestController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\DeliveryTimeSlotController;
use App\Http\Controllers\QuickGuideController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\SiteDataController;
use App\Http\Controllers\AppNotificationController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

//Authentification Routes including forgot password and verify otp


Route::prefix('v1')->group(function () {

    Route::prefix('user')->group(function () {
        Route::prefix('auth')->group(function () {
            Route::post('/register', [AuthController::class, 'register']);
            Route::post('/login', [AuthController::class, 'login']);
            Route::post('/forgot_password', [AuthController::class, 'forgot_password']);
            Route::post('/resend_otp', [AuthController::class, 'resend_otp']);
            Route::post('/verify_otp', [AuthController::class, 'verify_otp']);
            Route::post('/reset_password', [AuthController::class, 'reset_password']);
            Route::post('/logout', [AuthController::class, 'logout']);
        });

            Route::prefix('order-noauth')->group(function () {
                Route::post('/orders', [OrderController::class, 'store']);
            });

        Route::middleware(['auth:user'])->group(function () {
            Route::prefix('profile')->group(function () {
                Route::get('show', [UserController::class, 'profile']);
                Route::post('/user/{user}', [UserController::class, 'update']);
                Route::post('change_password', [UserController::class, 'changePassword']);
            });

            Route::prefix('orders')->group(function () {
                Route::get('/orders', [OrderController::class, 'index']); // Get all orders
                Route::post('/reorder/{order}', [OrderController::class, 'reorder']); // Reorder
                Route::get('/order/{order}', [OrderController::class, 'show']); // Get order details
                Route::put('/update/{order}', [OrderController::class, 'update']); // Update order
                Route::delete('/delete/{order}', [OrderController::class, 'destroy']); // Delete order
            });

            Route::prefix('shopping_list')->group(function () {
                Route::post('/save_list', [ShoppingListController::class, 'createList']);
                Route::post('/place_order/{list_id}', [OrderController::class, 'place']);
                Route::get('/list/{list_id}', [ShoppingListController::class, 'show']);
                Route::get('/lists', [ShoppingListController::class, 'index']);
                Route::delete('/delete/{list_id}', [ShoppingListController::class, 'delete']);
            });

            Route::prefix('wallet')->group(function () {
                Route::get('/balance', [WalletController::class, 'balance']);
                Route::post('/fund_wallet', [WalletController::class, 'fundWallet']);
                Route::post('/withdraw_funds', [WalletController::class, 'withdrawFunds']);
                Route::get('/transactions', [WalletController::class, 'transactionHistory']);
                Route::get('/limited-transactions', [WalletController::class, 'limitedTransactionHistory']);
                Route::get('/transactions/{transaction}', [WalletController::class, 'transactionDetails']);
                Route::post('/convert_points', [WalletController::class, 'convertPoints']);
            });
        });

        Route::apiResource('delivery-locations', DeliveryLocationController::class);
        Route::get('/get-locations/{band_id}', [DeliveryLocationController::class, 'getByBandId']);

        Route::prefix('delivery-time-slots')->group(function () {
            // Route::get('/list', [DeliveryTimeSlotController::class, 'index']); // Get all delivery time slots
            Route::get('/list', [DeliveryTimeSlotController::class, 'indexByBand']); // Get all delivery time slots y bands
        });

        Route::prefix('category')->group(function () { // Category routes
            Route::get('/list', [CategoryController::class, 'index']); // Get all categories
            Route::get('show/{category}', [CategoryController::class, 'show']); // Get category details
        });
        Route::prefix('product')->group(function(){
            Route::get('/list', [ProductController::class, 'index']); // Get all products
            Route::get('show/{product}', [ProductController::class, 'show']); // Get product details
            Route::get('/search/{query}/{categoryId}', [ProductController::class, 'searchByCategory']);
            Route::get('/search/{query}', [ProductController::class, 'search']);

        });

        Route::prefix('special_request')->group(function(){
            Route::post('/create', [SpecialRequestController::class, 'store']);
        });

        Route::prefix('coupons')->group(function(){
            Route::post('load', [CouponController::class, 'loadCoupon']); // Get coupon details

        });

        Route::prefix('quickguide')->group(function(){
            Route::get('/list', [QuickGuideController::class, 'index']); // Get all quick guides

        });


        Route::prefix('payment')->group(function () {
            Route::post('/process', [PaymentController::class, 'confirmPayment']);
            Route::post('/process-payment-non-auth', [PaymentController::class, 'confirmNonUserPayment']);
            Route::post('/process-web', [PaymentController::class, 'confirmPaymentWeb']);
            Route::post('/process-payment-non-auth-web', [PaymentController::class, 'confirmNonUserPaymentWeb']);
            Route::get('/payment/{payment}', [PaymentController::class, 'loadPayment']);
            Route::put('/payment/{payment}', [PaymentController::class, 'update']);
            Route::delete('/payments/{payment}', [PaymentController::class, 'destroy']);
        });

        Route::prefix('site-data')->group(function () {
            Route::get('/site_data', [SiteDataController::class, 'show']);
        });
    });

    Route::prefix('admin')->group(function () {

        Route::prefix('auth')->group(function () {
            Route::post('/signup', [AdminController::class, 'signup']);
            Route::post('/login', [AdminController::class, 'login']);
            Route::post('/logout', [AdminController::class, 'logout']);
            Route::post('/forgot_password', [AdminController::class, 'forgot_password']);

        });

        Route::prefix('admin')->group(function () {
            Route::get('/list', [AdminController::class, 'index']);
            Route::post('/create', [AdminController::class, 'create']);
            Route::delete('/delete/{admin}', [AdminController::class, 'delete']);
        });

        Route::prefix('dashboard')->group(function () {
            Route::get('/stats', [AdminController::class, 'stats']);
            Route::get('/analytics', [AdminController::class, 'getAnalytics']);
        });

        Route::prefix('band')->group(function () { // Band routes
            Route::get('/list', [BandController::class, 'index']); // Get all bands
            Route::post('/create', [BandController::class, 'create']); // Create a band
            Route::get('show/{band}', [BandController::class, 'show']); // Get band details
            Route::put('update/{band}', [BandController::class, 'update']); // Update band
            Route::post('hide/{band}', [BandController::class, 'hide']); // Delete band
        });

        Route::prefix('category')->group(function () { // Category routes
            Route::get('/list', [CategoryController::class, 'indexAdmin']); // Get all categories
            Route::post('/create', [CategoryController::class, 'create']); // Create a category
            Route::get('show/{category}', [CategoryController::class, 'show']); // Get category details
            Route::post('update/{category}', [CategoryController::class, 'update']); // Update category
            Route::post('hide/{category}', [CategoryController::class, 'hide']); // Hide category
        });

        Route::prefix('product')->group(function(){
            Route::get('/list', [ProductController::class, 'index']); // Get all products
            Route::post('/create', [ProductController::class, 'create']); // Create a product
            Route::post('/filter', [ProductController::class, 'filterProducts']); // Filter products
            Route::get('show/{product}', [ProductController::class, 'show']); // Get product details
            Route::post('update/{product}', [ProductController::class, 'update']); // Update product
            Route::get('/search/{query}', [ProductController::class, 'search']);
            Route::post('toggle/{product}', [ProductController::class, 'toggleAvailability']); // Hide product

        });

        Route::prefix('orders')->group(function(){
            Route::get('/list', [OrderController::class, 'getAllOrdersAdmin']); // Get all orders
            Route::get('/order/{order}', [OrderController::class, 'getOneOrderAdmin']); // Get order details
            Route::put('/update/{order}', [OrderController::class, 'updateOrderAdmin']); // Update order
            Route::delete('/delete/{order}', [OrderController::class, 'deleteOrderAdmin']); // Delete order
            Route::post('filter', [OrderController::class, 'filterOrders']); // Filter orders
        });

        Route::prefix('coupons')->group(function () {
            Route::get('/list', [CouponController::class, 'list']); // Get all coupons
            Route::post('/create', [CouponController::class, 'create']); // Create a coupon
            Route::get('show/{coupon}', [CouponController::class, 'show']); // Get coupon details
            Route::put('update/{coupon}', [CouponController::class, 'update']); // Update coupon
            Route::post('hide/{coupon}', [CouponController::class, 'hide']); // Hide coupon
            Route::delete('delete/{coupon}', [CouponController::class, 'destroy']); // Delete coupon
        });

        Route::prefix('delivery-locations')->group(function () {
        Route::post('create', [DeliveryLocationController::class, 'store']);
        Route::get('list', [DeliveryLocationController::class, 'index']);
        Route::get('list/{id}', [DeliveryLocationController::class, 'show']);
        Route::put('update/{id}', [DeliveryLocationController::class, 'update']);
        Route::delete('delete/{id}', [DeliveryLocationController::class, 'destroy']);

        });

        Route::prefix('delivery-time-slots')->group(function () {
            Route::get('/list', [DeliveryTimeSlotController::class, 'index']); // Get all delivery time slots
            Route::post('/create', [DeliveryTimeSlotController::class, 'store']); // Create a delivery time slot
            Route::get('show/{timeSlot}', [DeliveryTimeSlotController::class, 'show']); // Get delivery time slot details
            Route::post('update/{timeSlot}', [DeliveryTimeSlotController::class, 'update']); // Update delivery time slot
            Route::post('hide/{timeSlot}', [DeliveryTimeSlotController::class, 'hide']); // Hide delivery time slot
            Route::post('unhide/{timeSlot}', [DeliveryTimeSlotController::class, 'unhide']); // Hide delivery time slot
            Route::delete('delete/{timeSlot}', [DeliveryTimeSlotController::class, 'destroy']); // Delete delivery time slot
        });

        Route::prefix('location')->group(function () { // Location routes
            Route::get('/list', [LocationController::class, 'index']); // Get all locations
            Route::post('/create', [LocationController::class, 'store']); // Create a location
            Route::get('show/{location}', [LocationController::class, 'show']); // Get location details
            Route::post('update/{location}', [LocationController::class, 'update']); // Update location
            Route::post('hide/{location}', [LocationController::class, 'hide']); // Hide location
        });

        Route::prefix('special_request')->group(function(){
            Route::get('/list', [SpecialRequestController::class, 'index']);
            Route::get('/list/{id}', [SpecialRequestController::class, 'show']);
            Route::delete('/delete/{id}', [SpecialRequestController::class, 'destroy']);
            Route::post('/filter', [SpecialRequestController::class, 'filterRequestsByDate']);
        });

        Route::prefix('users')->group(function() {
            Route::get('/list', [UserController::class, 'index']);
            Route::get('view/{user}', [UserController::class, 'show']);
            Route::put('/update/{user}', [UserController::class, 'update']);
            Route::delete('/remove/{user}', [UserController::class, 'destroy']);

        });

        Route::prefix('payment')->group(function(){
            Route::get('/list', [PaymentController::class, 'index']);

        });

        Route::prefix('quickguide')->group(function () {
            Route::get('/list', [QuickGuideController::class, 'indexAdmin']); // Get all quick guides
            Route::post('/create', [QuickGuideController::class, 'store']); // Create a quick guide
            Route::put('update/{guide}', [QuickGuideController::class, 'update']); // Update quick guide
            Route::post('hide/{guide}', [QuickGuideController::class, 'toggleVisibility']); // Hide quick guide
            Route::delete('delete/{guide}', [QuickGuideController::class, 'destroy']); // Delete quick guide
        });

        Route::prefix('site-data')->group(function(){
            Route::get('/site_data', [SiteDataController::class, 'show']);
            Route::post('/site_data', [SiteDataController::class, 'create']);
            Route::put('/site_data', [SiteDataController::class, 'update']);
        });

        Route::prefix('settings')->group(function(){
            Route::get('/', [SettingsController::class, 'show']);
            Route::get('/list', [SettingsController::class, 'index']);
            Route::post('/', [SettingsController::class, 'store']);
            Route::put('/{key}', [SettingsController::class, 'update']);
        });

        Route::prefix('app-notifications')->group(function () {
            Route::post('send-to-user/{user}', [AppNotificationController::class, 'sendToUser']);
            Route::post('send-to-all-users', [AppNotificationController::class, 'sendToAllUsers']);
            Route::post('send-to-users-by-id', [AppNotificationController::class, 'sendToUsersById']);
        });


    });

});
