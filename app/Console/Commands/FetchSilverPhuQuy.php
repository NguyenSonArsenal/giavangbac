<?php

namespace App\Console\Commands;

use App\Models\SilverPriceHistory;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FetchSilverPhuQuy extends Command
{
    protected $signature   = 'silver:fetch-phuquy';
    protected $description = 'Fetch giá bạc từ Phú Quý (giabac.vn) mỗi 30 phút';

    const BASE_URL = 'https://giabac.vn/SilverInfo';

    /**
     * Map: unit key để gọi API → [filterType, label, unit lưu DB, multiplier]
     * Lưu ý: API Phú Quý chỉ có endpoint CHI (& KG); LUONG = CHI × 10
     */
    const UNITS = [
        'KG'  => ['filterType' => '#pills-contact', 'label' => 'Kilogram',  'save_unit' => 'KG',    'mult' => 1 ],
        'CHI' => ['filterType' => '#pills-home',    'label' => 'Lượng (từ Chỉ)', 'save_unit' => 'LUONG', 'mult' => 10],
        // Gọi API CHI nhưng lưu vào DB là LUONG (buy/sell × 10)
    ];

    public function handle(): int
    {
        $logFile = storage_path('logs/cron-silver-phuquy.log');
        $startAt = now()->format('Y-m-d H:i:s');
        file_put_contents($logFile, "[{$startAt}] ▶ silver:fetch-phuquy START\n", FILE_APPEND);

        $this->info("[{$startAt}] Bắt đầu fetch giá bạc Phú Quý...");

        $success = true;

        // Fetch chart data cho KG và CHI (lưu dưới dạng KG và LUONG)
        $chartUnits = ['KG', 'CHI'];
        foreach ($chartUnits as $apiUnit) {
            $cfg = self::UNITS[$apiUnit];
            try {
                $res = Http::timeout(15)
                    ->get(self::BASE_URL . '/GetGoldPriceChartFromSQLData', [
                        'days' => 1,
                        'type' => $apiUnit,
                    ]);

                if ($res->ok()) {
                    $data = $res->json();
                    $this->saveHistory($data, $cfg['save_unit'], $cfg['mult']);
                } else {
                    $this->warn("  ❌ Chart data [{$apiUnit}] HTTP " . $res->status());
                    $success = false;
                }
            } catch (\Exception $e) {
                $this->error("  💥 Chart data [{$apiUnit}]: " . $e->getMessage());
                Log::error('FetchPhuQuySilverPrice chart error', ['unit' => $apiUnit, 'error' => $e->getMessage()]);
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
     * @param string $saveUnit   Đơn vị lưu vào DB (KG hoặc LUONG)
     * @param int    $multiplier Nhân giá trước khi lưu (1 cho KG, 10 cho LUONG từ CHI)
     */
    private function saveHistory(array $data, string $saveUnit = 'KG', int $multiplier = 1): void
    {
        $dates      = $data['Dates']          ?? [];
        $buyPrices  = $data['LastBuyPrices']  ?? [];
        $sellPrices = $data['LastSellPrices'] ?? [];

        if (empty($dates)) {
            return;
        }

        // Lấy entry CUỐI cùng trong data trả về (mới nhất)
        $lastIdx = count($dates) - 1;

        $date  = $dates[$lastIdx]      ?? null;
        $buy   = isset($buyPrices[$lastIdx])  ? (int) round($buyPrices[$lastIdx]  * $multiplier) : null;
        $sell  = isset($sellPrices[$lastIdx]) ? (int) round($sellPrices[$lastIdx] * $multiplier) : null;

        if (!$date || !$buy || !$sell) {
            return;
        }

        // Dedup theo giá: lấy record cuối cùng trong DB, so sánh giá
        $lastRecord = SilverPriceHistory::where('source', 'phuquy')
            ->where('unit', $saveUnit)
            ->orderByDesc('recorded_at')
            ->first();

        if ($lastRecord && (int)$lastRecord->buy_price === $buy && (int)$lastRecord->sell_price === $sell) {
            $this->line("  ⏭  History [{$saveUnit}]: giá không đổi (Mua=" . number_format($buy) . ' Bán=' . number_format($sell) . '), bỏ qua.');
            return;
        }

        SilverPriceHistory::create([
            'source'      => 'phuquy',
            'unit'        => $saveUnit,
            'buy_price'   => $buy,
            'sell_price'  => $sell,
            'price_date'  => $date,
            'recorded_at' => now(),
        ]);
        $this->info("  ✅ History [{$saveUnit}]: {$date} Mua=" . number_format($buy) . ' Bán=' . number_format($sell));
    }
}

