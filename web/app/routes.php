<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the Closure to execute when that URI is requested.
|
*/


/* Route::post('api/users', 'UsersController@apiSignup'); */

/*
Route::get('signup', 'UsersController@getSignup');
Route::post('signup', 'UsersController@postSignup');
*/

Route::group(array('prefix' => 'api', 'before' => 'apiauth'), function()
{
	Route::post('login', 'UsersController@login');
	Route::get('logout', 'UsersController@logout');
	Route::resource('users', 'UsersController');
	Route::resource('tags', 'TagsController');
	Route::resource('timers', 'TimersController');
	Route::resource('hours', 'HoursController');
});
Route::post('api/users', 'UsersController@apiSignup');

Route::get('{all}', 'PageController@index')->where('all', '.*');