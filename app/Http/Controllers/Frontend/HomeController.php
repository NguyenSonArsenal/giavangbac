<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Post;

class HomeController extends Controller
{
    public function index()
    {
        // Bài nổi bật
        $featuredPost = Post::active()
            ->where('is_featured', true)
            ->with('category')
            ->latest()
            ->first();

        // Bài mới nhất (trừ bài nổi bật)
        $latestPosts = Post::active()
            ->with('category')
            ->when($featuredPost, function ($q) use ($featuredPost) {
                $q->where('id', '!=', $featuredPost->id);
            })
            ->latest()
            ->limit(4)
            ->get();

        // Categories active
        $newsCategories = Category::where('status', 1)
            ->withCount(['posts' => function ($q) {
                $q->where('status', 1);
            }])
            ->having('posts_count', '>', 0)
            ->orderBy('name')
            ->get();

        return view('frontend.home.index', compact(
            'featuredPost', 'latestPosts', 'newsCategories'
        ));
    }

    public function jenkin()
    {
        dd('jenkin v1');
    }
}
