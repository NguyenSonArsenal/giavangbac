<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Post;

class PostController extends Controller
{
    public function index()
    {
        $posts = Post::active()->with('category')->orderBy('id', 'desc')->paginate(12);
        return view('frontend.post.index', compact('posts'));
    }

    public function show($slug)
    {
        $post = Post::with('category')->where('slug', $slug)->firstOrFail();

        // Bài trước (cũ hơn)
        $prevPost = Post::active()
            ->where('id', '<', $post->id)
            ->orderBy('id', 'desc')
            ->first(['id', 'title', 'slug']);

        // Bài sau (mới hơn)
        $nextPost = Post::active()
            ->where('id', '>', $post->id)
            ->orderBy('id', 'asc')
            ->first(['id', 'title', 'slug']);

        // Comments: root (approved) + replies
        $comments = $post->comments()
            ->approved()
            ->rootComments()
            ->with(['replies' => function ($q) {
                $q->approved()->orderBy('created_at', 'asc');
            }])
            ->orderBy('created_at', 'desc')
            ->get();

        $commentCount = $post->comments()->approved()->count();

        return view('frontend.post.show', compact('post', 'prevPost', 'nextPost', 'comments', 'commentCount'));
    }
}
