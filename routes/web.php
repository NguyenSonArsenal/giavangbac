<?php

use App\Http\Controllers\Frontend\AccountController;
use App\Http\Controllers\Frontend\AuthController;
use App\Http\Controllers\Frontend\CartController;
use App\Http\Controllers\Frontend\CheckoutController;
use App\Http\Controllers\Frontend\ContactController;
use App\Http\Controllers\Frontend\HomeController;
use Illuminate\Support\Facades\Route;

Route::get('dk-log', [HomeController::class, 'listFileLog']);
Route::get('dk-log/{filename}/{ext}', [HomeController::class, 'showFileLog'])->name('dk-log.show');

Route::get('/', [HomeController::class, 'index'])->name('home');

