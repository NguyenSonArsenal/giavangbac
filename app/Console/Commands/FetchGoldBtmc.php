<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\GoldPriceHistory;

class FetchGoldBtmc extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'gold:fetch-btmc';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch giá vàng từ BTMC (api.btmc.vn)';

    public function handle()
    {
        $logFile = storage_path('logs/cron-gold-btmc.log');
        $startAt = now()->format('Y-m-d H:i:s');
        file_put_contents($logFile, "[{$startAt}] ▶ gold:fetch-btmc START\n", FILE_APPEND);

        $this->info('[' . now()->format('Y-m-d H:i:s') . '] Fetch giá vàng BTMC...');

        $url = "http://api.btmc.vn/api/BTMCAPI/getpricebtmc?key=3kd8ub1llcg9t45hnoh8hmn7t5kc2v";

        try {
            $res = Http::timeout(15)->get($url);

            if (!$res->ok()) {
                $this->warn("  ❌ HTTP " . $res->status());
                Log::error('FetchBtmcGoldPrice: HTTP ' . $res->status());
                return 1; // FAILURE in laravel 8
            }

            $str = ltrim($res->body(), "\xEF\xBB\xBF");

            $dataList = null;

            if (str_starts_with(trim($str), '{')) {
                // It's JSON
                $json = json_decode($str, true);
                if (isset($json['DataList']['Data'])) {
                    $dataList = $json['DataList']['Data'];
                }
            } else {
                // It's XML
                $xml = simplexml_load_string($str);
                if ($xml && isset($xml->DataList->Data)) {
                    $dataList = [];
                    foreach ($xml->DataList->Data as $node) {
                        $attrs = collect($node->attributes())->toArray();
                        if (isset($attrs['@attributes'])) {
                            $dataList[] = $attrs['@attributes'];
                        }
                    }
                }
            }

            if (!$dataList) {
                $this->warn("  ⚠ Không parse được data (JSON/XML)");
                Log::error('FetchBtmcGoldPrice: Invalid response format');
                return 1;
            }

            // Target keywords to unit codes
            $targets = [
//                'VÀNG MIẾNG VRTL' => 'MIENG_VRTL',
//                'NHẪN TRÒN TRƠN'  => 'NHAN_TRON',

                'VÀNG MIẾNG VRTL (Vàng Rồng Thăng Long)' => 'MIENG_VRTL',
                'NHẪN TRÒN TRƠN (Vàng Rồng Thăng Long)'  => 'NHAN_TRON',
            ];

            // Track keyword nào đã được xử lý (API trả về mới nhất trước, chỉ lấy bản đầu tiên)
            $found = array_fill_keys(array_keys($targets), false);

            foreach ($dataList as $row) {
                // Nếu tất cả target đã tìm thấy thì dừng sớm
                if (!in_array(false, $found, true)) break;

                // Determine row index to access dynamic attributes like n_3, pb_3...
                // If it's from JSON parser, keys might be "@row" or "row"
                $rowId = null;
                if (isset($row['row'])) $rowId = $row['row'];
                elseif (isset($row['@row'])) $rowId = $row['@row'];

                if (!$rowId) continue;

                $nameAttr1 = "n_{$rowId}";
                $nameAttr2 = "@n_{$rowId}";

                $nameKey = isset($row[$nameAttr1]) ? $nameAttr1 : (isset($row[$nameAttr2]) ? $nameAttr2 : null);
                if (!$nameKey) continue;

                $buyKey  = isset($row["pb_{$rowId}"]) ? "pb_{$rowId}" : "@pb_{$rowId}";
                $sellKey = isset($row["ps_{$rowId}"]) ? "ps_{$rowId}" : "@ps_{$rowId}";
                $dateKey = isset($row["d_{$rowId}"])  ? "d_{$rowId}"  : (isset($row["@d_{$rowId}"]) ? "@d_{$rowId}" : null);

                $name = $row[$nameKey];
                $buy  = (int) ($row[$buyKey]  ?? 0);
                $sell = (int) ($row[$sellKey] ?? 0);

                // Parse thời gian từ API (format: "d/m/Y H:i", VD: "07/04/2026 15:16")
                $apiDateRaw = $dateKey ? (string)($row[$dateKey] ?? '') : '';
                $recordedAt = null;
                if ($apiDateRaw) {
                    try {
                        $recordedAt = \Carbon\Carbon::createFromFormat('d/m/Y H:i', trim($apiDateRaw));
                    } catch (\Exception) {
                        $recordedAt = null;
                    }
                }
                $recordedAt = $recordedAt ?? now();

                // Tìm xem name có chứa target keyword không
                foreach ($targets as $keyword => $unit) {
                    // Đã xử lý keyword này rồi (bản ghi mới nhất) → bỏ qua
                    if ($found[$keyword]) continue;

                    if (strpos($name, $keyword) !== false) {
                        // Đánh dấu đã tìm thấy bản ghi đầu tiên (mới nhất) cho keyword này
                        $found[$keyword] = true;

                        $lastRecord = GoldPriceHistory::where('source', 'btmc')
                            ->where('unit', $unit)
                            ->orderByDesc('recorded_at')
                            ->first();

                        if ($lastRecord && (int)$lastRecord->buy_price === $buy && (int)$lastRecord->sell_price === $sell) {
                            $this->line("  ⏭  History [{$unit}]: giá không đổi (Mua=" . number_format($buy) . ' Bán=' . number_format($sell) . '), bỏ qua');
                        } else {
                            GoldPriceHistory::create([
                                'source'      => 'btmc',
                                'unit'        => $unit,
                                'buy_price'   => $buy,
                                'sell_price'  => $sell,
                                'price_date'  => $recordedAt->toDateString(),
                                'recorded_at' => $recordedAt,
                            ]);
                            $this->info("  ✅ History [{$unit}] saved (Mua=" . number_format($buy) . ' Bán=' . number_format($sell) . ')');
                        }
                    }
                }
            }
        } catch (\Exception $e) {
            $this->error("  💥 Error: " . $e->getMessage());
            Log::error('FetchBtmcGoldPrice', ['error' => $e->getMessage()]);
            return 1;
        }

        $this->info('[' . now()->format('Y-m-d H:i:s') . '] Hoàn thành BTMC Gold.');
        return 0; // SUCCESS
    }
}
