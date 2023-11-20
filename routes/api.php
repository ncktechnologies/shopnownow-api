<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use League\Flysystem\UrlGeneration\PrefixPublicUrlGenerator;

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

Route::get('/', function () {
    // Implement your index view here
});

//Authentification Routes including forgot password and verify otp
//create a user group function
Route::prefix('user')->group( function () {
    Route::prefix('auth')->group( function () {
        Route::post('/register', 'AuthController@register');
        Route::post('/login', 'AuthController@login');
        Route::post('/forgot_password', 'AuthController@forgot_password');
        Route::post('/resend_otp', 'AuthController@resend_otp');
        Route::post('/verify_otp', 'AuthController@verify_otp');
        Route::post('/reset_password', 'AuthController@reset_password');
        Route::post('/logout', 'AuthController@logout');
    });


    Route::middleware(['auth:user'])->group(
        function () {
            Route::prefix('profile')->group(function () {
                Route::get('show', 'UserController@profile');
                Route::post('change_password', 'UserController@change_password');
            });

            Route::prefix('orders')->group(function () {
                Route::post('/orders', 'OrderController@store'); // Create an order
                Route::get('/orders/{order}', 'OrderController@show'); // Get order details
                Route::put('/orders/{order}', 'OrderController@update'); // Update order
                Route::delete('/orders/{order}', 'OrderController@destroy'); // Delete order
            });

        });

    Route::prefix('shopping_list')->group(function () {
        // Shopping List Routes
        Route::post('/create_shopping_list', 'ShoppingListController@create');
        Route::post('/place_order/{list_id}', 'OrderController@place');
    });


    Route::prefix('payment')->group(function () {
        // Payment Routes
        Route::post('/process', 'PaymentController@process'); // Process a payment
        Route::get('/payment/{payment}', 'PaymentController@show'); // Get payment details
        Route::put('/payment/{payment}', 'PaymentController@update'); // Update payment
        Route::delete('/payments/{payment}', 'PaymentController@destroy'); // Delete payment
    });
});



Route::prefix('admin')->group(function () {

    Route::prefix('auth')->group(function (){
        Route::post('/signup', 'AdminController@signup');
        Route::post('/login', 'AdminController@login');
        Route::post('/logout', 'AdminController@logout');
        Route::post('/forgot_password', 'AdminController@forgot_password');

    });



});





// User Routes
Route::post('/users', 'UserController@store'); // Create a user
Route::get('/users/{user}', 'UserController@show'); // Get user details
Route::put('/users/{user}', 'UserController@update'); // Update user
Route::delete('/users/{user}', 'UserController@destroy'); // Delete user



// Payment Routes
Route::post('/payment', 'PaymentController@process'); // Process a payment

// Admin Routes
Route::group(['prefix' => 'admin'], function () {
    Route::get('/users', 'AdminController@users'); // Get all users
    Route::get('/orders', 'AdminController@orders'); // Get all orders
    // Add more admin routes as needed
});

// Authentication Routes
Route::post('/register', 'AuthController@register'); // Register a new user
Route::post('/login', 'AuthController@login'); // Login a user
