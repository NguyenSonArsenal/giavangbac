<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\GoldPriceHistory;
use Carbon\Carbon;

class FetchGoldPhuquy extends Command
{
    protected $signature   = 'gold:fetch-phuquy';
    protected $description = 'Fetch giá vàng từ Phú Quý Group (banggia.phuquygroup.vn) – HTML scraping';

    /**
     * Keyword tìm trong text tên sản phẩm → unit code trong DB
     * Khớp theo substring (case-insensitive)
     */
    const TARGETS = [
        'Vàng miếng SJC'           => 'SJC',
        'Nhẫn tròn Phú Quý 999.9'  => 'NHAN_TRON',
    ];

    const URL = 'http://banggia.phuquygroup.vn/';

    public function handle(): int
    {
        $logFile = storage_path('logs/cron-gold-phuquy.log');

        // Helper: ghi đồng thời ra terminal và file log
        $log = function(string $msg, string $level = 'line') use ($logFile) {
            match($level) {
                'info'  => $this->info($msg),
                'warn'  => $this->warn($msg),
                'error' => $this->error($msg),
                default => $this->line($msg),
            };
            file_put_contents($logFile, $msg . "\n", FILE_APPEND);
        };

        $inserted  = 0;
        $unchanged = 0;

        file_put_contents($logFile, "\n▶ Chạy: gold:fetch-phuquy\n", FILE_APPEND);
        $this->info('[' . now()->format('Y-m-d H:i:s') . '] Fetch giá vàng Phú Quý...');

        try {
            $res = Http::timeout(20)
                ->withHeaders(['Accept-Language' => 'vi-VN,vi;q=0.9'])
                ->get(self::URL);

            if (!$res->ok()) {
                $log("  ❌ HTTP " . $res->status(), 'warn');
                Log::error('FetchPhuquyGoldPrice: HTTP ' . $res->status());
                return 1;
            }

            $html = $res->body();

            // ── 1. Parse thời gian cập nhật từ header ───────────────────────
            // Dạng: "18:02 07/04/2026" trong thẻ .font-roboto.text-uppercase
            $recordedAt = null;
            if (preg_match('/(\d{1,2}:\d{2})\s+(\d{2}\/\d{2}\/\d{4})/', $html, $dtMatch)) {
                // $dtMatch[1] = "18:02", $dtMatch[2] = "07/04/2026"
                $rawDate = $dtMatch[2] . ' ' . $dtMatch[1]; // "07/04/2026 18:02"
                try {
                    $recordedAt = Carbon::createFromFormat('d/m/Y H:i', $rawDate);
                } catch (\Exception) {
                    $recordedAt = null;
                }
            }
            $recordedAt = $recordedAt ?? now();

            $log("  🕐 Thời gian API: " . $recordedAt->format('d/m/Y H:i'));

            // ── 2. Parse từng hàng trong bảng ───────────────────────────────
            // Regex bắt: <td class="...text-white fz-1-3em...">Tên sản phẩm</td>
            //            <td ...text-buy fz-1-5em"...>Giá mua</td> (không có buon-col)
            //            <td ...text-sell fz-1-5em"...>Giá bán</td>
            $rowPattern = '/<tr[^>]*class="show-tr"[^>]*>(.*?)<\/tr>/s';
            preg_match_all($rowPattern, $html, $rowMatches);

            if (empty($rowMatches[1])) {
                $log("  ⚠ Không tìm được bảng giá", 'warn');
                Log::error('FetchPhuquyGoldPrice: No rows found');
                return 1;
            }

            $found = array_fill_keys(array_keys(self::TARGETS), false);

            foreach ($rowMatches[1] as $rowHtml) {
                if (!in_array(false, $found, true)) break;

                // Lấy tên sản phẩm (bỏ HTML entities)
                if (!preg_match('/<td[^>]+class="[^"]*text-white fz-1-3em[^"]*"[^>]*>(.*?)<\/td>/s', $rowHtml, $nameM)) continue;
                $name = html_entity_decode(strip_tags($nameM[1]), ENT_QUOTES | ENT_HTML5, 'UTF-8');
                $name = trim($name);

                // Lấy tất cả các cột giá (text-buy / text-sell không có buon-col)
                // Chỉ lấy td KHÔNG có "buon-col" trong class
                $buyPrices  = [];
                $sellPrices = [];

                // Parse từng td
                preg_match_all('/<td([^>]*)>(.*?)<\/td>/s', $rowHtml, $tdMatches, PREG_SET_ORDER);
                foreach ($tdMatches as $td) {
                    $attrs   = $td[1];
                    $content = trim(strip_tags($td[2]));

                    if (strpos($attrs, 'buon-col') !== false) continue; // bỏ qua cột buôn
                    if (strpos($attrs, 'text-buy') !== false) {
                        $buyPrices[] = $content;
                    } elseif (strpos($attrs, 'text-sell') !== false) {
                        $sellPrices[] = $content;
                    }
                }

                if (empty($buyPrices) || empty($sellPrices)) continue;

                // Parse số từ chuỗi "16,900,000"
                $buy  = (int) str_replace([',', '.', ' '], '', $buyPrices[0]);
                $sell = (int) str_replace([',', '.', ' '], '', $sellPrices[0]);

                if ($buy <= 0 && $sell <= 0) continue;

                // So với targets
                foreach (self::TARGETS as $keyword => $unit) {
                    if ($found[$keyword]) continue;

                    // So sánh: tên sản phẩm chứa keyword
                    if (mb_stripos($name, $keyword) !== false) {
                        $found[$keyword] = true;

                        $lastRecord = GoldPriceHistory::where('source', 'phuquy')
                            ->where('unit', $unit)
                            ->orderByDesc('recorded_at')
                            ->first();

                        if ($lastRecord
                            && (int)$lastRecord->buy_price  === $buy
                            && (int)$lastRecord->sell_price === $sell
                        ) {
                            $lastRecord->recorded_at = $recordedAt;
                            $lastRecord->save();
                            $log("  🔄 [{$unit}] \"{$name}\": giá không đổi (Mua=" . number_format($buy) . " Bán=" . number_format($sell) . "), đã cập nhật recorded_at → " . $recordedAt->format('H:i'));
                            $unchanged++;
                        } else {
                            GoldPriceHistory::create([
                                'source'      => 'phuquy',
                                'unit'        => $unit,
                                'buy_price'   => $buy,
                                'sell_price'  => $sell,
                                'price_date'  => $recordedAt->toDateString(),
                                'recorded_at' => $recordedAt,
                            ]);
                            $this->info("  ✅ [{$unit}] \"{$name}\" saved (Mua=" . number_format($buy) . " Bán=" . number_format($sell) . ") lúc " . $recordedAt->format('H:i'));
                            $inserted++;
                        }
                    }
                }
            }

            // Log keyword nào không tìm thấy
            foreach ($found as $keyword => $ok) {
                if (!$ok) {
                    $log("  ⚠ Không tìm thấy: \"{$keyword}\"", 'warn');
                    Log::warning('FetchPhuquyGoldPrice: keyword not found', ['keyword' => $keyword]);
                }
            }

        } catch (\Exception $e) {
            $log("  💥 Error: " . $e->getMessage(), 'error');
            Log::error('FetchPhuquyGoldPrice', ['error' => $e->getMessage()]);
            return 1;
        }

        $this->info('[' . now()->format('Y-m-d H:i:s') . '] Hoàn thành Phú Quý Gold.');
        return 0;
    }
}
