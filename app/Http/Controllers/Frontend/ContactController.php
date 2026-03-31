<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ContactController extends Controller
{
    public function index()
    {
        return view('frontend.contact.index');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'    => 'required|string|max:255',
            'email'   => 'required|email|max:255',
            'phone'   => 'nullable|string|max:20',
            'message' => 'required|string|max:2000',
        ], [
            'name.required'    => 'Vui lòng nhập họ và tên',
            'email.required'   => 'Vui lòng nhập email',
            'email.email'      => 'Email không đúng định dạng',
            'message.required' => 'Vui lòng nhập tin nhắn',
        ]);

        try {
            Contact::create([
                'name'    => $request->name,
                'email'   => $request->email,
                'phone'   => $request->phone,
                'message' => $request->message,
                'status'  => Contact::STATUS_UNREAD,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Gửi tin nhắn thành công! Chúng tôi sẽ liên hệ với bạn sớm nhất.',
            ]);
        } catch (\Exception $e) {
            Log::error('Contact store error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Đã có lỗi xảy ra, vui lòng thử lại sau.',
            ], 500);
        }
    }
}
