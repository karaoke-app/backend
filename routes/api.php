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

    /*Route::middleware('auth:api')->get('/user', function (Request $request) {
        return $request->user();
    });*/

    Route::get('/provider/{provider}', 'Api\AuthController@redirectToProvider')->name('redirectToProvider');
    Route::get('/provider/{provider}/callback', 'Api\AuthController@handleProviderCallback');

    Route::post('login', 'Api\AuthController@login');
    Route::post('register', 'Api\AuthController@register');

Route::group(['middleware' => 'auth.jwt'], function () {
    Route::get('logout', 'Api\AuthController@logout');
    Route::post('logout', 'Api\AuthController@logout');
    Route::post('refresh', 'Api\AuthController@refresh');
    Route::post('me', 'Api\AuthController@me');
});
