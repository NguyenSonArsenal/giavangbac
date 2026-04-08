<?php

namespace App\Console\Commands;

use App\Models\SilverPriceHistory;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SeedHistorySilver extends Command
{
    protected $signature   = 'silver:seed-history {--days=365 : Số ngày cần seed}';
    protected $description = 'Backfill lịch sử giá bạc Phú Quý vào DB từ API (chạy 1 lần để có đủ data)';

    const BASE_URL = 'https://giabac.vn/SilverInfo/GetGoldPriceChartFromSQLData';
    const UNITS    = ['KG', 'CHI', 'LUONG'];
    const TYPE_MAP = ['KG' => 'KG', 'CHI' => 'CHI', 'LUONG' => 'LUONG'];

    public function handle(): int
    {
        $maxDays = (int) $this->option('days');
        $periods = [7, 30, 90, 365];

        // Chỉ giữ periods <= maxDays
        $periods = array_filter($periods, function($d) use ($maxDays) {
            return $d <= $maxDays;
        });
        $periods = array_unique($periods);
        rsort($periods); // Lớn nhất trước để data nhiều nhất rồi trùng thì skip

        $this->info("=== Seed Silver Price History (max {$maxDays} ngày) ===");

        // Fetch mỗi unit
        foreach (self::UNITS as $unit) {
            $this->newLine();
            $this->info("── Unit: {$unit} ──");

            // Fetch period lớn nhất → đủ data cho tất cả period nhỏ hơn
            $days = max($periods);

            try {
                $res = Http::timeout(30)
                    ->get(self::BASE_URL, [
                        'days' => $days,
                        'type' => self::TYPE_MAP[$unit],
                    ]);

                if (!$res->ok()) {
                    $this->warn("  HTTP {$res->status()} cho unit {$unit}");
                    continue;
                }

                $data       = $res->json();
                $dates      = $data['Dates']          ?? [];
                $buyPrices  = $data['LastBuyPrices']  ?? [];
                $sellPrices = $data['LastSellPrices'] ?? [];

                if (empty($dates)) {
                    $this->warn("  Không có data cho unit {$unit}");
                    continue;
                }

                $this->line("  Fetched " . count($dates) . " data points từ API");

                // Group by date: lấy bản ghi CUỐI cùng mỗi ngày (giá cuối ngày)
                $grouped = [];
                foreach ($dates as $i => $d) {
                    $grouped[$d] = [
                        'buy'  => $buyPrices[$i]  ?? 0,
                        'sell' => $sellPrices[$i] ?? 0,
                    ];
                }

                $inserted = 0;
                $skipped  = 0;

                foreach ($grouped as $date => $price) {
                    $exists = SilverPriceHistory::where('source', 'phuquy')
                        ->where('unit', $unit)
                        ->where('price_date', $date)
                        ->exists();

                    if ($exists) {
                        $skipped++;
                        continue;
                    }

                    SilverPriceHistory::create([
                        'source'      => 'phuquy',
                        'unit'        => $unit,
                        'buy_price'   => (int) round($price['buy']),
                        'sell_price'  => (int) round($price['sell']),
                        'price_date'  => $date,
                        'recorded_at' => $date . ' 08:30:00',
                    ]);
                    $inserted++;
                }

                $this->info("  ✅ Inserted: {$inserted} | Skipped (exists): {$skipped}");

            } catch (\Exception $e) {
                $this->error("  💥 Lỗi fetch unit {$unit}: " . $e->getMessage());
                Log::error('SeedSilverPriceHistory error', ['unit' => $unit, 'error' => $e->getMessage()]);
            }
        }

        $this->newLine();
        $total = SilverPriceHistory::where('source', 'phuquy')->count();
        $this->info("=== Hoàn tất! Tổng records trong DB: {$total} ===");

        return Command::SUCCESS;
    }
}
