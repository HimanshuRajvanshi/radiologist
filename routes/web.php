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

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');


Route::get('/get/blog', 'HomeController@getBlogs')->name('get_blog');
Route::get('/status/blog/{update}/{id}', 'HomeController@statusBlogUpdate');

Route::get('/get/offer', 'HomeController@getoffers')->name('get_offer');

Route::get('/get/video', 'HomeController@getVideos')->name('get_video');

Route::get('/status/update/{typ}/{update}/{id}', 'HomeController@statusUpdate');

