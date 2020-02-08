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
Route::post('/user/auth','User\UserController@auth');
Route::post('/test/github','User\UserController@github');//


Route::get('/test/verify','User\UserController@verify');//
Route::post('/test/postsign','User\UserController@postsign');//
Route::get('/test/sslencr','User\UserController@sslencr');//
Route::get('/test/sslcbc','User\UserController@sslcbc');//
