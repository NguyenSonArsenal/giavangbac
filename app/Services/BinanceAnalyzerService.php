<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

/**
 * BinanceAnalyzerService
 * ─────────────────────────────────────────────────────────────────────────────
 * Ưu tiên   : Binance public API (klines) — Hoạt động tốt local/VN
 * Fallback 1: Bybit  public API — Không block IP Mỹ, có đủ OHLCV!
 * Fallback 2: CoinGecko OHLC API — Dự phòng cuối cùng
 * Tính toán: MA7 / MA25 / MA99, RSI(14), chấm điểm tín hiệu mua 0–12
 */
class BinanceAnalyzerService
{
    const BASE_URL      = 'https://api.binance.com';
    const BYBIT_URL     = 'https://api.bybit.com/v5/market/kline';
    const COINGECKO_URL = 'https://api.coingecko.com/api/v3';

    /** Binance interval → Bybit interval */
    const BYBIT_INTERVAL_MAP = [
        '1m'  => '1',
        '5m'  => '5',
        '15m' => '15',
        '30m' => '30',
        '1h'  => '60',
        '2h'  => '120',
        '4h'  => '240',
        '1d'  => 'D',
    ];

    /** Map Binance symbol → CoinGecko coin id */
    const COINGECKO_MAP = [
        'BNBUSDT'  => 'binancecoin',
        'BTCUSDT'  => 'bitcoin',
        'ETHUSDT'  => 'ethereum',
        'XAUTUSDT' => 'tether-gold',
    ];

    /**
     * Phân tích 1 cặp coin.
     *
     * @param  string $symbol   Ví dụ: BNBUSDT, BTCUSDT, XAUTUSDT
     * @param  string $interval Khung nến: 15m (mặc định)
     * @param  int    $limit    Số nến cần lấy (tối thiểu 120 để tính MA99 + RSI)
     * @return array|null       Kết quả phân tích hoặc null nếu lỗi
     */
    public function analyze(string $symbol, string $interval = '15m', int $limit = 150): ?array
    {
        // ── 1. Gọi Binance API ───────────────────────────────────────────────
        $klines = $this->fetchKlines($symbol, $interval, $limit);
        if (!$klines) {
            return null;
        }

        // ── 2. Parse OHLCV ───────────────────────────────────────────────────
        $opens   = array_column($klines, 1);
        $highs   = array_column($klines, 2);
        $lows    = array_column($klines, 3);
        $closes  = array_column($klines, 4);
        $volumes = array_column($klines, 5);

        // Cast sang float
        $opens   = array_map('floatval', $opens);
        $highs   = array_map('floatval', $highs);
        $lows    = array_map('floatval', $lows);
        $closes  = array_map('floatval', $closes);
        $volumes = array_map('floatval', $volumes);

        $n = count($closes);

        // Nến cuối cùng (hiện tại)
        $price       = $closes[$n - 1];
        $openLast    = $opens[$n - 1];
        $highLast    = $highs[$n - 1];
        $lowLast     = $lows[$n - 1];
        $volCurrent  = $volumes[$n - 1];
        $volAvg      = $this->average(array_slice($volumes, -21, 20)); // TB 20 nến trước

        // ── 3. Tính chỉ báo kỹ thuật ────────────────────────────────────────
        $ma7  = $this->calcMA($closes, 7);
        $ma25 = $this->calcMA($closes, 25);
        $ma99 = $this->calcMA($closes, 99);
        $rsi  = $this->calcRSI($closes, 14);

        if ($ma7 === null || $ma25 === null || $ma99 === null || $rsi === null) {
            Log::warning("BinanceAnalyzer: Không đủ dữ liệu để tính chỉ báo [{$symbol}]");
            return null;
        }

        // ── 4. Chấm điểm tín hiệu (thang 0–12) ─────────────────────────────
        $score    = 0;
        $reasons  = [];
        $warnings = [];

        // ── Điều kiện 1: Xu hướng (MA99) — quan trọng nhất ──────────────────
        if ($price > $ma99) {
            $score += 2;
            $pctAbove = round((($price - $ma99) / $ma99) * 100, 2);
            $reasons[] = "Gia ({$price}) TREN MA99 ({$ma99}) +{$pctAbove}% — Trend tang";
        } else {
            $score -= 2;
            $pctBelow = round((($ma99 - $price) / $ma99) * 100, 2);
            $warnings[] = "Gia ({$price}) DUOI MA99 ({$ma99}) -{$pctBelow}% — Trend yeu, rui ro cao!";
        }

        // ── Điều kiện 2: RSI ─────────────────────────────────────────────────
        $rsiRound = round($rsi, 1);
        if ($rsi < 20) {
            $score += 4;
            $reasons[] = "RSI {$rsiRound} — Cuc ky Oversold! Co hoi lon nhat";
        } elseif ($rsi < 30) {
            $score += 3;
            $reasons[] = "RSI {$rsiRound} — Oversold manh — Vung mua rat tot";
        } elseif ($rsi < 40) {
            $score += 2;
            $reasons[] = "RSI {$rsiRound} — Oversold nhe — Vung mua tot";
        } elseif ($rsi < 50) {
            $score += 1;
            $reasons[] = "RSI {$rsiRound} — Hoi thap — Theo doi";
        } elseif ($rsi < 65) {
            // Trung tính — không cộng không trừ
        } elseif ($rsi < 72) {
            $score -= 1;
            $warnings[] = "RSI {$rsiRound} — Dang Overbought nhe";
        } else {
            $score -= 3;
            $warnings[] = "RSI {$rsiRound} — OVERBOUGHT! Khong mua luc nay!";
        }

        // ── Điều kiện 3: Giá chạm vùng hỗ trợ MA ───────────────────────────
        $pctFromMA99 = abs($price - $ma99) / $ma99;
        $pctFromMA25 = abs($price - $ma25) / $ma25;
        $pctFromMA7  = abs($price - $ma7)  / $ma7;

        if ($pctFromMA99 <= 0.005 && $price >= $ma99 * 0.995) {
            // Giá đang chạm MA99 (trong vòng 0.5%)
            $score += 3;
            $ma99Round = round($ma99, 4);
            $reasons[] = "Gia dang CHAM MA99 ({$ma99Round}) — Ho tro manh nhat!";
        } elseif ($pctFromMA25 <= 0.003 && $price >= $ma25 * 0.997) {
            // Giá đang chạm MA25 (trong vòng 0.3%)
            $score += 2;
            $ma25Round = round($ma25, 4);
            $reasons[] = "Gia dang CHAM MA25 ({$ma25Round}) — Vung ho tro tot";
        } elseif ($pctFromMA7 <= 0.002 && $price >= $ma7 * 0.998) {
            // Giá đang chạm MA7 (trong vòng 0.2%)
            $score += 1;
            $ma7Round = round($ma7, 4);
            $reasons[] = "Gia dang CHAM MA7 ({$ma7Round}) — Ho tro ngan han";
        }

        // ── Điều kiện 4: MA7 cắt lên MA25 (Golden Cross ngắn hạn) ───────────
        // Lấy MA7 và MA25 của nến sát trước để so sánh chiều
        $prevCloses = array_slice($closes, 0, $n - 1);
        $prevMA7    = $this->calcMA($prevCloses, 7);
        $prevMA25   = $this->calcMA($prevCloses, 25);
        if ($prevMA7 && $prevMA25) {
            if ($prevMA7 < $prevMA25 && $ma7 >= $ma25) {
                $score += 2;
                $reasons[] = "MA7 vua cat len MA25 (Golden Cross ngan han) — Xu huong dang doi tang!";
            }
        }

        // ── Điều kiện 5: Volume xác nhận ────────────────────────────────────
        if ($volAvg > 0) {
            $volRatio = $volCurrent / $volAvg;
            if ($volRatio >= 2.0) {
                $score += 2;
                $volR = round($volRatio, 1);
                $reasons[] = "Volume tang {$volR}x so trung binh — Luc mua/ban rat manh";
            } elseif ($volRatio >= 1.4) {
                $score += 1;
                $volR = round($volRatio, 1);
                $reasons[] = "Volume tang {$volR}x — Co luc mua xac nhan";
            }
        }

        // ── Điều kiện 6: Mẫu nến Hammer (bấc dài dưới) ─────────────────────
        $body       = abs($price - $openLast);
        $lowerWick  = min($openLast, $price) - $lowLast;
        $upperWick  = $highLast - max($openLast, $price);
        $isGreenCandle = $price > $openLast;

        if ($body > 0 && $lowerWick >= $body * 1.8 && $isGreenCandle && $upperWick < $body) {
            $score += 1;
            $reasons[] = "Nen bac dai duoi (Hammer) — Phe mua dang vao manh!";
        }

        // ── 5. Xác định loại tín hiệu ────────────────────────────────────────
        // Nếu RSI > 72 thì force không mua dù score cao
        if ($rsi > 72) {
            $signalType  = 'NO_SIGNAL';
            $signalLabel = 'KHONG MUA — RSI Overbought';
        } elseif ($score >= 7) {
            $signalType  = 'STRONG_BUY';
            $signalLabel = 'MUA MANH — Hoi tu nhieu dieu kien!';
        } elseif ($score >= 5) {
            $signalType  = 'BUY';
            $signalLabel = 'XEM XET MUA — Tin hieu tot';
        } elseif ($score >= 3) {
            $signalType  = 'WATCH';
            $signalLabel = 'QUAN SAT — Chua du dieu kien, theo doi';
        } else {
            $signalType  = 'NO_SIGNAL';
            $signalLabel = 'KHONG CO TIN HIEU';
        }

        return [
            'symbol'         => $symbol,
            'interval'       => $interval,
            'price'          => $price,
            'ma7'            => round($ma7,  4),
            'ma25'           => round($ma25, 4),
            'ma99'           => round($ma99, 4),
            'rsi'            => round($rsi,  2),
            'volume_current' => round($volCurrent, 4),
            'volume_avg'     => round($volAvg,     4),
            'score'          => $score,
            'signal_type'    => $signalType,
            'signal_label'   => $signalLabel,
            'reasons'        => $reasons,
            'warnings'       => $warnings,
        ];
    }

    // ── Private: Gọi Binance Klines API (Primary) ──────────────────────────
    private function fetchKlines(string $symbol, string $interval, int $limit): ?array
    {
        // ── Bước 1: Thử Binance trước ───────────────────────────────────────
        $url = self::BASE_URL . '/api/v3/klines?' . http_build_query([
            'symbol'   => $symbol,
            'interval' => $interval,
            'limit'    => $limit,
        ]);

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => 15,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_HTTPHEADER     => [
                'Accept: application/json',
                'User-Agent: Mozilla/5.0 (compatible; TradingBot/1.0)',
            ],
        ]);

        $body     = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlErr  = curl_error($ch);
        curl_close($ch);

        if ($curlErr) {
            Log::error("BinanceAnalyzer: cURL error [{$symbol}]", ['error' => $curlErr]);
        } elseif ($httpCode === 200) {
            $data = json_decode($body, true);
            if (is_array($data) && count($data) >= 30) {
                return $data; // Binance thanh cong
            }
        } else {
            Log::warning("BinanceAnalyzer: Binance HTTP {$httpCode} [{$symbol}] — thu Bybit...");
        }

        // Buoc 2: Fallback Bybit (khong block IP My)
        $bybitData = $this->fetchFromBybit($symbol, $interval, $limit);
        if ($bybitData) {
            return $bybitData;
        }

        // Buoc 3: Fallback CoinGecko (du phong cuoi)
        return $this->fetchFromCoinGecko($symbol);
    }

    // Goi Bybit Klines API (Fallback 1 — khong block IP My, co OHLCV day du)
    private function fetchFromBybit(string $symbol, string $interval, int $limit): ?array
    {
        $bybitInterval = self::BYBIT_INTERVAL_MAP[$interval] ?? '60';

        $url = self::BYBIT_URL . '?' . http_build_query([
            'category' => 'spot',
            'symbol'   => $symbol,
            'interval' => $bybitInterval,
            'limit'    => min($limit, 200),
        ]);

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => 15,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_HTTPHEADER     => [
                'Accept: application/json',
                'User-Agent: Mozilla/5.0 (compatible; TradingBot/1.0)',
            ],
        ]);

        $body     = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlErr  = curl_error($ch);
        curl_close($ch);

        if ($curlErr) {
            Log::error("Bybit: cURL error [{$symbol}]", ['error' => $curlErr]);
            return null;
        }

        if ($httpCode !== 200) {
            Log::error("Bybit: HTTP {$httpCode} [{$symbol}]");
            return null;
        }

        $json = json_decode($body, true);
        $list = $json['result']['list'] ?? null;

        if (!is_array($list) || count($list) < 30) {
            Log::error("Bybit: Du lieu khong hop le [{$symbol}]");
            return null;
        }

        Log::info("Bybit: Da lay " . count($list) . " nen [{$symbol}] (fallback 1 thanh cong)");
        return $this->convertBybitToKlines($list);
    }

    /**
     * Convert Bybit klines sang Binance format.
     * Bybit:   [startTime, open, high, low, close, volume, turnover]  (moi -> cu)
     * Binance: [ts, open, high, low, close, volume, ...]              (cu -> moi)
     */
    private function convertBybitToKlines(array $list): array
    {
        // Bybit tra newest-first, can reverse thanh oldest-first
        $reversed = array_reverse($list);

        return array_map(function ($candle) {
            return [
                (int)$candle[0],    // 0: timestamp
                (string)$candle[1], // 1: open
                (string)$candle[2], // 2: high
                (string)$candle[3], // 3: low
                (string)$candle[4], // 4: close
                (string)$candle[5], // 5: volume (Bybit co volume day du!)
            ];
        }, $reversed);
    }

    // ── Private: Gọi CoinGecko OHLC API (Fallback) ─────────────────────────
    private function fetchFromCoinGecko(string $symbol): ?array
    {
        $coinId = self::COINGECKO_MAP[$symbol] ?? null;
        if (!$coinId) {
            Log::error("BinanceAnalyzer: Không có CoinGecko map cho [{$symbol}]");
            return null;
        }

        // Lấy 30 ngày OHLC, mỗi điểm = 1 giờ (đủ để tính MA99)
        $url = self::COINGECKO_URL . "/coins/{$coinId}/ohlc?vs_currency=usd&days=30";

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => 20,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_HTTPHEADER     => [
                'Accept: application/json',
                'User-Agent: Mozilla/5.0 (compatible; TradingBot/1.0)',
            ],
        ]);

        $body     = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlErr  = curl_error($ch);
        curl_close($ch);

        if ($curlErr) {
            Log::error("CoinGecko: cURL error [{$symbol}]", ['error' => $curlErr]);
            return null;
        }

        if ($httpCode !== 200) {
            Log::error("CoinGecko: HTTP {$httpCode} [{$symbol}]");
            return null;
        }

        $raw = json_decode($body, true);
        if (!is_array($raw) || count($raw) < 30) {
            Log::error("CoinGecko: Dữ liệu không hợp lệ [{$symbol}]");
            return null;
        }

        Log::info("CoinGecko: Đã lấy " . count($raw) . " nến [{$symbol}] (fallback thành công)");
        return $this->convertCoinGeckoToKlines($raw);
    }

    /**
     * Convert CoinGecko OHLC format sang Binance klines format.
     * CoinGecko: [timestamp_ms, open, high, low, close]
     * Binance:   [ts, open, high, low, close, volume, ...] (index 1-5)
     */
    private function convertCoinGeckoToKlines(array $raw): array
    {
        return array_map(function ($candle) {
            // Binance klines: index 0=ts, 1=open, 2=high, 3=low, 4=close, 5=volume
            return [
                $candle[0],            // 0: timestamp
                (string)$candle[1],    // 1: open
                (string)$candle[2],    // 2: high
                (string)$candle[3],    // 3: low
                (string)$candle[4],    // 4: close
                '0',                   // 5: volume (CoinGecko OHLC không có volume)
            ];
        }, $raw);
    }

    // ── Private: Tính Moving Average ─────────────────────────────────────────
    private function calcMA(array $closes, int $period): ?float
    {
        $n = count($closes);
        if ($n < $period) {
            return null;
        }
        return array_sum(array_slice($closes, -$period)) / $period;
    }

    // ── Private: Tính RSI (Wilder's method) ─────────────────────────────────
    private function calcRSI(array $closes, int $period = 14): ?float
    {
        $n = count($closes);
        if ($n < $period + 1) {
            return null;
        }

        // Tính gain/loss cho $period nến đầu tiên
        $gains  = 0.0;
        $losses = 0.0;

        for ($i = $n - $period; $i < $n; $i++) {
            $diff = $closes[$i] - $closes[$i - 1];
            if ($diff > 0) {
                $gains  += $diff;
            } else {
                $losses += abs($diff);
            }
        }

        $avgGain = $gains  / $period;
        $avgLoss = $losses / $period;

        if ($avgLoss == 0) {
            return 100.0;
        }

        $rs  = $avgGain / $avgLoss;
        $rsi = 100 - (100 / (1 + $rs));

        return $rsi;
    }

    // ── Private: Tính trung bình mảng ───────────────────────────────────────
    private function average(array $arr): float
    {
        if (empty($arr)) {
            return 0.0;
        }
        return array_sum($arr) / count($arr);
    }
}
