<?php

namespace App\Console\Commands;

use App\Models\SilverPriceHistory;
use App\Models\SilverTrendLog;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

/**
 * Tự động đánh giá độ chính xác của nhận định AI xu hướng bạc.
 *
 * Logic:
 *  - Với mỗi nhận định chưa đánh giá (is_accurate = null)
 *  - Tìm giá đầu ngày hôm SAU nhận định (D+1, bản ghi đầu tiên của ngày đó)
 *  - So sánh với latest_price tại thời điểm nhận định
 *  - Nếu AI nói "tăng" mà sell_price đầu D+1 > latest_price → ĐÚNG, ngược lại SAI
 *  - Nếu AI nói "giảm" mà sell_price đầu D+1 < latest_price → ĐÚNG, ngược lại SAI
 *  - Nếu AI nói "đi ngang" → kiểm tra biến động < 0.5% → ĐÚNG
 *  - Nếu D+1 chưa có data (nhận định hôm nay hoặc dữ liệu thiếu) → bỏ qua
 */
class EvaluateTrendAccuracy extends Command
{
    protected $signature   = 'silver:evaluate-accuracy {--dry-run : Chỉ hiển thị, không ghi DB}';
    protected $description = 'Tự động đánh giá độ chính xác nhận định xu hướng bạc dựa trên giá thực tế ngày hôm sau';

    /** Nguồn giá tham chiếu cho việc đánh giá */
    const EVAL_SOURCE = 'phuquy';
    const EVAL_UNIT   = 'KG';

    /** Ngưỡng % để coi là "đi ngang" (dưới ngưỡng này) */
    const FLAT_THRESHOLD = 0.5;

    /** Cần dữ liệu từ ít nhất N giờ sau nhận định để đánh giá */
    const MIN_HOURS_AFTER = 8;

    public function handle(): int
    {
        $dryRun = $this->option('dry-run');
        $now    = now();

        $this->info('[' . $now->format('Y-m-d H:i:s') . '] Bắt đầu evaluate accuracy...');
        if ($dryRun) {
            $this->warn('  ⚠ DRY-RUN mode – không ghi vào DB');
        }

        // Lấy tất cả nhận định chưa đánh giá
        $pending = SilverTrendLog::whereNull('is_accurate')
            ->orderBy('created_at')
            ->get();

        $this->info("  📋 Có {$pending->count()} nhận định chưa đánh giá");

        $evaluated = 0;
        $skipped   = 0;

        foreach ($pending as $log) {
            $logTime = Carbon::parse($log->created_at);

            // Phải cách đủ MIN_HOURS_AFTER giờ mới có data để so
            if ($logTime->diffInHours($now) < self::MIN_HOURS_AFTER) {
                $this->line("  ⏩ #{$log->id} [{$logTime->format('d/m H:i')}]: Còn quá sớm để đánh giá, bỏ qua");
                $skipped++;
                continue;
            }

            // Lấy bản ghi đầu tiên của ngày hôm sau (giá đầu ngày D+1)
            $nextDate = $logTime->copy()->addDay()->toDateString();

            $firstNextPrice = SilverPriceHistory::where('source', self::EVAL_SOURCE)
                ->where('unit', self::EVAL_UNIT)
                ->where('price_date', $nextDate)
                ->orderBy('recorded_at')
                ->first();

            if (!$firstNextPrice) {
                // Thử tìm trong ngày kế tiếp nếu ngày sau không có (cuối tuần...)
                $firstNextPrice = SilverPriceHistory::where('source', self::EVAL_SOURCE)
                    ->where('unit', self::EVAL_UNIT)
                    ->where('price_date', '>', $logTime->toDateString())
                    ->orderBy('price_date')
                    ->orderBy('recorded_at')
                    ->first();
            }

            if (!$firstNextPrice) {
                $this->line("  ⏩ #{$log->id} [{$logTime->format('d/m H:i')}]: Chưa có giá ngày {$nextDate}, bỏ qua");
                $skipped++;
                continue;
            }

            $nextPrice = (int) $firstNextPrice->sell_price;
            $nextDateLabel = $firstNextPrice->price_date instanceof \Carbon\Carbon
                ? $firstNextPrice->price_date->format('d/m')
                : $firstNextPrice->price_date;

            $basePrice      = (int) $log->latest_price;
            if ($basePrice <= 0) {
                $this->line("  ⏩ #{$log->id}: latest_price = 0, bỏ qua");
                $skipped++;
                continue;
            }

            $changePct      = (($nextPrice - $basePrice) / $basePrice) * 100;
            $predictedTrend = strtolower(trim($log->trend));

            // Đánh giá độ chính xác
            $isAccurate = $this->evaluate($predictedTrend, $changePct);
            $direction  = $changePct > 0 ? '↑' : ($changePct < 0 ? '↓' : '→');

            $this->line(sprintf(
                "  %s #%d [%s]: Dự báo=%s | Giá đầu ngày %s=%s (%s%.2f%%) → %s",
                $isAccurate ? '✅' : '❌',
                $log->id,
                $logTime->format('d/m H:i'),
                $predictedTrend,
                $nextDateLabel,
                number_format($nextPrice),
                $direction,
                abs($changePct),
                $isAccurate ? 'ĐÚNG' : 'SAI'
            ));

            if (!$dryRun) {
                $log->is_accurate = $isAccurate;
                $log->admin_note  = sprintf(
                    'Auto-eval %s: base=%d first_D+1=%d change=%.2f%% (ngày %s)',
                    now()->format('Y-m-d H:i'),
                    $basePrice,
                    $nextPrice,
                    $changePct,
                    $nextDateLabel
                );
                $log->save();
                $evaluated++;
            } else {
                $evaluated++;
            }
        }

        // Tổng kết toàn bộ (kể cả các bản ghi đã đánh giá trước đó)
        $totalEvaluated = SilverTrendLog::whereNotNull('is_accurate')->count();
        $totalCorrect   = SilverTrendLog::where('is_accurate', true)->count();
        $accuracyPct    = $totalEvaluated > 0
            ? round(($totalCorrect / $totalEvaluated) * 100)
            : 0;

        $this->info(sprintf(
            '✅ Đã đánh giá xong! Tỷ lệ đúng: %d/%d (%d%%)',
            $totalCorrect,
            $totalEvaluated,
            $accuracyPct
        ));

        return Command::SUCCESS;
    }

    /**
     * Đánh giá dự báo có đúng không dựa trên % thay đổi thực tế.
     *
     * @param  string $predicted  'tăng' | 'giảm' | 'đi ngang' | 'flat' | 'up' | 'down'
     * @param  float  $changePct  % thay đổi thực tế (dương = tăng, âm = giảm)
     */
    private function evaluate(string $predicted, float $changePct): bool
    {
        $absChange = abs($changePct);

        // Chuẩn hóa trend về 3 giá trị
        if (in_array($predicted, ['tăng', 'tang', 'up', 'rise', 'tăng mạnh'])) {
            // Đúng nếu thực tế tăng (>= 0.2%)
            return $changePct >= 0.2;
        }

        if (in_array($predicted, ['giảm', 'giam', 'down', 'fall', 'giảm mạnh'])) {
            // Đúng nếu thực tế giảm (<= -0.2%)
            return $changePct <= -0.2;
        }

        // "đi ngang" / flat
        // Đúng nếu biến động dưới ngưỡng FLAT_THRESHOLD
        return $absChange < self::FLAT_THRESHOLD;
    }
}
