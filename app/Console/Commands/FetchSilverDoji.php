<?php

namespace App\Console\Commands;

use App\Models\SilverPriceHistory;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FetchSilverDoji extends Command
{
    protected $signature   = 'silver:fetch-doji';
    protected $description = 'Fetch giá bạc từ DOJI (giabac.doji.vn) mỗi 30 phút';

    // Cache-buster: timestamp dạng milliseconds, cập nhật mỗi lần chạy
    // DOJI chỉ có Lượng (không có KG riêng)
    // 5 Lượng được tính = 1 Lượng × 5 ở tầng JS/API
    const ENDPOINTS = [
        'LUONG' => 'https://giabac.doji.vn/data/DataBac9991Luong.txt',
    ];

    public function handle(): int
    {
        $logFile = storage_path('logs/cron-silver-doji.log');
        $startAt = now()->format('Y-m-d H:i:s');
        file_put_contents($logFile, "[{$startAt}] ▶ silver:fetch-doji START\n", FILE_APPEND);

        $this->info('[' . now()->format('Y-m-d H:i:s') . '] Fetch giá bạc DOJI...');

        $cacheBuster = now()->timestamp * 1000;
        $success     = true;

        foreach (self::ENDPOINTS as $unit => $url) {
            try {
                $res = Http::timeout(15)->get($url, ['t' => $cacheBuster]);

                if (!$res->ok()) {
                    $this->warn("  ❌ [{$unit}] HTTP " . $res->status());
                    $success = false;
                    continue;
                }

                $rows = $this->parseRows($res->body());
                if (empty($rows)) {
                    $this->warn("  ⚠ [{$unit}] Không parse được data");
                    continue;
                }

                // Lấy entry cuối cùng = giá hiện tại
                $last = end($rows);
                [$buy, $sell, $datetime] = $last;

                // Lưu history – dedup theo giá (1 bảng duy nhất)
                $lastRecord = SilverPriceHistory::where('source', 'doji')
                    ->where('unit', $unit)
                    ->orderByDesc('recorded_at')
                    ->first();

                if ($lastRecord && (int)$lastRecord->buy_price === $buy && (int)$lastRecord->sell_price === $sell) {
                    $this->line("  ⏭  History [{$unit}]: giá không đổi (Mua=" . number_format($buy) . ' Bán=' . number_format($sell) . '), bỏ qua');
                    continue;
                }

                SilverPriceHistory::create([
                    'source'      => 'doji',
                    'unit'        => $unit,
                    'buy_price'   => $buy,
                    'sell_price'  => $sell,
                    'price_date'  => now()->toDateString(),
                    'recorded_at' => now(),
                ]);
                $this->info("  ✅ History [{$unit}] saved (Mua=" . number_format($buy) . ' Bán=' . number_format($sell) . ')');

            } catch (\Exception $e) {
                $this->error("  💥 [{$unit}]: " . $e->getMessage());
                Log::error('FetchDojiSilverPrice', ['unit' => $unit, 'error' => $e->getMessage()]);
                $success = false;
            }
        }

        $this->info('[' . now()->format('Y-m-d H:i:s') . '] Hoàn thành DOJI.');
        return $success ? Command::SUCCESS : Command::FAILURE;
    }

    /**
     * Parse text file: mỗi dòng = "buy|sell|HH:MM:SS DD/MM/YYYY"
     * Trả về array of [buy, sell, datetime_string]
     */
    public function parseRows(string $body): array
    {
        // Xóa BOM nếu có
        $body = ltrim($body, "\xEF\xBB\xBF");
        $lines = preg_split('/\r?\n/', trim($body));
        $rows  = [];

        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line)) {
                continue;
            }
            $parts = explode('|', $line);
            if (count($parts) < 3) {
                continue;
            }
            $buy  = (int) preg_replace('/[^0-9]/', '', $parts[0]);
            $sell = (int) preg_replace('/[^0-9]/', '', $parts[1]);
            $dt   = trim($parts[2]);

            if ($buy > 0 && $sell > 0) {
                $rows[] = [$buy, $sell, $dt];
            }
        }

        return $rows;
    }
}
