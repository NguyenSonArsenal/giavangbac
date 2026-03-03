<?php

use App\Http\Controllers\Backend\AuthController;
use App\Http\Controllers\Backend\ContactController;
use App\Http\Controllers\Backend\DashboardController;
use Illuminate\Support\Facades\Route;

Route::get('login', [AuthController::class, 'showFormLogin'])->name('auth.login');
Route::post('login', [AuthController::class, 'postLogin'])->name('auth.login.post');
Route::get('logout', [AuthController::class, 'logout'])->name('auth.logout');

Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
Route::group(['prefix'=>'category/', 'as'=>'category.'], function(){
    Route::get('/', ['as' => 'index', 'uses' => 'CategoryController@index']);
    Route::get('/create', ['as' => 'create', 'uses' => 'CategoryController@create']);
    Route::post('/store', ['as' => 'store', 'uses' => 'CategoryController@store']);
    Route::get('/{id}/edit', ['as' => 'edit', 'uses' => 'CategoryController@edit']);
    Route::delete('/{id}', ['as' => 'destroy', 'uses' => 'CategoryController@destroy']);
    Route::get('test/{id}', ['as' => 'test', 'uses' => 'CategoryController@test']);
    Route::post('reorder', ['as' => 'reorder', 'uses' => 'CategoryController@reorder']);
    Route::post('/{id}', ['as' => 'update', 'uses' => 'CategoryController@update']);
});

Route::resource('new', 'NewController');
Route::resource('product', 'ProductController');
Route::resource('contact', 'ContactController')->only(['index', 'show', 'destroy']);
Route::post('contact/{id}/status', [ContactController::class, 'updateStatus'])->name('contact.status');
Route::post('tinymce_editor/upload', ['as' => 'tinymce_editor.upload', 'uses' => 'TinyMceEditorController@upload']);




//    Route::get('forgot-password', [AuthController::class, 'forgotPassword'])->name('forgot-password');
//    Route::post('forgot-password', [AuthController::class, 'postForgotPassword'])->name('forgot-password.post');
//    Route::get('recovery-password/{otp}', [AuthController::class, 'getRecoveryPassword'])->name('recovery-password');
//    Route::post('recovery-password/{otp}', [AuthController::class, 'postRecoveryPassword'])->name('recovery-password.post');

//
//    Route::group(['middleware' => ['authBackend']], function () {
//        Route::get('logout', [AuthController::class, 'logout'])->name('logout');
//        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
//
//        // ========== Module User ==========
//        Route::group(['prefix' => 'user/', 'as' => 'user.'], function () {
//            Route::get('/', [UserController::class, 'index'])->name('index');
//            Route::get('/create', [UserController::class, 'create'])->name('create');
//            Route::post('/store', [UserController::class, 'store'])->name('store');
//            Route::get('/edit/{id}', [UserController::class, 'edit'])->name('edit');
//            Route::post('/update', [UserController::class, 'update'])->name('update');
//            Route::delete('/{id}', [UserController::class, 'destroy'])->name('destroy');
//        });
//
//        // ========== Module Tracking Email ==========
//        Route::group(['prefix' => 'tracking-email/', 'as' => 'tracking_email.'], function () {
//            Route::get('/', [TrackingEmailController::class, 'index'])->name('index');
//            Route::get('create', [TrackingEmailController::class, 'create'])->name('create');
//            Route::post('store', [TrackingEmailController::class, 'store'])->name('store');
//        });
//
//        Route::get('coin', [CommonController::class, 'coin'])->name('coin.index');
//        Route::post('coin-new', [CommonController::class, 'getLastPriceCoin'])->name('coin.new');
//    });
