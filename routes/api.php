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

Route::post('users', 'UsersController@store'); // Change to post for creating user
Route::get('users/current', 'UsersController@show');
Route::patch('users/current', 'UsersController@update');
//Route::resource('users.current', 'CurrentUsersController');

Route::resource('chats', 'ChatsController');
//Route::patch('chats/{chat}', 'ChatsController@update');
Route::resource('chats.chat_messages', 'ChatMessagesController');

//Route::group(['middleware' => ['api']], function () {
  Route::resource('auth/logout', 'AuthController@logout');
  Route::resource('auth/login', 'AuthController@login'); // Change to post
//});

//Route::resource('test', 'TestController');
