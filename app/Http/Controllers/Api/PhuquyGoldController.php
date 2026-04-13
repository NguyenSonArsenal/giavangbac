<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\GoldPriceHistory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PhuquyGoldController extends Controller
{
    const LABEL_MAP = [
        'SJC'       => 'Vàng miếng SJC',
        'NHAN_TRON' => 'Nhẫn tròn Phú Quý 999.9',
    ];

    /**
     * GET /api/gold/phuquy/current
     */
    public function currentPrice(): JsonResponse
    {
        $byUnit = [];

        foreach (array_keys(self::LABEL_MAP) as $unit) {
            $latest = GoldPriceHistory::where('source', 'phuquy')
                ->where('unit', $unit)
                ->orderByDesc('recorded_at')
                ->first();

            if (!$latest) continue;

            $byUnit[$unit] = [
                'unit'           => $unit,
                'unit_label'     => self::LABEL_MAP[$unit],
                'buy_price'      => $latest->buy_price,
                'sell_price'     => $latest->sell_price,
                'buy_formatted'  => number_format($latest->buy_price),
                'sell_formatted' => number_format($latest->sell_price),
                'recorded_at'    => $latest->recorded_at
                    ? $latest->recorded_at->format('d/m/Y H:i')
                    : null,
            ];
        }

        if (empty($byUnit)) {
            return response()->json([
                'success' => false,
                'message' => 'Chưa có dữ liệu Phú Quý.',
                'data'    => [],
            ], 404);
        }

        $latestAny = GoldPriceHistory::where('source', 'phuquy')
            ->orderByDesc('recorded_at')
            ->first();

        return response()->json([
            'success'    => true,
            'source'     => 'phuquy',
            'updated_at' => $latestAny?->recorded_at?->format('H:i d/m/Y'),
            'data'       => $byUnit,
        ]);
    }

    /**
     * GET /api/gold/phuquy/history?days=N&type=SJC|NHAN_TRON
     */
    public function history(Request $request): JsonResponse
    {
        $days = (int) $request->get('days', 7);
        $unit = strtoupper($request->get('type', 'NHAN_TRON'));
        $days = max(1, min(365, $days));

        // ── 1D: intraday ──
        if ($days === 1) {
            $history = GoldPriceHistory::getIntradayHistory('phuquy', $unit);

            if ($history->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Chưa có dữ liệu trong ngày hôm nay.',
                    'data'    => ['dates' => [], 'buy_prices' => [], 'sell_prices' => []],
                ]);
            }

            $dates = $buyPrices = $sellPrices = [];
            foreach ($history as $r) {
                $dates[]      = $r->recorded_at->format('H:i');
                $buyPrices[]  = $r->buy_price;
                $sellPrices[] = $r->sell_price;
            }

            return response()->json([
                'success'    => true,
                'unit'       => $unit,
                'days'       => 1,
                'type_label' => self::LABEL_MAP[$unit] ?? $unit,
                'data'       => [
                    'dates'       => $dates,
                    'buy_prices'  => $buyPrices,
                    'sell_prices' => $sellPrices,
                ],
            ]);
        }

        // ── Multi-day ──
        $history = GoldPriceHistory::getHistory('phuquy', $unit, $days);

        if ($history->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Chưa có dữ liệu lịch sử.',
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
            'type_label' => self::LABEL_MAP[$unit] ?? $unit,
            'data'       => [
                'dates'       => $dates,
                'buy_prices'  => $buyPrices,
                'sell_prices' => $sellPrices,
            ],
        ]);
    }
}
