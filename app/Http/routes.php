<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::get('/', 'WelcomeController@index');

Route::get('home', 'HomeController@index');

Route::get('deliveries', 'DeliveriesController@index');

Route::get('deliveries_2', 'Deliveries2Controller@index');

Route::get('word_cloud', 'Deliveries2Controller@getWord_cloud');
Route::get('word_cloud_dinamica', 'Deliveries2Controller@getWord_cloud_dinamica');

Route::controllers([
	'auth' => 'Auth\AuthController',
	'password' => 'Auth\PasswordController',
]);
