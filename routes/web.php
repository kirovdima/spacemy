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

Route::get('/', 'Auth\LoginController@index');

Route::get('/signin', 'Auth\LoginController@signin');
Route::get('/signin/demo/friends', 'Auth\LoginController@signin');

Route::get('/login', 'Auth\LoginController@login');
Route::get('/verify', 'Auth\LoginController@verify');

Route::get('/statistic/{person_id}/visits', function () {
    return view('layouts.profile', ['api_token' => \Auth::user() ? \Auth::user()->api_token : '']);
});
Route::get('/statistic/{person_id}/friends', function () {
    return view('layouts.profile', ['api_token' => \Auth::user() ? \Auth::user()->api_token : '']);
});