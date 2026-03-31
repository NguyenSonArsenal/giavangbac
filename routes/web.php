<?php

use App\Http\Controllers\Frontend\ArticleController;
use App\Http\Controllers\Frontend\BrandSilverController;
use App\Http\Controllers\Frontend\CategoryController;
use App\Http\Controllers\Frontend\ContactController;
use App\Http\Controllers\Frontend\HomeController;
use App\Http\Controllers\Frontend\PostController;
use App\Http\Controllers\Frontend\SitemapController;
use App\Http\Controllers\Frontend\ToolController;
use App\Http\Controllers\Frontend\ServerMonitorController;
use Illuminate\Support\Facades\Route;

Route::get('/sitemap.xml', [SitemapController::class, 'index']);

Route::get('dk-log', [HomeController::class, 'listFileLog']);
Route::get('dk-log/{filename}/{ext}', [HomeController::class, 'showFileLog'])->name('dk-log.show');
Route::get('dk-stats', 'App\Http\Controllers\Frontend\StatsController@index');
Route::get('dk-server', [ServerMonitorController::class, 'index']);

Route::get('/', [HomeController::class, 'index'])->name('home');

// ── Trang giá bạc theo thương hiệu ──
Route::get('/gia-bac-phu-quy',      [BrandSilverController::class, 'phuquy'])->name('gia-bac.phuquy');
Route::get('/gia-bac-ancarat',       [BrandSilverController::class, 'ancarat'])->name('gia-bac.ancarat');
Route::get('/gia-bac-doji',          [BrandSilverController::class, 'doji'])->name('gia-bac.doji');
Route::get('/gia-bac-kim-ngan-phuc', [BrandSilverController::class, 'kimNganPhuc'])->name('gia-bac.kimnganphuc');

// ── Trang công cụ SEO ──
Route::get('/quy-doi-bac',     [ToolController::class, 'quyDoi'])->name('tool.quydoi');
Route::get('/so-sanh-gia-bac', [ToolController::class, 'soSanh'])->name('tool.sosanh');
Route::get('/lich-su-gia-bac', [ToolController::class, 'lichSu'])->name('tool.lichsu');

// ── Trang thông tin SEO (authority) ──
Route::get('/bac-999-la-gi',               [ArticleController::class, 'bac999LaGi'])->name('article.bac999');
Route::get('/nen-mua-bac-o-dau',           [ArticleController::class, 'nenMuaBacODau'])->name('article.muabac');
Route::get('/bac-co-phai-kenh-dau-tu-tot', [ArticleController::class, 'dauTuBac'])->name('article.dautu');

// ── Bài viết (CMS) ──
Route::get('/bai-viet',        [PostController::class, 'index'])->name('post.index');

Route::get('category',        [CategoryController::class, 'index'])->name('category.index');
Route::get('category/{slug}', [CategoryController::class, 'show'])->name('category.show');
Route::get('post/{slug}', [PostController::class, 'show'])->name('post.show');

// ── Liên hệ ──
Route::get('/lien-he',  [ContactController::class, 'index'])->name('contact.index');
Route::post('/lien-he', [ContactController::class, 'store'])->name('contact.store');

// ── Bình luận ──
Route::post('/post/{postId}/comment', [\App\Http\Controllers\Frontend\CommentController::class, 'store'])->name('comment.store');
