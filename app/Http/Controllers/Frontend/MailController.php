<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Jobs\SendTestMailJob;

class MailController extends Controller
{
    /**
     * Gửi mail test dùng template mail.test
     * Truy cập: GET /mail
     */
    public function index()
    {
        $toEmail = 'test@example.com'; // Đổi thành email muốn nhận
        $toName  = 'Nguyễn Văn A';

        try {
            // Mail::to($toEmail, $toName)->send(new TestMail($toName));
            dispatch(new SendTestMailJob($toEmail, $toName));

            return response()->json([
                'success' => true,
                'message' => "Gửi mail thành công tới {$toEmail}",
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gửi mail thất bại: ' . $e->getMessage(),
            ], 500);
        }
    }
}
