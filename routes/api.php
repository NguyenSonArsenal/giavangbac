<?php

use App\Http\Controllers\Api\SilverPriceController;
use App\Http\Controllers\Api\SilverTrendController;
use App\Http\Controllers\Api\AncaratPriceController;
use App\Http\Controllers\Api\DojiPriceController;
use App\Http\Controllers\Api\KimNganPhucPriceController;
use App\Http\Controllers\Api\BtmhGoldController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Internal API – chỉ cho phép gọi từ chính domain của site
Route::middleware('internal.api')->group(function () {
    // Silver price – Phú Quý
    Route::prefix('silver')->group(function () {
        Route::get('current', [SilverPriceController::class, 'currentPrice']);
        Route::get('history', [SilverPriceController::class, 'history']);
        Route::get('percent', [SilverPriceController::class, 'percent']);
        Route::get('trend', [SilverTrendController::class , 'trend']);
    });

    // Silver price – Ancarat
    Route::prefix('ancarat')->group(function () {
        Route::get('current', [AncaratPriceController::class, 'currentPrice']);
        Route::get('history', [AncaratPriceController::class, 'history']);
        Route::get('percent', [AncaratPriceController::class, 'percent']);
    });

    // Silver price – DOJI
    Route::prefix('doji')->group(function () {
        Route::get('current', [DojiPriceController::class, 'currentPrice']);
        Route::get('history', [DojiPriceController::class, 'history']);
        Route::get('percent', [DojiPriceController::class, 'percent']);
    });

    // Silver price – Kim Ngân Phúc
    Route::prefix('kimnganphuc')->group(function () {
        Route::get('current', [KimNganPhucPriceController::class, 'currentPrice']);
        Route::get('history', [KimNganPhucPriceController::class, 'history']);
        Route::get('percent', [KimNganPhucPriceController::class, 'percent']);
    });

    // Gold price – BTMC
    Route::prefix('gold/btmc')->group(function () {
        Route::get('current', [App\Http\Controllers\Api\BtmcGoldController::class, 'currentPrice']);
        Route::get('history', [App\Http\Controllers\Api\BtmcGoldController::class, 'history']);
    });

    // Gold price – BTMH (Bảo Tín Mạnh Hải)
    Route::prefix('gold/btmh')->group(function () {
        Route::get('current', [BtmhGoldController::class, 'currentPrice']);
        Route::get('history', [BtmhGoldController::class, 'history']);
    });
});
