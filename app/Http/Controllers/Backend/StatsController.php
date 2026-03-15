<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\PageView;
use Illuminate\Support\Facades\DB;

class StatsController extends Controller
{
    public function index()
    {
        $today     = now()->toDateString();
        $yesterday = now()->subDay()->toDateString();
        $week      = now()->subDays(7)->toDateString();
        $month     = now()->subDays(30)->toDateString();

        $stats = [
            'today_views'      => PageView::whereDate('created_at', $today)->where('is_bot', false)->count(),
            'today_unique'     => PageView::whereDate('created_at', $today)->where('is_bot', false)->distinct('ip')->count('ip'),
            'yesterday_views'  => PageView::whereDate('created_at', $yesterday)->where('is_bot', false)->count(),
            'yesterday_unique' => PageView::whereDate('created_at', $yesterday)->where('is_bot', false)->distinct('ip')->count('ip'),
            'week_views'       => PageView::where('created_at', '>=', $week)->where('is_bot', false)->count(),
            'week_unique'      => PageView::where('created_at', '>=', $week)->where('is_bot', false)->distinct('ip')->count('ip'),
            'month_views'      => PageView::where('created_at', '>=', $month)->where('is_bot', false)->count(),
            'month_unique'     => PageView::where('created_at', '>=', $month)->where('is_bot', false)->distinct('ip')->count('ip'),
            'total_views'      => PageView::where('is_bot', false)->count(),
            'total_unique'     => PageView::where('is_bot', false)->distinct('ip')->count('ip'),
            'bot_views'        => PageView::where('is_bot', true)->count(),
        ];

        $topPages = PageView::select('url', DB::raw('COUNT(*) as views'), DB::raw('COUNT(DISTINCT ip) as unique_ips'))
            ->where('created_at', '>=', $month)
            ->where('is_bot', false)
            ->groupBy('url')
            ->orderByDesc('views')
            ->limit(20)
            ->get();

        $dailyViews = PageView::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as views'),
                DB::raw('COUNT(DISTINCT ip) as unique_ips')
            )
            ->where('created_at', '>=', $month)
            ->where('is_bot', false)
            ->groupBy(DB::raw('DATE(created_at)'))
            ->orderBy('date')
            ->get();

        $recent = PageView::where('is_bot', false)
            ->orderByDesc('created_at')
            ->limit(50)
            ->get();

        return view('backend.stats.index', compact('stats', 'topPages', 'dailyViews', 'recent'));
    }
}
