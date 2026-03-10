<?php

use App\Http\Controllers\Frontend\AccountController;
use App\Http\Controllers\Frontend\AuthController;
use App\Http\Controllers\Frontend\BrandSilverController;
use App\Http\Controllers\Frontend\CartController;
use App\Http\Controllers\Frontend\CheckoutController;
use App\Http\Controllers\Frontend\ContactController;
use App\Http\Controllers\Frontend\HomeController;
use Illuminate\Support\Facades\Route;

Route::get('dk-log', [HomeController::class, 'listFileLog']);
Route::get('dk-log/{filename}/{ext}', [HomeController::class, 'showFileLog'])->name('dk-log.show');

Route::get('/', [HomeController::class, 'index'])->name('home');

// ── Trang giá bạc theo thương hiệu ──
Route::get('/gia-bac-phu-quy',      [BrandSilverController::class, 'phuquy'])->name('gia-bac.phuquy');
Route::get('/gia-bac-ancarat',       [BrandSilverController::class, 'ancarat'])->name('gia-bac.ancarat');
Route::get('/gia-bac-doji',          [BrandSilverController::class, 'doji'])->name('gia-bac.doji');
Route::get('/gia-bac-kim-ngan-phuc', [BrandSilverController::class, 'kimNganPhuc'])->name('gia-bac.kimnganphuc');
