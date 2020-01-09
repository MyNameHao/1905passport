<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});
Route::post('/user/reg','User\UserController@reg');
Route::post('/user/login','User\UserController@login');
Route::post('/user/getuserinfo','User\UserController@getuserinfo');
Route::post('/user/gettoken','User\UserController@gettoken');
Route::post('/test/github','User\UserController@github');
