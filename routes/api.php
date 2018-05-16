<?php

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

Route::get('/friend', 'Profile\FriendsController@getAll');
Route::get('/friend/{id}', 'Profile\FriendsController@get');
Route::delete('/friend/{id}', 'Profile\FriendsController@delete');
Route::post('/friend/{id}', 'Profile\FriendsController@add');

Route::get('/statistic/{person_id}', 'Profile\VisitStatisticController@get');
Route::get('/statistic/{person_id}/friend', 'Profile\VisitStatisticController@getFriendList');
