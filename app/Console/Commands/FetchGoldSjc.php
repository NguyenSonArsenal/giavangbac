<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\GoldPriceHistory;
use Carbon\Carbon;

class FetchGoldSjc extends Command
{
    protected $signature   = 'gold:fetch-sjc';
    protected $description = 'Fetch giá vàng SJC từ sjc.com.vn (AJAX API)';

    const API_URL = 'https://sjc.com.vn/GoldPrice/Services/PriceService.ashx';

    /**
     * Keyword trong TypeName → unit code trong DB
     * Chỉ lấy 2 loại quan trọng nhất (anh khoanh đỏ)
     */
    const TARGETS = [
        'VANG_MIEN'  => '1l, 10l, 1kg',       // Vàng SJC 1L, 10L, 1KG
        'NHAN_TRON'  => 'nhẫn sjc 99,99%',    // Vàng nhẫn SJC 99,99%
    ];

    public function handle(): int
    {
        $logFile = storage_path('logs/cron-gold-sjc.log');
        $startAt = now()->format('Y-m-d H:i:s');
        file_put_contents($logFile, "[{$startAt}] ▶ gold:fetch-sjc START\n", FILE_APPEND);
        $this->info("[{$startAt}] Fetch giá vàng SJC...");

        try {
            // Dùng cURL để truyền User-Agent browser — Http facade bị SJC chặn (HTTP 400)
            $ch = curl_init(self::API_URL);
            curl_setopt_array($ch, [
                CURLOPT_POST           => true,
                CURLOPT_POSTFIELDS     => 'method=GetCurrentGoldPricesByBranch&BranchId=1',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_TIMEOUT        => 20,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTPHEADER     => [
                    'Content-Type: application/x-www-form-urlencoded; charset=UTF-8',
                    'Accept: application/json, text/javascript, */*; q=0.01',
                    'X-Requested-With: XMLHttpRequest',
                    'Referer: https://sjc.com.vn/gia-vang-online',
                    'Origin: https://sjc.com.vn',
                    'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/123.0.0.0 Safari/537.36',
                ],
            ]);
            $body     = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $curlErr  = curl_error($ch);
            curl_close($ch);

            if ($curlErr) {
                throw new \RuntimeException("cURL error: $curlErr");
            }
            if ($httpCode !== 200) {
                $this->warn("  ❌ HTTP {$httpCode}");
                Log::error('FetchSjcGoldPrice: HTTP ' . $httpCode);
                return 1;
            }

            $json = json_decode($body, true);

            if (!isset($json['data']) || !is_array($json['data'])) {
                $this->warn("  ⚠ Response không có data: " . substr($body, 0, 200));
                Log::error('FetchSjcGoldPrice: invalid response', ['body' => substr($body, 0, 500)]);
                return 1;
            }

            // Parse thời gian cập nhật
            // latestDate dạng "09:34 07/04/2026" hoặc số Unix ms
            $recordedAt = null;
            if (isset($json['latestDate'])) {
                $raw = trim($json['latestDate']);
                // Thử parse "H:i d/m/Y"
                if (preg_match('/(\d{1,2}:\d{2})\s+(\d{2}\/\d{2}\/\d{4})/', $raw, $m)) {
                    try {
                        $recordedAt = Carbon::createFromFormat('H:i d/m/Y', $m[1] . ' ' . $m[2]);
                    } catch (\Exception) {}
                }
                // Thử parse nếu là số (Unix ms)
                if (!$recordedAt && is_numeric($raw)) {
                    $recordedAt = Carbon::createFromTimestampMs((int)$raw);
                }
            }
            $recordedAt = $recordedAt ?? now();

            $this->line("  🕐 Thời gian API: " . $recordedAt->format('d/m/Y H:i'));

            // Duyệt data
            $found = array_fill_keys(array_keys(self::TARGETS), false);

            foreach ($json['data'] as $item) {
                if (!in_array(false, $found, true)) break;

                $typeName = mb_strtolower(trim($item['TypeName'] ?? ''));

                foreach (self::TARGETS as $unit => $keyword) {
                    if ($found[$unit]) continue;

                    if (mb_strpos($typeName, $keyword) !== false) {
                        $found[$unit] = true;

                        $buy  = (int) str_replace([',', '.', ' '], '', $item['Buy']  ?? '0');
                        $sell = (int) str_replace([',', '.', ' '], '', $item['Sell'] ?? '0');

                        if ($buy <= 0 && $sell <= 0) {
                            $this->warn("  ⚠ [{$unit}] Giá = 0, bỏ qua");
                            continue;
                        }

                        $lastRecord = GoldPriceHistory::where('source', 'sjc')
                            ->where('unit', $unit)
                            ->orderByDesc('recorded_at')
                            ->first();

                        if ($lastRecord
                            && (int)$lastRecord->buy_price  === $buy
                            && (int)$lastRecord->sell_price === $sell
                        ) {
                            $this->line("  ⏭  [{$unit}] Giá không đổi (Mua=" . number_format($buy) . " Bán=" . number_format($sell) . "), bỏ qua");
                        } else {
                            GoldPriceHistory::create([
                                'source'      => 'sjc',
                                'unit'        => $unit,
                                'buy_price'   => $buy,
                                'sell_price'  => $sell,
                                'price_date'  => $recordedAt->toDateString(),
                                'recorded_at' => $recordedAt,
                            ]);
                            $this->info("  ✅ [{$unit}] saved (Mua=" . number_format($buy) . " Bán=" . number_format($sell) . ") lúc " . $recordedAt->format('H:i'));
                            file_put_contents($logFile, "[" . $recordedAt->format('Y-m-d H:i') . "] ✅ {$unit}: Mua=" . number_format($buy) . " Bán=" . number_format($sell) . "\n", FILE_APPEND);
                        }
                    }
                }
            }

            foreach ($found as $unit => $ok) {
                if (!$ok) {
                    $this->warn("  ⚠ Không tìm thấy unit: {$unit}");
                    Log::warning('FetchSjcGoldPrice: unit not found', ['unit' => $unit]);
                }
            }

        } catch (\Exception $e) {
            $this->error("  💥 Error: " . $e->getMessage());
            Log::error('FetchSjcGoldPrice', ['error' => $e->getMessage()]);
            return 1;
        }

        $this->info('[' . now()->format('Y-m-d H:i:s') . '] Hoàn thành SJC Gold.');
        file_put_contents($logFile, "[" . now()->format('Y-m-d H:i:s') . "] ✅ gold:fetch-sjc DONE\n", FILE_APPEND);
        return 0;
    }
}
