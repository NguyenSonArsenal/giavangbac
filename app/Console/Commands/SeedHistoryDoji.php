<?php

namespace App\Console\Commands;

use App\Models\SilverPriceHistory;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SeedHistoryDoji extends Command
{
    protected $signature   = 'silver:seed-doji-history';
    protected $description = 'Seed lịch sử giá bạc DOJI từ file txt (data thực, group theo ngày)';

    const ENDPOINTS = [
        'LUONG' => 'https://giabac.doji.vn/data/DataBac9991Luong.txt',
        'KG'    => 'https://giabac.doji.vn/data/DataBac9991Kg.txt',
    ];

    public function handle(): int
    {
        $this->info('=== Seed DOJI History từ API (data thực) ===');

        $cacheBuster = now()->timestamp * 1000;

        foreach (self::ENDPOINTS as $unit => $url) {
            $this->newLine();
            $this->info("── Unit: {$unit}");

            try {
                $res = Http::timeout(30)->get($url, ['t' => $cacheBuster]);
                if (!$res->ok()) {
                    $this->warn("  HTTP " . $res->status());
                    continue;
                }

                $rows = $this->parseRows($res->body());
                $this->line("  Fetched " . count($rows) . " data points");

                // Group by ngày: lấy entry cuối mỗi ngày (giá đóng cửa)
                $grouped = [];
                foreach ($rows as [$buy, $sell, $dt]) {
                    // Parse datetime: "HH:MM:SS DD/MM/YYYY"
                    $date = $this->extractDate($dt);
                    if ($date) {
                        // Overwrite = giữ entry cuối nhất của mỗi ngày
                        $grouped[$date] = [$buy, $sell];
                    }
                }

                $inserted = 0;
                $skipped  = 0;

                foreach ($grouped as $date => $price) {
                    [$buy, $sell] = $price;

                    $exists = SilverPriceHistory::where('source', 'doji')
                        ->where('unit', $unit)
                        ->where('price_date', $date)
                        ->exists();

                    if ($exists) {
                        $skipped++;
                        continue;
                    }

                    SilverPriceHistory::create([
                        'source'      => 'doji',
                        'unit'        => $unit,
                        'buy_price'   => $buy,
                        'sell_price'  => $sell,
                        'price_date'  => $date,
                        'recorded_at' => $date . ' 18:00:00',
                    ]);
                    $inserted++;
                    $this->line("  Inserted: {$date} buy=" . number_format($buy) . ' sell=' . number_format($sell));
                }

                $this->info("  ✅ Inserted: {$inserted} | Skipped: {$skipped}");

            } catch (\Exception $e) {
                $this->error("  💥 " . $e->getMessage());
                Log::error('SeedDojiHistory', ['unit' => $unit, 'error' => $e->getMessage()]);
            }
        }

        $total = SilverPriceHistory::where('source', 'doji')->count();
        $this->newLine();
        $this->info("=== Hoàn tất! Tổng DOJI records trong DB: {$total} ===");
        return Command::SUCCESS;
    }

    private function parseRows(string $body): array
    {
        $body  = ltrim($body, "\xEF\xBB\xBF");
        $lines = preg_split('/\r?\n/', trim($body));
        $rows  = [];

        foreach ($lines as $line) {
            $line  = trim($line);
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

    /**
     * Parse "HH:MM:SS DD/MM/YYYY" → "YYYY-MM-DD"
     */
    private function extractDate(string $dt): ?string
    {
        // Format: "08:30:00 07/03/2026"
        if (preg_match('/(\d{2})\/(\d{2})\/(\d{4})/', $dt, $m)) {
            return $m[3] . '-' . $m[2] . '-' . $m[1]; // YYYY-MM-DD
        }
        return null;
    }
}
