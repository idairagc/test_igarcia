<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});


Route::post('register', 'API\RegisterController@register');
Route::post('login', 'API\LoginController@login');
   
Route::middleware('auth:api')->group( function () {
    //aqui irán el resto de rutas
    Route::resource('users', 'API\UserController');
    //como el update de customers puede tener una imagen, hay que pasarlo con post
    Route::post('customers/{customer}', 'API\CustomerController@update');
    Route::resource('customers', 'API\CustomerController');
});