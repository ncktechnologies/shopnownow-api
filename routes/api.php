<?php

use Illuminate\Http\Request;
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

Route::get('/', function () {
    // Implement your index view here
});

Route::post('/create_shopping_list', 'ShoppingListController@create');
Route::post('/place_order/{list_id}', 'OrderController@place');


// User Routes
Route::post('/users', 'UserController@store'); // Create a user
Route::get('/users/{user}', 'UserController@show'); // Get user details
Route::put('/users/{user}', 'UserController@update'); // Update user
Route::delete('/users/{user}', 'UserController@destroy'); // Delete user

// Order Routes
Route::post('/orders', 'OrderController@store'); // Create an order
Route::get('/orders/{order}', 'OrderController@show'); // Get order details
Route::put('/orders/{order}', 'OrderController@update'); // Update order
Route::delete('/orders/{order}', 'OrderController@destroy'); // Delete order
