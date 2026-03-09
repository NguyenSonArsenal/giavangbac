<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SilverPriceHistory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class KimNganPhucPriceController extends Controller
{
    /**
     * GET /api/kimnganphuc/current
     */
    public function currentPrice(): JsonResponse
    {
        $units  = ['KG', 'LUONG'];
        $byUnit = [];

        foreach ($units as $unit) {
            $latest = SilverPriceHistory::where('source', 'kimnganphuc')
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
                'message' => 'Chưa có dữ liệu. Chạy: php artisan silver:fetch-kimnganphuc',
                'data'    => [],
            ], 404);
        }

        $latestAny = SilverPriceHistory::where('source', 'kimnganphuc')
            ->orderByDesc('recorded_at')
            ->first();

        return response()->json([
            'success'    => true,
            'source'     => 'kimnganphuc',
            'updated_at' => $latestAny && $latestAny->recorded_at
                ? $latestAny->recorded_at->format('H:i d/m/Y') : null,
            'data'       => $byUnit,
        ]);
    }

    /**
     * GET /api/kimnganphuc/history?days=7&type=KG
     * type: KG | LUONG
     * days=1 → intraday
     */
    public function history(Request $request): JsonResponse
    {
        $days = (int) $request->get('days', 7);
        $unit = strtoupper($request->get('type', 'KG'));

        $days = max(1, min(365, $days));
        $unit = in_array($unit, ['KG', 'LUONG']) ? $unit : 'KG';

        $labelMap = ['KG' => 'VND/Kilogram', 'LUONG' => 'VND/Lượng'];

        // 1D: intraday
        if ($days === 1) {
            $history = SilverPriceHistory::getIntradayHistory('kimnganphuc', $unit);

            if ($history->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Chưa có dữ liệu trong ngày hôm nay.',
                    'data'    => ['dates' => [], 'buy_prices' => [], 'sell_prices' => []],
                ]);
            }

            $dates = $buyPrices = $sellPrices = [];
            foreach ($history as $record) {
                $dates[]      = $record->recorded_at->format('H:i');
                $buyPrices[]  = $record->buy_price;
                $sellPrices[] = $record->sell_price;
            }

            return response()->json([
                'success'    => true,
                'unit'       => $unit,
                'days'       => 1,
                'type_label' => $labelMap[$unit] ?? $unit,
                'data'       => compact('dates', 'buy_prices', 'sell_prices'),
            ]);
        }

        // Multi-day
        $history = SilverPriceHistory::getHistory('kimnganphuc', $unit, $days);

        if ($history->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Chưa có dữ liệu lịch sử Kim Ngân Phúc.',
                'data'    => ['dates' => [], 'buy_prices' => [], 'sell_prices' => []],
            ]);
        }

        $dates = $buyPrices = $sellPrices = [];
        foreach ($history as $record) {
            $dates[]      = $record->price_date->format('d/m');
            $buyPrices[]  = $record->buy_price;
            $sellPrices[] = $record->sell_price;
        }

        return response()->json([
            'success'    => true,
            'unit'       => $unit,
            'days'       => $days,
            'type_label' => $labelMap[$unit] ?? $unit,
            'data'       => compact('dates', 'buy_prices', 'sell_prices'),
        ]);
    }

    /**
     * GET /api/kimnganphuc/percent?days=7&type=KG
     */
    public function percent(Request $request): JsonResponse
    {
        $days = (int) $request->get('days', 7);
        $unit = strtoupper($request->get('type', 'KG'));

        $days = max(1, min(365, $days));
        $unit = in_array($unit, ['KG', 'LUONG']) ? $unit : 'KG';

        $from = now()->subDays($days)->startOfDay();

        $oldest = SilverPriceHistory::where('source', 'kimnganphuc')
            ->where('unit', $unit)
            ->where('recorded_at', '>=', $from)
            ->orderBy('recorded_at')
            ->first();

        $latest = SilverPriceHistory::where('source', 'kimnganphuc')
            ->where('unit', $unit)
            ->orderByDesc('recorded_at')
            ->first();

        if (!$oldest || !$latest) {
            return response()->json([
                'success' => true,
                'percent' => null,
                'trend'   => 'neutral',
                'days'    => $days,
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
}
