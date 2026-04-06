<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\GoldPriceHistory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BtmhGoldController extends Controller
{
    const LABEL_MAP = [
        'KGB' => 'Nhẫn Tròn ép vỉ (Kim Gia Bảo) 24K (999.9)',
    ];

    /**
     * GET /api/gold/btmh/current
     * Trả về bản ghi mới nhất trong DB – đơn giản, ổn định
     */
    public function currentPrice(): JsonResponse
    {
        $byUnit = [];

        foreach (array_keys(self::LABEL_MAP) as $unit) {
            $latest = GoldPriceHistory::where('source', 'btmh')
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
                'message' => 'Chưa có dữ liệu BTMH.',
                'data'    => [],
            ], 404);
        }

        $latestAny = GoldPriceHistory::where('source', 'btmh')
            ->orderByDesc('recorded_at')
            ->first();

        return response()->json([
            'success'    => true,
            'source'     => 'btmh',
            'updated_at' => $latestAny?->recorded_at?->format('H:i d/m/Y'),
            'data'       => $byUnit,
        ]);
    }

    /**
     * GET /api/gold/btmh/history?days=N&type=KGB
     * Tất cả đều lấy từ DB – giống cách silver chart hoạt động
     */
    public function history(Request $request): JsonResponse
    {
        $days = (int) $request->get('days', 7);
        $unit = strtoupper($request->get('type', 'KGB'));
        $days = max(1, min(365, $days));

        // ── 1D: intraday (từng bản ghi trong ngày hôm nay) ──────────────────
        if ($days === 1) {
            $history = GoldPriceHistory::getIntradayHistory('btmh', $unit);

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

        // ── Multi-day: 1 điểm/ngày (bản ghi cuối mỗi ngày) ─────────────────
        $history = GoldPriceHistory::getHistory('btmh', $unit, $days);

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
