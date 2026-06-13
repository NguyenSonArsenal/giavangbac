<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

/**
 * Kéo dữ liệu bảng metal_prices từ DB server về local theo ngày.
 *
 * Cách dùng:
 *   php artisan db:sync-remote                          → đồng bộ hôm nay
 *   php artisan db:sync-remote 2026-05-01 2026-05-31    → sync khoảng từ ngày A đến ngày B
 *   php artisan db:sync-remote 2026-05-01               → sync từ ngày A đến hôm nay
 *   php artisan db:sync-remote --date=2026-04-13        → 1 ngày cụ thể
 *   php artisan db:sync-remote --days=7                 → 7 ngày gần nhất
 *   php artisan db:sync-remote --date=2026-04-10 --date=2026-04-11  → nhiều ngày
 *   php artisan db:sync-remote --dry-run                → xem trước, không insert
 *
 * # Sync hôm nay (mặc định)
 * php artisan db:sync-remote
 * # Sync khoảng ngày (A → B)
 * php artisan db:sync-remote 2026-05-01 2026-05-31
 * # Sync từ ngày A đến hôm nay
 * php artisan db:sync-remote 2026-05-01
 * # Sync 1 ngày cụ thể
 * php artisan db:sync-remote --date=2026-04-13
 * # Sync nhiều ngày
 * php artisan db:sync-remote --date=2026-04-10 --date=2026-04-11 --date=2026-04-12
 * # Sync 7 ngày gần nhất
 * php artisan db:sync-remote --days=7
 * # Chỉ sync vàng trong khoảng ngày
 * php artisan db:sync-remote 2026-05-01 2026-05-31 --metal=gold
 * # Chỉ sync bạc Phú Quý
 * php artisan db:sync-remote --days=3 --metal=silver --source=phuquy
 * # Xem trước (không ghi DB)
 * php artisan db:sync-remote 2026-05-01 2026-05-31 --dry-run
 * # Ghi đè bản ghi đã tồn tại
 * php artisan db:sync-remote --date=2026-04-13 --force
 *
 * Lưu ý: Phải cấu hình REMOTE_DB_* trong .env trước khi chạy.
 */
class SyncFromRemoteDb extends Command
{
    protected $signature = 'db:sync-remote
                            {from?         : Ngày bắt đầu khoảng sync (Y-m-d). Nếu chỉ truyền from thì sync từ from đến hôm nay}
                            {to?           : Ngày kết thúc khoảng sync (Y-m-d). Dùng kèm with from}
                            {--date=*      : Ngày cụ thể cần sync (Y-m-d), có thể truyền nhiều lần}
                            {--days=       : Sync N ngày gần nhất (thay thế --date)}
                            {--metal=      : Lọc theo metal_type: gold | silver | all (mặc định: all)}
                            {--source=     : Lọc theo source cụ thể: btmc | btmh | phuquy | sjc...}
                            {--dry-run     : Chỉ xem trước, không ghi vào local DB}
                            {--force       : Ghi đè bản ghi đã tồn tại (mặc định: bỏ qua duplicate)}';

    protected $description = 'Kéo dữ liệu metal_prices từ DB server về local theo ngày';

    public function handle(): int
    {
        // ── 1. Kiểm tra kết nối remote ──────────────────────────────────────
        $this->info('🔗 Kiểm tra kết nối tới DB server...');
        try {
            DB::connection('mysql_remote')->getPdo();
            $this->info('   ✅ Kết nối thành công!');
        } catch (\Exception $e) {
            $this->error('   ❌ Không kết nối được DB server: ' . $e->getMessage());
            $this->line('');
            $this->warn('👉 Hãy kiểm tra các biến sau trong file .env:');
            $this->line('   REMOTE_DB_HOST=<IP hoặc domain server>');
            $this->line('   REMOTE_DB_PORT');
            $this->line('   REMOTE_DB_DATABASE');
            $this->line('   REMOTE_DB_USERNAME');
            $this->line('   REMOTE_DB_PASSWORD');
            return 1;
        }

        // ── 2. Xác định danh sách ngày cần sync ─────────────────────────────
        $dates = $this->resolveDates();
        if (empty($dates)) {
            $this->error('Không xác định được ngày cần sync.');
            return 1;
        }

        $isDryRun = $this->option('dry-run');
        $forceOverwrite = $this->option('force');
        $metalFilter = $this->option('metal') ?: 'all';
        $sourceFilter = $this->option('source') ?: null;

        $this->line('');
        $this->info('📅 Ngày cần sync: ' . implode(', ', $dates));
        $this->info('🏷  metal_type  : ' . $metalFilter);
        if ($sourceFilter) $this->info('🏢 source      : ' . $sourceFilter);
        if ($isDryRun)   $this->warn('👁  DRY-RUN     : Chỉ xem trước, không ghi DB');
        if ($forceOverwrite) $this->warn('⚠  FORCE       : Sẽ ghi đè bản ghi đã tồn tại');
        $this->line('');

        $totalInserted = 0;
        $totalSkipped  = 0;
        $totalUpdated  = 0;

        // ── 3. Xử lý từng ngày ──────────────────────────────────────────────
        foreach ($dates as $date) {
            $this->line("─── 📆 Đang sync ngày: <comment>{$date}</comment> ───");

            // Query từ server
            $query = DB::connection('mysql_remote')
                ->table('metal_prices')
                ->whereDate('price_date', $date);

            if ($metalFilter !== 'all') {
                $query->where('metal_type', $metalFilter);
            }
            if ($sourceFilter) {
                $query->where('source', $sourceFilter);
            }

            $remoteRows = $query->orderBy('recorded_at')->get();

            if ($remoteRows->isEmpty()) {
                $this->warn("   ⚠ Server không có dữ liệu ngày {$date}");
                continue;
            }

            $this->line("   📥 Server trả về: <info>{$remoteRows->count()}</info> bản ghi");

            foreach ($remoteRows as $row) {
                $data = [
                    'metal_type'  => $row->metal_type,
                    'source'      => $row->source,
                    'unit'        => $row->unit,
                    'buy_price'   => $row->buy_price,
                    'sell_price'  => $row->sell_price,
                    'price_date'  => $row->price_date,
                    'recorded_at' => $row->recorded_at,
                    'created_at'  => $row->created_at ?? now(),
                    'updated_at'  => now(),
                ];

                $label = "[{$row->metal_type}|{$row->source}|{$row->unit}] Mua=" . number_format($row->buy_price) . " Bán=" . number_format($row->sell_price) . " @ " . $row->recorded_at;

                // Tìm bản ghi trùng: cùng source + unit + metal_type + recorded_at
                $exists = DB::table('metal_prices')
                    ->where('metal_type', $row->metal_type)
                    ->where('source', $row->source)
                    ->where('unit', $row->unit)
                    ->where('recorded_at', $row->recorded_at)
                    ->first();

                if ($exists) {
                    if ($forceOverwrite) {
                        if (!$isDryRun) {
                            DB::table('metal_prices')
                                ->where('id', $exists->id)
                                ->update($data);
                        }
                        $this->line("   🔄 UPDATE : {$label}");
                        $totalUpdated++;
                    } else {
                        $this->line("   ⏭  SKIP   : {$label} (đã tồn tại)");
                        $totalSkipped++;
                    }
                } else {
                    if (!$isDryRun) {
                        DB::table('metal_prices')->insert($data);
                    }
                    $this->info("   ✅ INSERT : {$label}");
                    $totalInserted++;
                }
            }

            $this->line('');
        }

        // ── 4. Tổng kết ─────────────────────────────────────────────────────
        $this->line('══════════════════════════════════════════');
        if ($isDryRun) {
            $this->warn('🔍 DRY-RUN – Không có thay đổi nào được lưu');
        }
        $this->info("✅ INSERT : {$totalInserted} bản ghi");
        if ($totalUpdated > 0) $this->info("🔄 UPDATE : {$totalUpdated} bản ghi");
        $this->line("⏭  SKIP   : {$totalSkipped} bản ghi (đã có local)");
        $this->line('══════════════════════════════════════════');

        return 0;
    }

    /**
     * Tính danh sách ngày cần sync từ các option.
     *
     * Thứ tự ưu tiên:
     *   1. Argument positional: from [to]  → khoảng ngày A–B
     *   2. --days=N                        → N ngày gần nhất
     *   3. --date=... (nhiều lần)          → danh sách ngày cụ thể
     *   4. Mặc định                        → hôm nay
     *
     * @return string[]  VD: ['2026-04-13', '2026-04-14']
     */
    private function resolveDates(): array
    {
        // ── Ưu tiên 1: argument positional from [to] ─────────────────────────
        $fromArg = $this->argument('from');
        if ($fromArg) {
            try {
                $from = Carbon::createFromFormat('Y-m-d', $fromArg)->startOfDay();
            } catch (\Exception) {
                $this->error("Ngày 'from' không hợp lệ: {$fromArg} (định dạng Y-m-d, VD: 2026-05-01)");
                return [];
            }

            $toArg = $this->argument('to');
            if ($toArg) {
                try {
                    $to = Carbon::createFromFormat('Y-m-d', $toArg)->startOfDay();
                } catch (\Exception) {
                    $this->error("Ngày 'to' không hợp lệ: {$toArg} (định dạng Y-m-d, VD: 2026-05-31)");
                    return [];
                }
            } else {
                // Chỉ có from → sync từ from đến hôm nay
                $to = Carbon::today();
            }

            if ($from->gt($to)) {
                $this->error("Ngày 'from' ({$from->toDateString()}) phải nhỏ hơn hoặc bằng 'to' ({$to->toDateString()})");
                return [];
            }

            $dates = [];
            $cursor = $from->copy();
            while ($cursor->lte($to)) {
                $dates[] = $cursor->format('Y-m-d');
                $cursor->addDay();
            }
            return $dates;
        }

        // ── Ưu tiên 2: --days=N → N ngày gần nhất kể từ hôm nay ─────────────
        if ($days = $this->option('days')) {
            $days = (int) $days;
            if ($days <= 0) {
                $this->error('--days phải là số nguyên dương');
                return [];
            }
            $dates = [];
            for ($i = $days - 1; $i >= 0; $i--) {
                $dates[] = Carbon::today()->subDays($i)->format('Y-m-d');
            }
            return $dates;
        }

        // ── Ưu tiên 3: --date (có thể truyền nhiều lần) ──────────────────────
        $dateOptions = (array) $this->option('date');
        $dateOptions = array_filter($dateOptions); // bỏ giá trị rỗng

        if (!empty($dateOptions)) {
            $dates = [];
            foreach ($dateOptions as $d) {
                try {
                    $dates[] = Carbon::createFromFormat('Y-m-d', $d)->format('Y-m-d');
                } catch (\Exception) {
                    $this->error("Ngày không hợp lệ: {$d} (định dạng Y-m-d, VD: 2026-04-13)");
                }
            }
            return array_unique($dates);
        }

        // ── Mặc định: hôm nay ─────────────────────────────────────────────────
        return [Carbon::today()->format('Y-m-d')];
    }
}
