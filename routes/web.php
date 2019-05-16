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

Route::get('test/test','Test\TestController@test');

Route::get('test/rec','Test\TestController@test_rec');

Route::get('test/caesar','Test\TestController@caesar');
Route::get('test/decrypt','Test\TestController@decrypt');
//ajax请求
Route::any('test/ajax','Test\TestController@ajaxTest');

//注册
Route::get('reg','User\UserController@register');
Route::post('regAdd','User\UserController@registerAdd');

Route::get('login','User\UserController@login');
Route::post('logindo','User\UserController@logindo');


