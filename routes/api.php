<?php

use Illuminate\Http\Request;

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

Route::post('login', 'API\UserController@login');
Route::post('register', 'API\UserController@register');


Route::middleware('auth:api')->group( function () {
	Route::post('details', 'API\UserController@details');
	Route::get('list', 'API\UserController@list');
	Route::post('role', 'API\UserController@newRole');
});
// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });
