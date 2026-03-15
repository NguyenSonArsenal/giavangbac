<?php

use Illuminate\Support\Facades\Route;

Route::get('login',  'AuthController@showFormLogin')->name('auth.login');
Route::post('login', 'AuthController@postLogin')->name('auth.login.post');
Route::get('logout', 'AuthController@logout')->name('auth.logout');

Route::get('/', 'DashboardController@index')->name('dashboard');
Route::get('stats', 'StatsController@index')->name('stats');

Route::resource('post', 'PostController');
Route::post('tinymce_editor/upload', 'PostController@upload')->name('tinymce_editor.upload');
