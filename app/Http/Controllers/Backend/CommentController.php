<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CommentController extends Controller
{
    public function index(Request $request)
    {
        $query = Comment::with('post')
            ->rootComments()
            ->orderBy('id', 'desc');

        if ($request->filled('post_id')) {
            $query->where('post_id', $request->post_id);
        }

        $data = $query->paginate(getConstant('BACKEND_PAGINATE'));
        return view('backend.comment.index', compact('data'));
    }

    public function reply(Request $request, $id)
    {
        $request->validate([
            'body' => 'required|string|max:2000',
        ], [
            'body.required' => 'Vui lòng nhập nội dung trả lời',
        ]);

        try {
            $parent = Comment::findOrFail($id);

            Comment::create([
                'post_id'   => $parent->post_id,
                'parent_id' => $parent->id,
                'name'      => 'GIAVANGBAC ADMIN',
                'email'     => 'admin@giavang.vn',
                'body'      => $request->body,
                'is_admin'  => true,
                'status'    => Comment::STATUS_APPROVED,
            ]);

            return redirect()->back()->with('notification_success', 'Trả lời bình luận thành công');
        } catch (\Exception $e) {
            Log::error($e);
            return redirect()->back()->with('notification_error', 'Đã có lỗi xảy ra');
        }
    }

    public function destroy(Request $request, $id)
    {
        try {
            $comment = Comment::findOrFail($id);
            // Also delete replies
            Comment::where('parent_id', $id)->delete();
            $comment->delete();
            
            if ($request->wantsJson()) {
                return response()->json(['success' => true, 'message' => 'Xóa bình luận thành công']);
            }
            return redirect()->back()->with('notification_success', 'Xóa bình luận thành công');
        } catch (\Exception $e) {
            Log::error($e);
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Đã có lỗi xảy ra'], 500);
            }
            return redirect()->back()->with('notification_error', 'Đã có lỗi xảy ra');
        }
    }
}
