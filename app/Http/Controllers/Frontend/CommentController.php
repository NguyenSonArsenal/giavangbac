<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CommentController extends Controller
{
    public function store(Request $request, $postId)
    {
        $request->validate([
            'name'      => 'required|string|max:100',
            'email'     => 'required|email|max:255',
            'body'      => 'required|string|max:1000',
            'parent_id' => 'nullable|integer|exists:comments,id',
        ], [
            'name.required'  => 'Vui lòng nhập tên hiển thị',
            'email.required' => 'Vui lòng nhập email',
            'email.email'    => 'Email không đúng định dạng',
            'body.required'  => 'Vui lòng nhập nội dung bình luận',
            'body.max'       => 'Bình luận tối đa 1000 ký tự',
        ]);

        try {
            $post = Post::findOrFail($postId);

            $comment = Comment::create([
                'post_id'   => $post->id,
                'parent_id' => $request->parent_id,
                'name'      => $request->name,
                'email'     => $request->email,
                'body'      => $request->body,
                'is_admin'  => false,
                'status'    => Comment::STATUS_APPROVED,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Bình luận đã được gửi thành công!',
                'comment' => [
                    'id'         => $comment->id,
                    'name'       => $comment->name,
                    'initial'    => $comment->initial,
                    'body'       => $comment->body,
                    'is_admin'   => false,
                    'created_at' => $comment->created_at->diffForHumans(),
                ],
            ]);
        } catch (\Exception $e) {
            Log::error('Comment store error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Đã có lỗi xảy ra, vui lòng thử lại.',
            ], 500);
        }
    }
}
