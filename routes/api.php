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

Route::get('/friend', 'Profile\FriendsController@getAll')->name('get_friends');
Route::get('/friend/{id}', 'Profile\FriendsController@get')->name('get_friend');
Route::delete('/friend/{id}', 'Profile\FriendsController@delete')->name('delete_friend');
Route::post('/friend/{id}', 'Profile\FriendsController@add')->name('add_friend');

Route::get('/statistic/{person_id}/friend', 'Profile\VisitStatisticController@getFriendList')->name('friends_statistic');
Route::get('/statistic/{person_id}/{period}/{start_date}', 'Profile\VisitStatisticController@get')->name('visit_statistic');
