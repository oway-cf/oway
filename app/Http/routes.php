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


Route::group(['prefix'=>'login'], function(){
	Route::get('vk', function(){
		$params = [
			'client_id'=>5296087,
			'redirect_uri'=>URL::to('vk/callback');,
		];
// 		ID приложения:	5296087
// Защищенный ключ:	
// gQWWfODjbozPvwtdkaVn

// 		client_id Обязательно	Идентификатор Вашего приложения.
// redirect_uri Обязательно	Адрес, на который будет переадресован пользователь после прохождения авторизации (домен указанного адреса должен соответствовать основному домену в настройках приложения и перечисленным значениям в списке доверенных redirect uri - адреса сравниваются вплоть до path-части).
// display Обязательно	Указывает тип отображения страницы авторизации. Поддерживаются следующие варианты:
// page — форма авторизации в отдельном окне;
// popup — всплывающее окно;
// mobile — авторизация для мобильных устройств (без использования Javascript)
// Если пользователь авторизуется с мобильного устройства, будет использован тип mobile.
// scope	Битовая маска настроек доступа приложения, которые необходимо проверить при авторизации пользователя и запросить, в случае отсутствия необходимых.
// response_type	Тип ответа, который Вы хотите получить. Укажите code, чтобы осуществляеть запросы со стороннего сервера.
// v	Версия API, которую Вы используете. Актуальная версия: 5.45.
// state	Произвольная строка, которая будет возвращена вместе с результатом авторизации.
		Log::info();
		return redirect()->to('https://oauth.vk.com/authorize');
	});
	Route::get('vk/callback', function(){

	});
});