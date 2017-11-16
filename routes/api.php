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
Route::group(['namespace' => 'Auth'], function () {
    Route::post('send-magic-link', 'MagicLinkController@send');
    Route::post('login', 'LoginController@login');
    Route::post('register', 'RegisterController@register');
});