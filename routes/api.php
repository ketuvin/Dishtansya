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

Route::prefix('v1')->group(function() {
    Route::post('register', 'Api\AuthController@register');
    Route::post('login', 'Api\AuthController@login');

    Route::group(['middleware' => 'auth:api'], function() {
        Route::post('logout', 'Api\AuthController@logout');
        Route::post('order', 'Api\OrderController@orderProduct');
        Route::get('product/{product_id}', 'Api\OrderController@getProduct');
    });
});
