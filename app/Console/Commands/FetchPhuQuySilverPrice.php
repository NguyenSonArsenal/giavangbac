<?php

namespace App\Console\Commands;

use App\Models\SilverPrice;
use App\Models\SilverPriceHistory;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FetchPhuQuySilverPrice extends Command
{
    protected $signature   = 'silver:fetch-phuquy';
    protected $description = 'Fetch giá bạc từ Phú Quý (giabac.vn) mỗi 30 phút';

    const BASE_URL = 'https://giabac.vn/SilverInfo';

    /**
     * Map: unit key → [filterType payload, unit label]
     */
    const UNITS = [
        'CHI' => ['filterType' => '#pills-home',   'label' => 'Chỉ'],
        'KG'  => ['filterType' => '#pills-contact', 'label' => 'Kilogram'],
        // LUONG không lưu vào DB: tính từ CHI × 10 ở tầng API
    ];

    public function handle(): int
    {
        $logFile = storage_path('logs/cron-phuquy.log');
        $startAt = now()->format('Y-m-d H:i:s');
        file_put_contents($logFile, "[{$startAt}] ▶ silver:fetch-phuquy START\n", FILE_APPEND);

        $this->info("[{$startAt}] Bắt đầu fetch giá bạc Phú Quý...");

        $success = true;

        // 1. Fetch giá hiện tại theo từng đơn vị
        foreach (self::UNITS as $unit => $cfg) {
            try {
                $res = Http::timeout(15)
                    ->asForm()
                    ->post(self::BASE_URL . '/FilterData', [
                        'filterType' => $cfg['filterType'],
                    ]);

                if (!$res->ok()) {
                    $this->warn("  ❌ FilterData [{$unit}] HTTP " . $res->status());
                    $success = false;
                    continue;
                }

                [$buy, $sell] = $this->parseFilterDataHtml($res->body());

                if ($buy === null || $sell === null) {
                    $this->warn("  ❌ Không parse được giá [{$unit}]");
                    $success = false;
                    continue;
                }

                SilverPrice::updateOrCreate(
                    ['source' => 'phuquy', 'unit' => $unit],
                    [
                        'product_name' => 'Bạc 999 Phú Quý (' . $cfg['label'] . ')',
                        'buy_price'    => $buy,
                        'sell_price'   => $sell,
                        'recorded_at'  => now(),
                    ]
                );

                $this->info("  ✅ [{$unit}] Mua: " . number_format($buy) . ' | Bán: ' . number_format($sell));

            } catch (\Exception $e) {
                $this->error("  💥 FilterData [{$unit}]: " . $e->getMessage());
                Log::error('FetchPhuQuySilverPrice FilterData error', ['unit' => $unit, 'error' => $e->getMessage()]);
                $success = false;
            }
        }

        // 2. Fetch chart data cho KG và CHI để lưu history
        // Lưu ý: LUONG không có endpoint riêng trong API Phú Quý → tính từ CHI × 10 ở tầng API
        $chartUnits = ['KG', 'CHI'];
        foreach ($chartUnits as $chartUnit) {
            try {
                $res = Http::timeout(15)
                    ->get(self::BASE_URL . '/GetGoldPriceChartFromSQLData', [
                        'days' => 1,
                        'type' => $chartUnit,
                    ]);

                if ($res->ok()) {
                    $data = $res->json();
                    $this->saveHistory($data, $chartUnit);
                } else {
                    $this->warn("  ❌ Chart data [{$chartUnit}] HTTP " . $res->status());
                    $success = false;
                }
            } catch (\Exception $e) {
                $this->error("  💥 Chart data [{$chartUnit}]: " . $e->getMessage());
                Log::error('FetchPhuQuySilverPrice chart error', ['unit' => $chartUnit, 'error' => $e->getMessage()]);
                $success = false;
            }
        }

        $this->info('[' . now()->format('Y-m-d H:i:s') . '] Hoàn thành.');
        $endAt = now()->format('Y-m-d H:i:s');
        $status = $success ? 'DONE ✓' : 'DONE (có lỗi)';
        file_put_contents($logFile, "[{$endAt}] ■ silver:fetch-phuquy {$status}\n", FILE_APPEND);
        return $success ? Command::SUCCESS : Command::FAILURE;
    }

    /**
     * Parse HTML từ FilterData để lấy buy/sell price
     * HTML dạng: <p class="text-24px text-red">84,426,456</p>
     */
    private function parseFilterDataHtml(string $html): array
    {
        // Lấy tất cả số trong các thẻ text-24px
        preg_match_all('/<p[^>]*class="[^"]*text-24px[^"]*"[^>]*>([\d,\.]+)<\/p>/i', $html, $matches);

        if (count($matches[1]) < 2) {
            // fallback: bắt tất cả số lớn
            preg_match_all('/>([\d]{2,3}[,\.][\d,\.]+)</', $html, $matches);
        }

        $prices = [];
        foreach (($matches[1] ?? []) as $raw) {
            $val = (int) preg_replace('/[^0-9]/', '', $raw);
            if ($val > 100000) { // ít nhất 100k để loại rác
                $prices[] = $val;
            }
        }

        if (count($prices) < 2) {
            return [null, null];
        }

        return [$prices[0], $prices[1]]; // [buy, sell]
    }

    /**
     * Lưu các điểm dữ liệu từ chart API vào history.
     * Chỉ lưu entry mới nhất nếu chưa có record trong 25 phút gần nhất.
     */
    private function saveHistory(array $data, string $unit = 'KG'): void
    {
        $dates      = $data['Dates']          ?? [];
        $buyPrices  = $data['LastBuyPrices']  ?? [];
        $sellPrices = $data['LastSellPrices'] ?? [];

        if (empty($dates)) {
            return;
        }

        $threshold = now()->subMinutes(25);

        $recentExists = SilverPriceHistory::where('source', 'phuquy')
            ->where('unit', $unit)
            ->where('recorded_at', '>=', $threshold)
            ->exists();

        if ($recentExists) {
            $this->line("  ⏭  History [{$unit}]: đã có record trong 25 phút, bỏ qua.");
            return;
        }

        // Lấy entry CUỐI cùng trong data trả về (mới nhất)
        $lastIdx = count($dates) - 1;

        $date  = $dates[$lastIdx]      ?? null;
        $buy   = $buyPrices[$lastIdx]  ?? null;
        $sell  = $sellPrices[$lastIdx] ?? null;

        if ($date && $buy && $sell) {
            SilverPriceHistory::create([
                'source'      => 'phuquy',
                'unit'        => $unit,
                'buy_price'   => (int) round($buy),
                'sell_price'  => (int) round($sell),
                'price_date'  => $date,
                'recorded_at' => now(),
            ]);
            $this->info("  ✅ History [{$unit}]: {$date} Mua=" . number_format((int)$buy) . ' Bán=' . number_format((int)$sell));
        }
    }
}

