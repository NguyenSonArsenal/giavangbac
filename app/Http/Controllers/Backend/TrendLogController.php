<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\SilverTrendLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class TrendLogController extends Controller
{
    public function index()
    {
        $logs = SilverTrendLog::orderByDesc('created_at')->paginate(20);

        return view('backend.trend-log.index', compact('logs'));
    }

    /**
     * AJAX: Toggle đánh giá đúng/sai thủ công
     */
    public function toggleAccuracy(Request $request, $id)
    {
        $log = SilverTrendLog::findOrFail($id);
        $log->is_accurate = $request->input('is_accurate');
        $log->admin_note  = $request->input('admin_note', $log->admin_note);
        $log->save();

        return response()->json(['success' => true, 'is_accurate' => $log->is_accurate]);
    }

    /**
     * AJAX: Tự động đánh giá tất cả nhận định chưa được đánh giá
     */
    public function autoEvaluate(Request $request)
    {
        try {
            Artisan::call('silver:evaluate-accuracy');
            $output = Artisan::output();

            // Đếm kết quả sau khi chạy
            $total   = SilverTrendLog::whereNotNull('is_accurate')->count();
            $correct = SilverTrendLog::where('is_accurate', true)->count();
            $wrong   = SilverTrendLog::where('is_accurate', false)->count();
            $rate    = $total > 0 ? round($correct / $total * 100) : 0;

            return response()->json([
                'success' => true,
                'message' => "Đã đánh giá xong! Tỷ lệ đúng: {$correct}/{$total} ({$rate}%)",
                'stats'   => compact('total', 'correct', 'wrong', 'rate'),
                'output'  => $output,
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
