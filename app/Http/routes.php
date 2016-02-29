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
    return view('index');
});

Route::group(['prefix' => 'api'], function () {
    Route::get('suggest/address/{query?}', 'SuggestController@address');
    Route::group(['prefix' => 'list'], function () {
        Route::post('/', 'ListController@create');
        Route::post('/{id}/update', 'ListController@update');
        Route::get('/{id}', 'ListController@show');
        Route::get('/{id}/way', 'WayController@show');
    });
});
