<?php

namespace App\Console\Commands;

use App\Models\SilverPriceHistory;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FetchKimNganPhucSilverPrice extends Command
{
    protected $signature   = 'silver:fetch-kimnganphuc';
    protected $description = 'Fetch giá bạc từ Kim Ngân Phúc (kimnganphuc.vn/bang-gia-bac) mỗi 30 phút';

    const URL = 'https://kimnganphuc.vn/bang-gia-bac';

    public function handle(): int
    {
        $logFile = storage_path('logs/cron-kimnganphuc.log');
        $startAt = now()->format('Y-m-d H:i:s');
        file_put_contents($logFile, "[{$startAt}] ▶ silver:fetch-kimnganphuc START\n", FILE_APPEND);
        $this->info("[{$startAt}] Bắt đầu fetch giá bạc Kim Ngân Phúc...");

        try {
            $response = Http::timeout(20)
                ->withHeaders(['User-Agent' => 'Mozilla/5.0 (compatible; GiaVangBot/1.0)'])
                ->get(self::URL);

            if (!$response->ok()) {
                $this->error('HTTP ' . $response->status());
                file_put_contents($logFile, "[{$startAt}] ✗ HTTP " . $response->status() . "\n", FILE_APPEND);
                return Command::FAILURE;
            }

            $prices = $this->parseTable($response->body());

            if (empty($prices)) {
                $this->error('Không parse được giá từ HTML');
                file_put_contents($logFile, "[{$startAt}] ✗ parse failed\n", FILE_APPEND);
                return Command::FAILURE;
            }

            foreach ($prices as $unit => $data) {
                [$buy, $sell] = $data;
                if (!$buy || !$sell) {
                    $this->warn("  ⚠ [{$unit}] Không có giá, bỏ qua");
                    continue;
                }

                // Dedup: không lưu nếu giá không đổi
                $last = SilverPriceHistory::where('source', 'kimnganphuc')
                    ->where('unit', $unit)
                    ->orderByDesc('recorded_at')
                    ->first();

                if ($last && (int)$last->buy_price === $buy && (int)$last->sell_price === $sell) {
                    $this->line("  ⏭  [{$unit}] giá không đổi (Mua=" . number_format($buy) . ' Bán=' . number_format($sell) . '), bỏ qua.');
                    continue;
                }

                SilverPriceHistory::create([
                    'source'      => 'kimnganphuc',
                    'unit'        => $unit,
                    'buy_price'   => $buy,
                    'sell_price'  => $sell,
                    'price_date'  => now()->toDateString(),
                    'recorded_at' => now(),
                ]);

                $this->info("  ✅ [{$unit}] Mua=" . number_format($buy) . ' Bán=' . number_format($sell));
            }

        } catch (\Exception $e) {
            $this->error('💥 ' . $e->getMessage());
            Log::error('FetchKimNganPhucSilverPrice', ['error' => $e->getMessage()]);
            file_put_contents($logFile, "[{$startAt}] 💥 " . $e->getMessage() . "\n", FILE_APPEND);
            return Command::FAILURE;
        }

        $endAt = now()->format('Y-m-d H:i:s');
        $this->info("[{$endAt}] Hoàn thành Kim Ngân Phúc.");
        file_put_contents($logFile, "[{$endAt}] ■ silver:fetch-kimnganphuc DONE\n", FILE_APPEND);
        return Command::SUCCESS;
    }

    /**
     * Parse bảng giá bạc từ HTML trang bang-gia-bac.
     *
     * Cấu trúc table đã xác nhận qua scraping:
     *   Row 0  : Header (SẢN PHẨM | MUA VÀO | BÁN RA)
     *   Row 3  : Bạc Thỏi 999/1Kilo phiên bản 2025 → **KG**
     *   Row 10 : BẠC MIẾNG MỸ NGHỆ * (section header)
     *   Row 11+: Sản phẩm 1 Lượng Mỹ Nghệ đầu tiên có giá → **LUONG**
     *
     * @return array<string, array{int, int}>  ['KG' => [buy, sell], 'LUONG' => [buy, sell]]
     */
    private function parseTable(string $html): array
    {
        libxml_use_internal_errors(true);
        $dom = new \DOMDocument();
        $dom->loadHTML('<?xml encoding="UTF-8">' . $html);
        $xpath = new \DOMXPath($dom);

        $result          = [];
        $inMyNgheSection = false;

        $rows = $xpath->query('//table//tr');

        foreach ($rows as $row) {
            $cells = $xpath->query('.//td|.//th', $row);
            $cols  = [];
            foreach ($cells as $cell) {
                $cols[] = trim(preg_replace('/\s+/', ' ', $cell->textContent));
            }

            if (empty($cols)) {
                continue;
            }

            // Section header: 1 cell, chữ IN HOA
            if (count($cols) === 1) {
                $upper = mb_strtoupper($cols[0], 'UTF-8');
                // Detect "BẠC MIẾNG MỸ NGHỆ"
                if (str_contains($upper, 'NGH')) {
                    $inMyNgheSection = true;
                } else {
                    $inMyNgheSection = false;
                }
                continue;
            }

            // Cần ít nhất 3 cột (tên | mua | bán)
            if (count($cols) < 3) {
                continue;
            }

            $name    = $cols[0];
            $buyRaw  = $cols[1];
            $sellRaw = $cols[2];
            $buy     = $this->parsePrice($buyRaw);
            $sell    = $this->parsePrice($sellRaw);

            // ── KG: Bạc Thỏi 1Kilo phiên bản 2025 ──
            if (!isset($result['KG'])
                && stripos($name, '1Kilo') !== false
                && stripos($name, '2025') !== false
                && $buy > 0 && $sell > 0
            ) {
                $result['KG'] = [$buy, $sell];
                $this->line("  🔍 KG match: {$name} → Mua=" . number_format($buy) . ' Bán=' . number_format($sell));
            }

            // ── LUONG: Mỹ Nghệ, sản phẩm 1 Lượng đầu tiên có đủ giá ──
            if (!isset($result['LUONG'])
                && $inMyNgheSection
                && preg_match('/\b1\s*L/iu', $name)
                && $buy > 0 && $sell > 0
            ) {
                $result['LUONG'] = [$buy, $sell];
                $this->line("  🔍 LUONG match: {$name} → Mua=" . number_format($buy) . ' Bán=' . number_format($sell));
            }

            // Dừng sớm nếu đã có cả hai
            if (isset($result['KG'], $result['LUONG'])) {
                break;
            }
        }

        return $result;
    }

    /**
     * Parse giá từ chuỗi của trang Kim Ngân Phúc.
     * Đơn vị gốc: 1.000 VND — "83.300" = 83.300 × 1.000 = 83.300.000 VND
     *
     * Ví dụ:
     *   "83.300" → bỏ dấu chấm → 83300 → × 1000 → 83,300,000
     *   "3.136"  → bỏ dấu chấm → 3136  → × 1000 → 3,136,000
     */
    private function parsePrice(string $raw): int
    {
        $raw = trim($raw);
        if (empty($raw) || $raw === '-') {
            return 0;
        }
        // Giữ lại chỉ các chữ số và dấu chấm
        $cleaned = preg_replace('/[^\d.]/', '', $raw);
        if (empty($cleaned)) {
            return 0;
        }
        // Xóa dấu chấm phân cách nghìn → ra số nguyên (×1000 đồng)
        $thousands = (int) str_replace('.', '', $cleaned);
        // Nhân ×1000 → ra VND
        return $thousands * 1000;
    }
}
