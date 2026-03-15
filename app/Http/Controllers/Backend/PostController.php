<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class PostController extends Controller
{
    public function index()
    {
        $data = Post::orderBy('id', 'desc')->paginate(getConstant('BACKEND_PAGINATE'));
        return view('backend.post.index', compact('data'));
    }

    public function create()
    {
        return view('backend.post.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title'   => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        try {
            $post = new Post();
            $post->fill($request->only(['title', 'des', 'content']));
            $post->save();

            return redirect()->route(backendRouteName('post.index'))
                ->with('notification_success', "Thêm bài viết [{$post->title}] thành công");
        } catch (\Exception $e) {
            Log::error($e);
            return redirect()->back()->with('notification_error', 'Đã có lỗi xảy ra');
        }
    }

    public function edit($id)
    {
        try {
            $data = Post::findOrFail($id);
            return view('backend.post.edit', compact('data'));
        } catch (\Exception $e) {
            Log::error($e);
            return redirect()->back()->with('notification_error', 'Không tìm thấy bài viết');
        }
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'title'   => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        try {
            $data = Post::findOrFail($id);
            $data->update($request->only(['title', 'des', 'content']));

            return redirect()->route(backendRouteName('post.index'))
                ->with('notification_success', "Cập nhật bài viết [{$data->title}] thành công");
        } catch (\Exception $e) {
            Log::error($e);
            return redirect()->back()->with('notification_error', 'Đã có lỗi xảy ra');
        }
    }

    public function destroy($id)
    {
        try {
            Post::where('id', $id)->delete();
            return redirect()->back()->with('notification_success', 'Xóa bài viết thành công');
        } catch (\Exception $e) {
            return redirect()->back()->with('notification_error', 'Đã có lỗi xảy ra');
        }
    }

    public function upload(Request $request)
    {
        $request->validate([
            'file' => ['required', 'image', 'max:5120'],
        ]);

        $path = $request->file('file')->store('posts', 'public');

        return response()->json([
            'location' => asset('storage/' . $path),
        ]);
    }
}
