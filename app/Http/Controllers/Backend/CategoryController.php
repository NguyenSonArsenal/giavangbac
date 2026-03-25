<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $query = Category::orderBy('id', 'desc');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $data = $query->paginate(getConstant('BACKEND_PAGINATE'));
        return view('backend.category.index', compact('data'));
    }

    public function create()
    {
        return view('backend.category.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'        => 'required|string|max:255',
            'slug'        => 'nullable|string|max:255|unique:categories,slug',
            'thumbnail'   => 'nullable|image|max:2048',
        ]);

        try {
            $category = new Category();
            $category->fill($request->only([
                'name', 'slug', 'description', 'status',
                'meta_title', 'meta_description',
            ]));

            if ($request->hasFile('thumbnail')) {
                $path = $request->file('thumbnail')->store('categories', 'public');
                $category->thumbnail = $path;
            }

            $category->save();

            return redirect()->route(backendRouteName('category.index'))
                ->with('notification_success', "Thêm danh mục [{$category->name}] thành công");
        } catch (\Exception $e) {
            Log::error($e);
            return redirect()->back()->with('notification_error', 'Đã có lỗi xảy ra');
        }
    }

    public function edit($id)
    {
        try {
            $data = Category::findOrFail($id);
            return view('backend.category.edit', compact('data'));
        } catch (\Exception $e) {
            Log::error($e);
            return redirect()->back()->with('notification_error', 'Không tìm thấy danh mục');
        }
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name'        => 'required|string|max:255',
            'slug'        => 'nullable|string|max:255|unique:categories,slug,' . $id,
            'thumbnail'   => 'nullable|image|max:2048',
        ]);

        try {
            $data = Category::findOrFail($id);
            $data->fill($request->only([
                'name', 'slug', 'description', 'status',
                'meta_title', 'meta_description',
            ]));

            if ($request->hasFile('thumbnail')) {
                // Xóa ảnh cũ
                if ($data->thumbnail) {
                    Storage::disk('public')->delete($data->thumbnail);
                }
                $path = $request->file('thumbnail')->store('categories', 'public');
                $data->thumbnail = $path;
            }

            $data->save();

            return redirect()->route(backendRouteName('category.index'))
                ->with('notification_success', "Cập nhật danh mục [{$data->name}] thành công");
        } catch (\Exception $e) {
            Log::error($e);
            return redirect()->back()->with('notification_error', 'Đã có lỗi xảy ra');
        }
    }

    public function destroy($id)
    {
        try {
            Category::where('id', $id)->delete();
            return redirect()->back()->with('notification_success', 'Xóa danh mục thành công');
        } catch (\Exception $e) {
            return redirect()->back()->with('notification_error', 'Đã có lỗi xảy ra');
        }
    }
}
