<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\GoldPriceHistory;

class FetchGoldBtmh extends Command
{
    protected $signature   = 'gold:fetch-btmh';
    protected $description = 'Fetch giá vàng realtime từ Bảo Tín Mạnh Hải – dùng goldRateChart?time_type=day lấy giá mới nhất trong ngày';

    /**
     * Các loại vàng cần fetch:  gold_type (API) → unit (DB)
     */
    const GOLD_TYPES = [
        'KGB' => 'KGB',  // Nhẫn Tròn ép vỉ (Kim Gia Bảo) 24K (999.9)
    ];

    const API_BASE = 'https://baotinmanhhai.vn/api/v1/exchangerate/goldRateChart';

    public function handle(): int
    {
        $logFile = storage_path('logs/cron-gold-btmh.log');
        $startAt = now()->format('Y-m-d H:i:s');
        file_put_contents($logFile, "[{$startAt}] ▶ gold:fetch-btmh START\n", FILE_APPEND);

        $this->info('[' . now()->format('Y-m-d H:i:s') . '] Fetch giá vàng BTMH (intraday)...');

        foreach (self::GOLD_TYPES as $goldType => $unit) {
            $this->line("  ── gold_type={$goldType} → unit={$unit}");

            try {
                // Lấy data intraday (time_type=day): mỗi entry là giá tại 1 mốc giờ
                $res = Http::timeout(15)->get(self::API_BASE, [
                    'gold_type' => $goldType,
                    'time_type' => 'day',
                    'init'      => 'false',
                ]);

                if (!$res->ok()) {
                    $this->warn("  ❌ HTTP " . $res->status());
                    Log::error("FetchBtmhGoldPrice [{$goldType}]: HTTP " . $res->status());
                    continue;
                }

                $data   = $res->json();
                $rates  = $data['data']['rate']  ?? [];
                $sells  = $data['data']['sell']  ?? [];
                $labels = $data['data']['dates'] ?? $data['data']['labels'] ?? $data['data']['time'] ?? [];

                if (empty($rates)) {
                    $this->warn("  ⚠ Không có data intraday cho {$goldType}");
                    continue;
                }

                // Lấy entry cuối cùng (giá mới nhất trong ngày)
                $lastRate = (int) round((float) end($rates));
                $lastSell = (int) round((float) end($sells));

                // Parse timestamp thực tế từ API (thử nhiều key phổ biến)
                $lastLabel = !empty($labels) ? end($labels) : null;
                $recordedAt = null;
                if ($lastLabel) {
                    foreach (['H:i d/m/Y', 'd/m/Y H:i', 'Y-m-d H:i:s', 'Y-m-d H:i', 'H:i'] as $fmt) {
                        try {
                            $dt = \Carbon\Carbon::createFromFormat($fmt, trim($lastLabel));
                            $recordedAt = $dt;
                            break;
                        } catch (\Exception) {}
                    }
                }
                $recordedAt = $recordedAt ?? now();

                if ($lastRate <= 0 && $lastSell <= 0) {
                    $this->warn("  ⚠ Giá = 0, bỏ qua");
                    continue;
                }

                // So sánh với bản ghi cuối trong DB
                $lastRecord = GoldPriceHistory::where('source', 'btmh')
                    ->where('unit', $unit)
                    ->orderByDesc('recorded_at')
                    ->first();

                $buyForLog  = number_format($lastRate);
                $sellForLog = number_format($lastSell);

                if (
                    $lastRecord
                    && (int) $lastRecord->buy_price  === $lastRate
                    && (int) $lastRecord->sell_price === $lastSell
                ) {
                    $this->line("  ⏭  [{$unit}] giá không thay đổi (Mua={$buyForLog} Bán={$sellForLog}), bỏ qua");
                } else {
                    GoldPriceHistory::create([
                        'source'      => 'btmh',
                        'unit'        => $unit,
                        'buy_price'   => $lastRate,
                        'sell_price'  => $lastSell,
                        'price_date'  => $recordedAt->toDateString(),
                        'recorded_at' => $recordedAt,
                    ]);
                    $this->info("  ✅ [{$unit}] saved (Mua={$buyForLog} Bán={$sellForLog})");
                    file_put_contents($logFile, "[" . $recordedAt->format('Y-m-d H:i:s') . "] ✅ {$unit}: Mua={$buyForLog} Bán={$sellForLog}\n", FILE_APPEND);
                }

            } catch (\Exception $e) {
                $this->error("  💥 Error [{$goldType}]: " . $e->getMessage());
                Log::error("FetchBtmhGoldPrice [{$goldType}]", ['error' => $e->getMessage()]);
            }
        }

        $this->info('[' . now()->format('Y-m-d H:i:s') . '] Hoàn thành BTMH Gold.');
        file_put_contents($logFile, "[" . now()->format('Y-m-d H:i:s') . "] ✅ gold:fetch-btmh DONE\n", FILE_APPEND);
        return 0;
    }
}
