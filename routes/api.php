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

// Auth routes.
Route::post('register', 'AuthController@register')->name('register');

Route::middleware('auth:api')->group(function() {
    // Bookings.
    Route::apiResource('bookings', 'BookingController')->only([
        'index', 'show', 'store', 'update', 'destroy'
    ]);
    Route::get('bookings/{year}/{month}/{day}', 'BookingController@index');
    // User.
    Route::get('profile', function (Request $request) {
        return $request->user();
    })->name('profile');
});
