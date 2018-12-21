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

Route::prefix('auth')->namespace('Auth')->group(function() {
    Route::post('register', 'RegisterController@register')->name('register');
});

Route::middleware('auth:api')->group(function() {
    Route::prefix('bookings')->group(function() {
        Route::get('/', 'BookingController@index');
        Route::post('/', 'BookingController@create');
    });
});

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
