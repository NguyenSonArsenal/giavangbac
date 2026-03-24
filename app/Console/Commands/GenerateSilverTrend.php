<?php

namespace App\Console\Commands;

use App\Models\SilverTrendLog;
use App\Models\SilverPriceHistory;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GenerateSilverTrend extends Command
{
    protected $signature = 'silver:generate-trend';
    protected $description = 'Phân tích xu hướng giá bạc bằng AI và lưu vào database';

    public function handle(): int
    {
        $this->info('🔍 Đang phân tích xu hướng giá bạc...');

        // Lấy dữ liệu giá 7 ngày
        $history = SilverPriceHistory::getHistory('phuquy', 'KG', 7);

        if ($history->isEmpty() || $history->count() < 2) {
            $this->warn('⚠️ Chưa đủ dữ liệu để phân tích.');
            return 1;
        }

        $latestSell  = $history->last()->sell_price;
        $latestBuy   = $history->last()->buy_price;
        $oldestSell  = $history->first()->sell_price;
        $pctChange   = $oldestSell > 0
            ? round((($latestSell - $oldestSell) / $oldestSell) * 100, 2)
            : 0;
        $highSell = $history->max('sell_price');
        $lowSell  = $history->min('sell_price');
        $avgSell  = round($history->avg('sell_price'));

        // So sánh hôm nay vs hôm qua
        $todayRecord     = $history->last();
        $yesterdayRecord = $history->count() >= 2 ? $history->slice(-2, 1)->first() : null;
        $dailyChange     = 0;
        $dailyChangePct  = 0;
        if ($yesterdayRecord && $yesterdayRecord->sell_price > 0) {
            $dailyChange    = $todayRecord->sell_price - $yesterdayRecord->sell_price;
            $dailyChangePct = round(($dailyChange / $yesterdayRecord->sell_price) * 100, 2);
        }

        $stats = [
            'period'     => '7 ngày',
            'pct_change' => $pctChange . '%',
            'trend'      => $pctChange > 0 ? 'tăng' : ($pctChange < 0 ? 'giảm' : 'đi ngang'),
            'high'       => number_format($highSell),
            'low'        => number_format($lowSell),
            'avg'        => number_format($avgSell),
            'latest'     => number_format($latestSell),
        ];

        // Chuẩn bị data cho AI
        $priceData = $history->map(function ($record) {
            return [
                'date'       => $record->price_date->format('d/m/Y'),
                'day'        => $record->price_date->locale('vi')->isoFormat('dddd'),
                'buy_price'  => number_format($record->buy_price),
                'sell_price' => number_format($record->sell_price),
            ];
        })->values()->toArray();

        // Gọi Gemini AI
        $analysis = $this->callGemini($priceData, $stats, $todayRecord, $dailyChangePct, $dailyChange);
        $source = 'gemini';

        if (!$analysis) {
            $analysis = $this->fallbackAnalysis($stats, $pctChange);
            $source = 'fallback';
            $this->warn('⚠️ Gemini không khả dụng, sử dụng fallback.');
        }

        // Lưu vào database
        $log = SilverTrendLog::create([
            'analysis'     => $analysis,
            'source'       => $source,
            'pct_change'   => $pctChange,
            'trend'        => $stats['trend'],
            'high_price'   => $highSell,
            'low_price'    => $lowSell,
            'latest_price' => $latestSell,
            'raw_stats'    => $stats,
        ]);

        // Update cache
        $cacheData = [
            'analysis'   => $analysis,
            'stats'      => $stats,
            'updated_at' => now()->format('H:i d/m/Y'),
            'log_id'     => $log->id,
        ];
        Cache::put('silver_trend_analysis', $cacheData, now()->addHours(12));

        $this->info("✅ Nhận định đã được tạo (source: {$source})");
        $this->info("📊 Xu hướng: {$stats['trend']} ({$stats['pct_change']})");
        $this->line($analysis);

        return 0;
    }

    private function callGemini(array $priceData, array $stats, $todayRecord, float $dailyChangePct, $dailyChange): ?string
    {
        $apiKey = config('services.gemini.api_key');
        if (!$apiKey) return null;

        $priceTable = collect($priceData)->map(function ($p) {
            return "- {$p['date']} ({$p['day']}): Mua {$p['buy_price']} | Bán {$p['sell_price']}";
        })->implode("\n");

        // Pre-compute cho heredoc
        $todayDate        = $todayRecord->price_date->format('d/m/Y');
        $todayBuyFmt      = $priceData[count($priceData) - 1]['buy_price'];
        $todaySellFmt     = $priceData[count($priceData) - 1]['sell_price'];
        $dailyChangeSign  = $dailyChange > 0 ? '+' : '';
        $dailyChangeFmt   = number_format($dailyChange);
        $dailyDir         = $dailyChangePct >= 0 ? 'tăng' : 'giảm';
        $dailyAbs         = abs($dailyChangePct);
        $dailyLabel       = $dailyAbs < 0.5 ? 'gần như không đổi' : ($dailyAbs < 2 ? "{$dailyDir} nhẹ" : ($dailyAbs < 5 ? "{$dailyDir} khá mạnh" : "cắm đầu {$dailyDir} mạnh"));

        $prompt = <<<PROMPT
Bạn là chuyên gia phân tích thị trường bạc.

DỮ LIỆU:
- Giá bạc hiện tại (bán ra): {$todaySellFmt} VND/KG
- Giá mua vào hôm nay: {$todayBuyFmt} VND/KG
- Giá cao nhất 7 ngày: {$stats['high']} VND/KG
- Giá thấp nhất 7 ngày: {$stats['low']} VND/KG
- Mức thay đổi so với hôm qua: {$dailyChangePct}% ({$dailyLabel})
- Mức thay đổi trong 7 ngày: {$stats['pct_change']}

BẢNG GIÁ 7 NGÀY (Bạc Phú Quý 999, VND/KG):
{$priceTable}

Hãy phân tích và trả lời theo cấu trúc:

1. Nhận định xu hướng hôm nay: tăng hay giảm (giải thích ngắn gọn)
2. Nhận định xu hướng 7 ngày gần đây: tăng, giảm hay đi ngang
3. Đánh giá xu hướng ngắn hạn sắp tới: có khả năng tăng hay giảm
4. Khuyến nghị:
   - Nếu đang giữ bạc → nên giữ hay bán
   - Nếu chưa mua → nên mua hay chờ thêm

Yêu cầu:
- Viết ngắn gọn, dễ hiểu cho người không chuyên
- Không dùng thuật ngữ kỹ thuật phức tạp
- Kết luận rõ ràng: NÊN MUA / NÊN BÁN / NÊN CHỜ
- KHÔNG dùng markdown, KHÔNG có tiêu đề dạng ##, KHÔNG đánh số 1. 2. 3., CHỈ trả về đoạn văn thuần
- KHÔNG có lời chào, lời mở đầu kiểu "Chào bạn" hay "Với vai trò...", vào thẳng nội dung
- Dùng số liệu THỰC TẾ từ dữ liệu đã cung cấp
PROMPT;

        try {
            $url = "https://generativelanguage.googleapis.com/v1/models/gemini-2.5-flash:generateContent?key={$apiKey}";

            $response = Http::timeout(30)
                ->withHeaders([
                    'Content-Type' => 'application/json',
                    'Accept'       => 'application/json',
                ])
                ->post($url, [
                    'contents' => [['parts' => [['text' => $prompt]]]],
                    'generationConfig' => ['temperature' => 0.4, 'maxOutputTokens' => 8192],
                ]);

            if ($response->successful()) {
                $text = $response->json('candidates.0.content.parts.0.text');
                Log::info('[SilverTrend] OK with gemini-2.5-flash');
                return $text ? trim($text) : null;
            }

            Log::warning('[SilverTrend] gemini-2.5-flash failed: ' . $response->status() . ' - ' . \Illuminate\Support\Str::limit($response->body(), 300));
            return null;
        } catch (\Exception $e) {
            Log::warning('[SilverTrend] Gemini exception: ' . $e->getMessage());
            return null;
        }
    }

    private function fallbackAnalysis(array $stats, float $pctChange): string
    {
        $absPct = abs($pctChange);

        if ($absPct < 0.5) {
            $trend = "Giá bạc 7 ngày qua đi ngang, dao động quanh vùng {$stats['low']}–{$stats['high']} VND/KG.";
            $trend .= " Thị trường đang trong giai đoạn tích lũy, chờ tín hiệu bứt phá rõ ràng hơn.";
            $trend .= " 💡 Khuyến nghị: NÊN CHỜ ĐỢI – thị trường chưa có xu hướng rõ ràng, hạn chế giao dịch cho đến khi có tín hiệu bứt phá.";
        } elseif ($pctChange > 0) {
            $trend = "Giá bạc tăng {$stats['pct_change']} trong 7 ngày qua, từ vùng {$stats['low']} lên {$stats['high']} VND/KG.";
            if ($absPct > 3) {
                $trend .= " Đà tăng mạnh, vùng kháng cự tiếp theo cần theo dõi quanh {$stats['high']} VND/KG.";
                $trend .= " 💡 Khuyến nghị: CÂN NHẮC BÁN RA chốt lời – giá đã tăng mạnh, rủi ro điều chỉnh là cao. Nếu muốn mua, nên chờ giá hồi về vùng {$stats['low']} VND/KG.";
            } else {
                $trend .= " Xu hướng tăng nhẹ, vùng hỗ trợ gần nhất tại {$stats['low']} VND/KG.";
                $trend .= " 💡 Khuyến nghị: CÓ THỂ MUA VÀO – xu hướng tăng đang được duy trì, ưu tiên mua khi giá hồi về gần vùng hỗ trợ {$stats['low']} VND/KG.";
            }
        } else {
            $trend = "Giá bạc giảm {$stats['pct_change']} trong 7 ngày qua, từ vùng {$stats['high']} về {$stats['low']} VND/KG.";
            if ($absPct > 3) {
                $trend .= " Áp lực bán mạnh, cần theo dõi vùng hỗ trợ quan trọng tại {$stats['low']} VND/KG.";
                $trend .= " 💡 Khuyến nghị: NÊN CHỜ ĐỢI – thị trường đang giảm mạnh, chưa nên mua vội. Chờ giá ổn định quanh vùng hỗ trợ {$stats['low']} VND/KG rồi hãy cân nhắc mua vào.";
            } else {
                $trend .= " Giảm nhẹ, thị trường có thể sớm hồi phục nếu giữ vững vùng {$stats['low']} VND/KG.";
                $trend .= " 💡 Khuyến nghị: CÓ THỂ MUA VÀO – giá đang ở vùng thấp, phù hợp để tích lũy nếu tin vào xu hướng dài hạn. Đặt cắt lỗ dưới vùng {$stats['low']} VND/KG.";
            }
        }

        return $trend;
    }
}
