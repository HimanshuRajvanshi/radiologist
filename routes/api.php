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


// Route::group(['middleware' => 'auth:api'], function(){
//     Route::get('blogs', 'API\APIController@blogs');
// });

Route::Post('login', 'API\APIController@login');
Route::Post('registation', 'API\APIController@register');
Route::Post('post/case', 'API\APIController@postCase'); // this is for save cases
Route::GET('get/case', 'API\APIController@getCase')->name('get_case'); // Get All Cases

Route::Post('post/achievement', 'API\APIController@postAchievement'); // this is for save cases
Route::GET('get/achievement', 'API\APIController@getAchievement'); // Get All Cases

Route::Post('updatePhoto', 'API\APIController@proflePhotoUpdate');
Route::Post('do-like-case','API\APIController@doLikeCase');
Route::Post('do-comment-case','API\APIController@doCommentCase');
Route::get('get-case-like-comment','API\APIController@getCaseLikeComment');
Route::get('detail','API\APIController@getDetails');
Route::get('getcategory','API\APIController@categorylist');
Route::get('doctorlist/list','API\APIController@getDoctor');