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

//rutas sin autentificacion
Route::post('login', 'API\LoginController@login');
 
//las rutas middleware, podría haberlas puesto en constructores en los modelos, pero creo que aquí se ve más claro.
//rutas que necesitan autentificacion
Route::middleware('auth:api')->group( function () {
    //rutas a las que sólo puede acceder el admin
    Route::group(['middleware' => ['role:Admin']], function () {
    	Route::post('users/{users}', 'API\UserController@changeRole')->name('users.changeRole');
    	Route::resource('users', 'API\UserController',['except' => ['create', 'edit']]);
	});
    //Rutas a las que pueden acceder el admin y los employee
	Route::group(['middleware' => ['role:Admin|Employee']], function () {
	    //como el update de customers puede tener una imagen, hay que pasarlo con post
	    Route::post('customers/{customer}', 'API\CustomerController@update');
	    Route::resource('customers', 'API\CustomerController',['except' => ['create', 'edit']]);
	});
});