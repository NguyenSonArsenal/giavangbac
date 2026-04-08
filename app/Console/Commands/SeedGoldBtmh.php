<?php

namespace App\Console\Commands;

use App\Models\GoldPriceHistory;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SeedGoldBtmh extends Command
{
    protected $signature   = 'gold:seed-btmh {--force : Ghi đè bản ghi đã tồn tại}';
    protected $description = 'Backfill lịch sử giá vàng Bảo Tín Mạnh Hải (BTMH) từ API – fetch cả nhiều time_type để có đủ ~365 ngày';

    /**
     * Danh sách gold_type cần seed:  gold_type (API) → unit (DB)
     */
    const GOLD_TYPES = [
        'KGB' => 'KGB',  // Nhẫn Tròn ép vỉ (Kim Gia Bảo) 24K (999.9)
    ];

    /**
     * Các time_type sẽ gọi theo thứ tự (lớn nhất → nhỏ nhất để lấy càng nhiều data càng tốt)
     * Dùng chiến lược: fetch mỗi năm riêng nếu API hỗ trợ tham số year,
     * nếu không thì dùng các period có sẵn.
     *
     * BTMH API hỗ trợ:
     *   time_type=year   → dữ liệu năm hiện tại (từ 01/01 đến nay)
     *   time_type=month  → dữ liệu tháng hiện tại
     *   time_type=week   → dữ liệu tuần hiện tại
     *   time_type=day    → intraday hôm nay
     *
     * Để có 365 ngày, ta thử thêm tham số year cho năm trước.
     */
    const API_BASE = 'https://baotinmanhhai.vn/api/v1/exchangerate/goldRateChart';

    public function handle(): int
    {
        $force = $this->option('force');
        $this->info('=== Seed Giá Vàng Bảo Tín Mạnh Hải (BTMH) – Full 365 ngày ===');

        foreach (self::GOLD_TYPES as $goldType => $unit) {
            $this->newLine();
            $this->info("── gold_type: {$goldType} → unit: {$unit} ──");

            $allData = [];

            // ── 1. Năm hiện tại (time_type=year) ─────────────────────────────────
            $this->fetchPeriod($goldType, 'year', null, $allData);

            // ── 2. Thử năm trước (year=YYYY) ─────────────────────────────────────
            // BTMH API có thể hỗ trợ tham số year để lấy dữ liệu năm cụ thể
            $prevYear = (int) date('Y') - 1;
            $fetched = $this->fetchPeriod($goldType, 'year', $prevYear, $allData);

            // ── 3. Nếu không lấy được năm trước, thử gọi lại không có tham số year ─
            if (!$fetched) {
                $this->line("  ℹ Không fetch được năm {$prevYear}, dùng dữ liệu năm hiện tại");
            }

            if (empty($allData)) {
                $this->warn("  ❌ Không có dữ liệu để seed cho {$goldType}");
                continue;
            }

            $this->line('  Tổng data points: ' . count($allData));

            // ── Lưu vào DB ────────────────────────────────────────────────────────
            $inserted = 0;
            $skipped  = 0;
            $updated  = 0;

            // Sắp xếp theo ngày tăng dần
            ksort($allData);

            foreach ($allData as $dateStr => $prices) {
                $buyPrice  = $prices['buy'];
                $sellPrice = $prices['sell'];

                if ($buyPrice <= 0 && $sellPrice <= 0) {
                    $skipped++;
                    continue;
                }

                $exists = GoldPriceHistory::where('source', 'btmh')
                    ->where('unit', $unit)
                    ->where('price_date', $dateStr)
                    ->first();

                if ($exists) {
                    if ($force) {
                        $exists->update(['buy_price' => $buyPrice, 'sell_price' => $sellPrice]);
                        $updated++;
                    } else {
                        $skipped++;
                    }
                    continue;
                }

                GoldPriceHistory::create([
                    'source'      => 'btmh',
                    'unit'        => $unit,
                    'buy_price'   => $buyPrice,
                    'sell_price'  => $sellPrice,
                    'price_date'  => $dateStr,
                    'recorded_at' => $dateStr . ' 08:30:00',
                ]);
                $inserted++;
            }

            $this->info("  ✅ Inserted: {$inserted} | Updated: {$updated} | Skipped: {$skipped}");
        }

        $this->newLine();
        $total = GoldPriceHistory::where('source', 'btmh')->count();
        $this->info("=== Hoàn tất! Tổng records BTMH trong DB: {$total} ===");

        return Command::SUCCESS;
    }

    /**
     * Fetch 1 period từ API và merge vào $allData array.
     * Key của $allData là date string (Y-m-d).
     * Return true nếu fetch được dữ liệu, false nếu không.
     */
    protected function fetchPeriod(string $goldType, string $timeType, ?int $year, array &$allData): bool
    {
        $params = [
            'gold_type' => $goldType,
            'time_type' => $timeType,
            'init'      => 'false',
        ];

        if ($year !== null) {
            $params['year'] = $year;
        }

        $yearLabel = $year ? " (year={$year})" : '';
        $this->line("  → Fetch time_type={$timeType}{$yearLabel}...");

        try {
            $res = Http::timeout(30)->get(self::API_BASE, $params);

            if (!$res->ok()) {
                $this->warn("    HTTP {$res->status()}");
                return false;
            }

            $data   = $res->json();
            $labels = $data['labels'] ?? [];
            $rates  = $data['data']['rate']  ?? [];
            $sells  = $data['data']['sell']  ?? [];

            if (empty($labels)) {
                $this->warn("    Không có data");
                return false;
            }

            $added = 0;
            foreach ($labels as $i => $dateOrLabel) {
                // Bỏ qua labels kiểu giờ (intraday như "0", "01:00" từ time_type=day)
                if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $dateOrLabel)) {
                    continue;
                }

                $buy  = isset($rates[$i]) ? (int) round((float) $rates[$i]) : 0;
                $sell = isset($sells[$i]) ? (int) round((float) $sells[$i]) : 0;

                if ($buy <= 0 && $sell <= 0) continue;

                // Nếu cùng ngày đã có, ưu tiên giữ giá mới hơn (ghi đè)
                $allData[$dateOrLabel] = ['buy' => $buy, 'sell' => $sell];
                $added++;
            }

            $this->line("    → Nhận {$added} ngày");
            return $added > 0;

        } catch (\Exception $e) {
            $this->error("    💥 Lỗi: " . $e->getMessage());
            Log::error('SeedBtmhGoldPrice fetchPeriod', [
                'gold_type' => $goldType,
                'time_type' => $timeType,
                'year'      => $year,
                'error'     => $e->getMessage(),
            ]);
            return false;
        }
    }
}
