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

Route::get('/', 'Auth\LoginController@index')
    ->name('main');
Route::get('/demo/visits', 'Auth\LoginController@index')
    ->name('demo_visits');
Route::get('/demo/friends', 'Auth\LoginController@index')
    ->name('demo_friends');
Route::get('/about', 'Auth\LoginController@index')
    ->name('about');

Route::get('/friends', 'Auth\LoginController@friends');

Route::get('/login', 'Auth\LoginController@login');
Route::get('/verify', 'Auth\LoginController@verify');

Route::get('/statistic/{person_id}/visits', function () {
    return view('layouts.profile', ['api_token' => \Auth::user() ? \Auth::user()->api_token : '']);
});
Route::get('/statistic/{person_id}/friends', function () {
    return view('layouts.profile', ['api_token' => \Auth::user() ? \Auth::user()->api_token : '']);
});