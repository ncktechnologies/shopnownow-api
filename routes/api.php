<?php

use Illuminate\Http\Request;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ShoppingListController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\BandController;
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

        Route::middleware(['auth:user'])->group(function () {
            Route::prefix('profile')->group(function () {
                Route::get('show', [UserController::class, 'profile']);
                Route::post('change_password', [UserController::class, 'change_password']);
            });

            Route::prefix('orders')->group(function () {
                Route::post('/orders', [OrderController::class, 'store']); // Create an order
                Route::get('/orders/{order}', [OrderController::class, 'show']); // Get order details
                Route::put('/orders/{order}', [OrderController::class, 'update']); // Update order
                Route::delete('/orders/{order}', [OrderController::class, 'destroy']); // Delete order
            });
        });

        Route::prefix('shopping_list')->group(function () {
            Route::post('/create_shopping_list', [ShoppingListController::class, 'create']);
            Route::post('/place_order/{list_id}', [OrderController::class, 'place']);
        });

        Route::prefix('payment')->group(function () {
            Route::post('/process', [PaymentController::class, 'process']);
            Route::get('/payment/{payment}', [PaymentController::class, 'show']);
            Route::put('/payment/{payment}', [PaymentController::class, 'update']);
            Route::delete('/payments/{payment}', [PaymentController::class, 'destroy']);
        });
    });

    Route::prefix('admin')->group(function () {
        Route::prefix('auth')->group(function () {
            Route::post('/signup', [AdminController::class, 'signup']);
            Route::post('/login', [AdminController::class, 'login']);
            Route::post('/logout', [AdminController::class, 'logout']);
            Route::post('/forgot_password', [AdminController::class, 'forgot_password']);
        });

        Route::prefix('band')->group(function () { // Band routes
            Route::get('/list', [BandController::class, 'index']); // Get all bands
            Route::post('/create', [BandController::class, 'create']); // Create a band
            Route::get('show/{band}', [BandController::class, 'show']); // Get band details
            Route::put('update/{band}', [BandController::class, 'update']); // Update band
            Route::post('hide/{band}', [BandController::class, 'hide']); // Delete band
        });

        Route::prefix('category')->group(function () { // Category routes
            Route::get('/list', [CategoryController::class, 'index']); // Get all categories
            Route::post('/create', [CategoryController::class, 'create']); // Create a category
            Route::get('show/{category}', [CategoryController::class, 'show']); // Get category details
            Route::put('update/{category}', [CategoryController::class, 'update']); // Update category
            Route::post('hide/{category}', [CategoryController::class, 'hide']); // Hide category
        });

        });


    Route::post('/users', [UserController::class, 'store']);
    Route::get('/users/{user}', [UserController::class, 'show']);
    Route::put('/users/{user}', [UserController::class, 'update']);
    Route::delete('/users/{user}', [UserController::class, 'destroy']);

    Route::post('/payment', [PaymentController::class, 'process']);

    Route::group(['prefix' => 'admin'], function () {
        Route::get('/users', [AdminController::class, 'users']);
        Route::get('/orders', [AdminController::class, 'orders']);
        // Add more admin routes as needed
    });

    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);

    });





