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

    Route::get('songs', 'SongController@index');
    Route::get('songs/status', 'SongController@verify');
    Route::get('songs/{id}', 'SongController@show');
    Route::get('songs/user/{user_id}', 'SongController@userSongs');
    Route::post('songs', 'SongController@store');
    Route::put('songs/{song}', 'SongController@update');
    Route::delete('songs/{id}', 'SongController@destroy');

    Route::post('songs/{id}/ratings', 'RatingController@store');
    Route::get('ratings', 'RatingController@show');

    Route::get('users', 'UserController@index');
    Route::get('users/{id}', 'UserController@show');
    Route::delete('users/{id}', 'UserController@destroy');

    Route::get('playlists', 'PlaylistController@index');
    Route::get('playlists/{id}', 'PlaylistController@show');
    Route::post('playlists', 'PlaylistController@create');
    Route::post('playlists/{playlist_id}/{id}', 'PlaylistController@add');
    Route::delete('playlists/{id}', 'PlaylistController@destroy');
    Route::get('playlists/{playlist}/{id}', 'PlaylistController@remove');
});


