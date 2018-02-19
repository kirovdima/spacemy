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

Route::middleware('auth:api')->get('/friend', 'Profile\FriendsController@get');
Route::middleware('auth:api')->delete('/friend/{id}', 'Profile\FriendsController@delete');
Route::middleware('auth:api')->post('/friend/{id}', 'Profile\FriendsController@add');

Route::middleware('auth:api')->get('/statistic/{person_id}', 'Profile\VisitStatisticController@get');
