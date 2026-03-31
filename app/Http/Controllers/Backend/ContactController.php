<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ContactController extends Controller
{
    public function index(Request $request)
    {
        $query = Contact::orderBy('id', 'desc');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $data = $query->paginate(getConstant('BACKEND_PAGINATE'));
        return view('backend.contact.index', compact('data'));
    }

    public function show($id)
    {
        try {
            $data = Contact::findOrFail($id);

            // Tự động đánh dấu đã đọc khi xem chi tiết
            if ($data->isUnread()) {
                $data->markAsRead();
            }

            return view('backend.contact.show', compact('data'));
        } catch (\Exception $e) {
            Log::error($e);
            return redirect()->back()->with('notification_error', 'Không tìm thấy liên hệ');
        }
    }

    public function destroy($id)
    {
        try {
            Contact::where('id', $id)->delete();
            return redirect()->back()->with('notification_success', 'Xóa liên hệ thành công');
        } catch (\Exception $e) {
            Log::error($e);
            return redirect()->back()->with('notification_error', 'Đã có lỗi xảy ra');
        }
    }

    public function toggleRead($id)
    {
        try {
            $contact = Contact::findOrFail($id);

            if ($contact->isRead()) {
                $contact->markAsUnread();
            } else {
                $contact->markAsRead();
            }

            return redirect()->back()->with('notification_success', 'Cập nhật trạng thái thành công');
        } catch (\Exception $e) {
            Log::error($e);
            return redirect()->back()->with('notification_error', 'Đã có lỗi xảy ra');
        }
    }
}
