<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\GoldPriceHistory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SjcGoldController extends Controller
{
    const LABEL_MAP = [
        'VANG_MIEN' => 'Vàng SJC 1L, 10L, 1KG',
        'NHAN_TRON' => 'Vàng nhẫn SJC 99,99%',
    ];

    public function currentPrice(): JsonResponse
    {
        $byUnit = [];

        foreach (array_keys(self::LABEL_MAP) as $unit) {
            $latest = GoldPriceHistory::where('source', 'sjc')
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
                'recorded_at'    => $latest->recorded_at?->format('d/m/Y H:i'),
            ];
        }

        if (empty($byUnit)) {
            return response()->json([
                'success' => false,
                'message' => 'Chưa có dữ liệu SJC.',
                'data'    => [],
            ], 404);
        }

        $latestAny = GoldPriceHistory::where('source', 'sjc')
            ->orderByDesc('recorded_at')->first();

        return response()->json([
            'success'    => true,
            'source'     => 'sjc',
            'updated_at' => $latestAny?->recorded_at?->format('H:i d/m/Y'),
            'data'       => $byUnit,
        ]);
    }

    public function history(Request $request): JsonResponse
    {
        $days = max(1, min(365, (int)$request->get('days', 7)));
        $unit = strtoupper($request->get('type', 'VANG_MIEN'));

        if ($days === 1) {
            $history = GoldPriceHistory::getIntradayHistory('sjc', $unit);
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

        $history = GoldPriceHistory::getHistory('sjc', $unit, $days);
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
