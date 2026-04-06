<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\GoldPriceHistory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BtmcGoldController extends Controller
{
    /**
     * GET /api/gold/btmc/current
     */
    public function currentPrice(): JsonResponse
    {
        $units  = ['MIENG_VRTL', 'NHAN_TRON'];
        $byUnit = [];

        foreach ($units as $unit) {
            $latest = GoldPriceHistory::where('source', 'btmc')
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
                'message' => 'Chưa có dữ liệu BTMC.',
                'data'    => [],
            ], 404);
        }

        $latestAny = GoldPriceHistory::where('source', 'btmc')
            ->orderByDesc('recorded_at')
            ->first();

        return response()->json([
            'success'    => true,
            'source'     => 'btmc',
            'updated_at' => $latestAny && $latestAny->recorded_at
                ? $latestAny->recorded_at->format('H:i d/m/Y') : null,
            'data'       => $byUnit,
        ]);
    }

    /**
     * GET /api/gold/btmc/history?days=7&type=NHAN_TRON
     */
    public function history(Request $request): JsonResponse
    {
        $days = (int) $request->get('days', 7);
        $unit = strtoupper($request->get('type', 'NHAN_TRON'));

        $days = max(1, min(365, $days));

        $labelMap = ['MIENG_VRTL' => 'VND/Lượng', 'NHAN_TRON' => 'VND/Lượng'];

        // ── 1D: intraday ──
        if ($days === 1) {
            $history = GoldPriceHistory::getIntradayHistory('btmc', $unit);

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
                'data'       => [
                    'dates'       => $dates,
                    'buy_prices'  => $buyPrices,
                    'sell_prices' => $sellPrices,
                ],
            ]);
        }

        // ── Multi-day ──
        $history = GoldPriceHistory::getHistory('btmc', $unit, $days);

        if ($history->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Chưa có dữ liệu lịch sử.',
                'data'    => ['dates' => [], 'buy_prices' => [], 'sell_prices' => []],
            ]);
        }

        $dates      = [];
        $buyPrices  = [];
        $sellPrices = [];

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
            'data'       => [
                'dates'       => $dates,
                'buy_prices'  => $buyPrices,
                'sell_prices' => $sellPrices,
            ],
        ]);
    }
}
