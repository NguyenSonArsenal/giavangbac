<?php

namespace App\Console\Commands;

use App\Models\SilverPriceHistory;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SeedHistoryAncarat extends Command
{
    protected $signature   = 'silver:seed-ancarat-history';
    protected $description = 'Seed fake 1 năm lịch sử giá bạc Ancarat dựa trên giá hiện tại + biến động ngẫu nhiên';

    const API_URL = 'https://giabac.ancarat.com/api/price-data';

    // SKU đại diện
    const PRIMARY = [
        'LUONG' => 'A4', // 1 lượng: Ngân Long Quảng Tiến
        'KG'    => 'K4', // 1 Kilo: Ngân Long Quảng Tiến
    ];

    public function handle(): int
    {
        $this->info('=== Seed Ancarat History (fake 1 năm) ===');

        // Lấy giá hiện tại từ API
        try {
            $res  = Http::timeout(15)->get(self::API_URL);
            $rows = $res->json();
        } catch (\Exception $e) {
            $this->error('Lỗi fetch API: ' . $e->getMessage());
            return Command::FAILURE;
        }

        $priceMap = $this->parsePriceMap($rows);

        foreach (self::PRIMARY as $unit => $sku) {
            if (!isset($priceMap[$sku])) {
                $this->warn("SKU {$sku} không tìm thấy");
                continue;
            }

            [$sell, $buy] = $priceMap[$sku];
            if ($buy <= 0 || $sell <= 0) {
                $this->warn("SKU {$sku} giá = 0");
                continue;
            }

            $this->info("── Unit: {$unit} (hiện tại: Mua=" . number_format($buy) . " Bán=" . number_format($sell) . ")");

            $inserted = 0;
            $skipped  = 0;

            // Tạo 365 ngày fake từ hôm qua trở về trước
            // Giả lập xu hướng: giá 1 năm trước thấp hơn ~15–20%, dần tăng lên đến hiện tại
            $totalDays = 365;
            $baseRatio = 0.80; // 1 năm trước bằng 80% giá hiện tại

            for ($i = $totalDays; $i >= 1; $i--) {
                $date = now()->subDays($i)->toDateString();

                $exists = SilverPriceHistory::where('source', 'ancarat')
                    ->where('unit', $unit)
                    ->where('price_date', $date)
                    ->exists();

                if ($exists) {
                    $skipped++;
                    continue;
                }

                // Tỷ lệ tăng dần từ 80% → 100% theo thời gian + nhiễu ngẫu nhiên nhỏ
                $progress = ($totalDays - $i) / $totalDays; // 0 → 1
                $trend    = $baseRatio + (1.0 - $baseRatio) * $progress;

                // Thêm nhiễu ±2%
                $noise = 1 + (mt_rand(-200, 200) / 10000);
                $ratio = $trend * $noise;

                $fakeBuy  = (int) round($buy  * $ratio);
                $fakeSell = (int) round($sell * $ratio);

                // Đảm bảo spread hợp lý (sell > buy khoảng 3%)
                if ($fakeSell < $fakeBuy) {
                    $fakeSell = (int) round($fakeBuy * 1.03);
                }

                SilverPriceHistory::create([
                    'source'      => 'ancarat',
                    'unit'        => $unit,
                    'buy_price'   => $fakeBuy,
                    'sell_price'  => $fakeSell,
                    'price_date'  => $date,
                    'recorded_at' => $date . ' 08:30:00',
                ]);

                $inserted++;
            }

            $this->info("  ✅ Inserted: {$inserted} | Skipped: {$skipped}");
        }

        $total = SilverPriceHistory::where('source', 'ancarat')->count();
        $this->info("=== Hoàn tất! Tổng Ancarat records: {$total} ===");
        return Command::SUCCESS;
    }

    private function parsePriceMap(array $rows): array
    {
        $map = [];
        foreach ($rows as $row) {
            if (!is_array($row) || count($row) < 3) {
                continue;
            }
            $sku  = $row[3] ?? null;
            if (!$sku) {
                continue;
            }
            $sellVal = (int) preg_replace('/[^0-9]/', '', (string)($row[1] ?? ''));
            $buyVal  = (int) preg_replace('/[^0-9]/', '', (string)($row[2] ?? ''));

            if ($sellVal > 0 || $buyVal > 0) {
                $map[$sku] = [$sellVal, $buyVal];
            }
        }
        return $map;
    }
}
