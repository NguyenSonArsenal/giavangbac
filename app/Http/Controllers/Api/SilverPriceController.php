<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SilverPriceHistory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SilverPriceController extends Controller
{
    /**
     * GET /api/silver/current
     * Trả về giá hiện tại KG và LUONG (DB lưu trực tiếp, không còn tính từ CHI)
     */
    public function currentPrice(): JsonResponse
    {
        $units  = ['KG', 'LUONG'];
        $byUnit = [];

        foreach ($units as $unit) {
            $latest = SilverPriceHistory::where('source', 'phuquy')
                ->where('unit', $unit)
                ->orderByDesc('recorded_at')
                ->first();

            if (!$latest) {
                continue;
            }

            $byUnit[$unit] = [
                'unit'           => $unit,
                'buy_price'      => $latest->buy_price,
                'sell_price'     => $latest->sell_price,
                'buy_formatted'  => number_format($latest->buy_price),
                'sell_formatted' => number_format($latest->sell_price),
                'recorded_at'    => $latest->recorded_at ? $latest->recorded_at->format('d/m/Y H:i') : null,
            ];
        }

        if (empty($byUnit)) {
            return response()->json([
                'success' => false,
                'message' => 'Chưa có dữ liệu. Vui lòng chạy: php artisan silver:fetch-phuquy',
                'data'    => null,
            ], 404);
        }

        $latestAny = SilverPriceHistory::where('source', 'phuquy')
            ->orderByDesc('recorded_at')
            ->first();

        return response()->json([
            'success'    => true,
            'source'     => 'phuquy',
            'updated_at' => $latestAny && $latestAny->recorded_at
                ? $latestAny->recorded_at->format('H:i d/m/Y') : null,
            'data'       => $byUnit,
        ]);
    }

    /**
     * GET /api/silver/history?days=7&type=KG|LUONG
     * days=1 → intraday (nhãn HH:MM); days>1 → multi-day (nhãn dd/mm)
     */
    public function history(Request $request): JsonResponse
    {
        $days = (int) $request->get('days', 7);
        $unit = strtoupper($request->get('type', 'KG'));

        $days = max(1, min(365, $days));
        $unit = in_array($unit, ['KG', 'LUONG']) ? $unit : 'KG';

        // ── 1D: intraday ──
        if ($days === 1) {
            $history = SilverPriceHistory::getIntradayHistory('phuquy', $unit);

            if ($history->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Chưa có dữ liệu trong ngày hôm nay.',
                    'data'    => ['dates' => [], 'buy_prices' => [], 'sell_prices' => []],
                ]);
            }

            $dates = $buy_prices = $sell_prices = [];
            foreach ($history as $record) {
                $dates[]        = $record->recorded_at->format('H:i');
                $buy_prices[]   = $record->buy_price;
                $sell_prices[]  = $record->sell_price;
            }

            return response()->json([
                'success'    => true,
                'unit'       => $unit,
                'days'       => 1,
                'type_label' => $this->unitLabel($unit),
                'data'       => compact('dates', 'buy_prices', 'sell_prices'),
            ]);
        }

        // ── Multi-day ──
        $history = SilverPriceHistory::getHistory('phuquy', $unit, $days);

        if ($history->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Chưa có dữ liệu lịch sử.',
                'data'    => ['dates' => [], 'buy_prices' => [], 'sell_prices' => []],
            ]);
        }

        $dates = $buy_prices = $sell_prices = [];
        foreach ($history as $record) {
            $dates[]        = $record->price_date->format('d/m');
            $buy_prices[]   = $record->buy_price;
            $sell_prices[]  = $record->sell_price;
        }

        return response()->json([
            'success'    => true,
            'unit'       => $unit,
            'days'       => $days,
            'type_label' => $this->unitLabel($unit),
            'data'       => compact('dates', 'buy_prices', 'sell_prices'),
        ]);
    }

    /**
     * GET /api/silver/percent?days=7
     */
    public function percent(Request $request): JsonResponse
    {
        $days = (int) $request->get('days', 7);
        $days = max(1, min(365, $days));
        $unit = 'KG';

        $from = now()->subDays($days)->startOfDay();

        $oldest = SilverPriceHistory::where('source', 'phuquy')
            ->where('unit', $unit)
            ->where('recorded_at', '>=', $from)
            ->orderBy('recorded_at')
            ->first();

        $latest = SilverPriceHistory::where('source', 'phuquy')
            ->where('unit', $unit)
            ->orderByDesc('recorded_at')
            ->first();

        if (!$oldest || !$latest) {
            return response()->json([
                'success'  => true,
                'percent'  => null,
                'trend'    => 'neutral',
                'days'     => $days,
                'message'  => 'Chưa đủ dữ liệu để tính %',
            ]);
        }

        $pct = $oldest->sell_price > 0
            ? round((($latest->sell_price - $oldest->sell_price) / $oldest->sell_price) * 100, 2)
            : 0;

        return response()->json([
            'success'     => true,
            'percent'     => abs($pct),
            'percent_raw' => $pct,
            'trend'       => $pct > 0 ? 'up' : ($pct < 0 ? 'down' : 'neutral'),
            'days'        => $days,
            'updated_at'  => $latest->recorded_at ? $latest->recorded_at->format('H:i d/m/Y') : null,
        ]);
    }

    private function unitLabel(string $unit): string
    {
        $labels = ['LUONG' => 'VND/Lượng', 'KG' => 'VND/Kilogram'];
        return $labels[$unit] ?? $unit;
    }
}
