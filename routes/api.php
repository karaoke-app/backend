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
    Route::get('activate/{id}', 'Api\AuthController@activate')->name('activate');
    Route::post('reactivate', 'Api\AuthController@reactivate');
    Route::post('register', 'Api\AuthController@register');

    Route::get('songs', 'SongController@index')->name('songs.index');
    Route::get('songs/{id}', 'SongController@show');
    Route::get('songs/user/{user_id}', 'SongController@userSongs');

    Route::get('ratings', 'RatingController@show');

    Route::get('users', 'UserController@index');
    Route::get('users/{id}', 'UserController@show');

    Route::get('playlists/{name}', 'PlaylistController@userPlaylist');
    Route::get('playlists/{id}', 'PlaylistController@show');

    Route::get('categories', 'CategoryController@index');
    Route::get('categories/{id}', 'CategoryController@show');

Route::group(['middleware' => 'auth.jwt'], function () {
    Route::get('logout', 'Api\AuthController@logout');
    Route::post('logout', 'Api\AuthController@logout');
    Route::post('refresh', 'Api\AuthController@refresh');
    Route::post('me', 'Api\AuthController@me');

    Route::post('songs', 'SongController@store');
    Route::put('songs/{song}', 'SongController@update');
    Route::delete('songs/{id}', 'SongController@destroy');

    Route::post('songs/{id}/report', 'ReportController@create');

    Route::get('lyrics/import', 'LyricsController@import');

    Route::put('users/password', 'UserController@changePassword');
    Route::put('users/username', 'UserController@changeUsername');
    Route::put('users/deactivate', 'UserController@deactivation');
    Route::delete('users/{name}', 'UserController@delete');

    Route::post('songs/{id}/ratings', 'RatingController@store');

    Route::get('playlists', 'PlaylistController@index');
    Route::post('playlists', 'PlaylistController@create');
    Route::post('playlists/{playlist_id}/{id}', 'PlaylistController@add');
    Route::delete('playlists/{id}', 'PlaylistController@destroy');
    Route::get('playlists/{playlist}/{id}', 'PlaylistController@remove');
});

Route::group(['middleware' => ['auth.jwt', 'admin']], function () {
    Route::get('songs/status', 'SongController@verify');

    Route::delete('users/{id}', 'UserController@destroy');

    Route::post('categories', 'CategoryController@create');
    Route::delete('categories/{id}', 'CategoryController@destroy');
    Route::get('categories/{category}/{id}', 'CategoryController@remove');

    Route::get('reports', 'ReportController@index');
    Route::get('reports/{id}', 'ReportController@show');
});
