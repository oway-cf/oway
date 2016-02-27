<?php

/*
|--------------------------------------------------------------------------
| Routes File
|--------------------------------------------------------------------------
|
| Here is where you will register all of the routes in an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::group(['prefix' => 'api'], function () {
    Route::get('suggest/address', 'SuggestController@address');
    Route::get('suggest/keyword', 'SuggestController@keyword');
    Route::get('suggest/firm', 'SuggestController@firms');
    Route::get('suggest', 'SuggestController@index');
    Route::group(['prefix' => 'list'], function () {
        Route::post('/', 'ListController@create');
        Route::put('/{id}', 'ListController@update');
        Route::get('/{id}', 'ListController@show');
        Route::get('/{id}/way', 'WayController@show');
    });
});

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| This route group applies the "web" middleware group to every route
| it contains. The "web" middleware group is defined in your HTTP
| kernel and includes session state, CSRF protection, and more.
|
*/

Route::group(['middleware' => ['web']], function () {
    //
});
