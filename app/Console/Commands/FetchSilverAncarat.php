<?php

namespace App\Console\Commands;

use App\Models\SilverPriceHistory;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FetchSilverAncarat extends Command
{
    protected $signature   = 'silver:fetch-ancarat';
    protected $description = 'Fetch giá bạc từ Ancarat (giabac.ancarat.com) mỗi 30 phút';

    const API_URL = 'https://giabac.ancarat.com/api/price-data';

    // SKU đại diện chính cho mỗi unit – chỉ track A4 (Lượng) và K4 (Kilo)
    const PRIMARY_SKU = [
        'LUONG' => 'A4',  // Ngân Long Quảng Tiến 999 - 1 lượng
        'KG'    => 'K4',  // Ngân Long Quảng Tiến 999 - 1 Kilo
    ];

    public function handle(): int
    {
        $logFile = storage_path('logs/cron-silver-ancarat.log');
        $startAt = now()->format('Y-m-d H:i:s');
        file_put_contents($logFile, "[{$startAt}] ▶ silver:fetch-ancarat START\n", FILE_APPEND);

        $this->info('[' . now()->format('Y-m-d H:i:s') . '] Fetch giá bạc Ancarat...');

        try {
            $res = Http::timeout(15)->get(self::API_URL);

            if (!$res->ok()) {
                $this->error('HTTP ' . $res->status());
                return Command::FAILURE;
            }

            $rows = $res->json();
            if (empty($rows)) {
                $this->error('API trả về rỗng');
                return Command::FAILURE;
            }

            // Parse thành map: sku → [sell, buy]
            $priceMap = $this->parsePriceMap($rows);
            $this->info('  Parsed ' . count($priceMap) . ' sản phẩm có SKU');

            // Lưu history cho PRIMARY SKU (1 bảng duy nhất, dedup theo giá)
            foreach (self::PRIMARY_SKU as $unit => $sku) {
                if (!isset($priceMap[$sku])) {
                    $this->warn("  ⚠ SKU {$sku} không tìm thấy");
                    continue;
                }
                [$sell, $buy] = $priceMap[$sku];
                if ($buy <= 0 || $sell <= 0) {
                    $this->warn("  ⚠ SKU {$sku} giá = 0, bỏ qua");
                    continue;
                }

                // Dedup theo giá: lấy record cuối cùng, so sánh buy/sell
                $lastRecord = SilverPriceHistory::where('source', 'ancarat')
                    ->where('unit', $unit)
                    ->orderByDesc('recorded_at')
                    ->first();

                if ($lastRecord && (int)$lastRecord->buy_price === $buy && (int)$lastRecord->sell_price === $sell) {
                    $this->line("  ⏭  History [{$unit}]: giá không đổi (Mua=" . number_format($buy) . ' Bán=' . number_format($sell) . '), bỏ qua');
                    continue;
                }

                SilverPriceHistory::create([
                    'source'      => 'ancarat',
                    'unit'        => $unit,
                    'buy_price'   => $buy,
                    'sell_price'  => $sell,
                    'price_date'  => now()->toDateString(),
                    'recorded_at' => now(),
                ]);

                $this->info("  ✅ [{$unit}/{$sku}] Mua=" . number_format($buy) . ' Bán=' . number_format($sell));
            }

        } catch (\Exception $e) {
            $this->error('💥 ' . $e->getMessage());
            Log::error('FetchAncaratSilverPrice', ['error' => $e->getMessage()]);
            return Command::FAILURE;
        }

        $this->info('[' . now()->format('Y-m-d H:i:s') . '] Hoàn thành Ancarat.');
        $endAt = now()->format('Y-m-d H:i:s');
        file_put_contents($logFile, "[{$endAt}] ■ silver:fetch-ancarat DONE\n", FILE_APPEND);
        return Command::SUCCESS;
    }

    /**
     * Parse API response thành map: SKU → [sell_price, buy_price]
     * Response: [product_name, sell_price, buy_price, sku?, url?]
     */
    private function parsePriceMap(array $rows): array
    {
        $map = [];
        foreach ($rows as $row) {
            // Bỏ qua header, category, row không có giá
            if (!is_array($row) || count($row) < 3) {
                continue;
            }
            $name = $row[0] ?? '';
            $sell = $row[1] ?? '';
            $buy  = $row[2] ?? '';
            $sku  = $row[3] ?? null;

            if (!$sku || empty($name)) {
                continue;
            }

            $sellVal = (int) preg_replace('/[^0-9]/', '', (string) $sell);
            $buyVal  = (int) preg_replace('/[^0-9]/', '', (string) $buy);

            if ($sellVal > 0 || $buyVal > 0) {
                $map[$sku] = [$sellVal, $buyVal];
            }
        }
        return $map;
    }
}
