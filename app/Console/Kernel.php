<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
	protected $commands = [
		\App\Console\Commands\FetchSilverPhuQuy::class,
		\App\Console\Commands\FetchSilverAncarat::class,
		\App\Console\Commands\FetchSilverDoji::class,
		\App\Console\Commands\FetchSilverKimNganPhuc::class,
		\App\Console\Commands\FetchAllSilverPrice::class,
		\App\Console\Commands\GenerateSilverTrend::class,
		\App\Console\Commands\EvaluateTrendAccuracy::class,
		\App\Console\Commands\FetchGoldBtmc::class,
		\App\Console\Commands\FetchGoldBtmh::class,
		\App\Console\Commands\FetchGoldPhuquy::class,
		\App\Console\Commands\FetchAllGoldPrice::class,
		\App\Console\Commands\FetchGoldSjc::class,
		\App\Console\Commands\FetchGoldSjc::class,
		\App\Console\Commands\SeedGoldBtmh::class,
		\App\Console\Commands\SyncFromRemoteDb::class,
		// ── Crypto Signal Scanner ──
		\App\Console\Commands\ScanCryptoSignal::class,
//		\App\Console\Commands\EvaluateCryptoSignal::class,
	];

	/**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // ── Phú Quý, Ancarat, DOJI ──────────────────────────────────────────
        // T2-T6: 8h–19h, mỗi 10 phút
        // T7   : 8h–10h, mỗi 10 phút
        // CN   : không chạy
        foreach (['silver:fetch-phuquy', 'silver:fetch-ancarat', 'silver:fetch-doji'] as $cmd) {
            // Thứ 2 → Thứ 6: 8:00 – 19:00 (mỗi 10 phút)
            $schedule->command($cmd)
                ->everyTenMinutes()
                ->weekdays()
                ->between('8:00', '19:00')
                ->withoutOverlapping();

            // Thứ 7: 8:00 – 10:00 (mỗi 10 phút)
            $schedule->command($cmd)
                ->everyTenMinutes()
                ->saturdays()
                ->between('8:00', '10:00')
                ->withoutOverlapping();

            // Thứ 2 → Thứ 6: 8:35 — bắt giá đầu ngày (branch thường set sau 8:30)
            $schedule->command($cmd)
                ->weekdays()
                ->at('8:35')
                ->withoutOverlapping();

            // Thứ 7: 8:35 — bắt giá đầu ngày
            $schedule->command($cmd)
                ->saturdays()
                ->at('8:35')
                ->withoutOverlapping();
        }

        // ── Kim Ngân Phúc ────────────────────────────────────────────────────
        // Hàng ngày (T2–CN): 8h–19h, mỗi 10 phút
        $schedule->command('silver:fetch-kimnganphuc')
            ->everyTenMinutes()
            ->between('8:00', '19:00')
            ->withoutOverlapping();

        // Thứ 2 → Thứ 7: 8:35 — bắt giá đầu ngày
        $schedule->command('silver:fetch-kimnganphuc')
            ->weekdays()
            ->at('8:35')
            ->withoutOverlapping();

        // ── Giá Vàng BTMC ────────────────────────────────────────────────────
        $schedule->command('gold:fetch-btmc')
            ->everyTenMinutes()
            ->between('8:00', '19:00')
            ->withoutOverlapping();

        $schedule->command('gold:fetch-btmc')
            ->weekdays()
            ->at('8:35')
            ->withoutOverlapping();

        $schedule->command('gold:fetch-btmc')
            ->saturdays()
            ->at('8:35')
            ->withoutOverlapping();

        // ── Giá Vàng BTMH (Bảo Tín Mạnh Hải) ──────────────────────────────────
        $schedule->command('gold:fetch-btmh')
            ->everyTenMinutes()
            ->between('8:00', '19:00')
            ->withoutOverlapping();

        $schedule->command('gold:fetch-btmh')
            ->weekdays()
            ->at('8:35')
            ->withoutOverlapping();

        $schedule->command('gold:fetch-btmh')
            ->saturdays()
            ->at('8:35')
            ->withoutOverlapping();

        // ── Giá Vàng Phú Quý (banggia.phuquygroup.vn) ────────────────────────────
        $schedule->command('gold:fetch-phuquy')
            ->everyTenMinutes()
            ->between('8:00', '19:00')
            ->withoutOverlapping();

        $schedule->command('gold:fetch-phuquy')
            ->weekdays()
            ->at('8:35')
            ->withoutOverlapping();

        $schedule->command('gold:fetch-phuquy')
            ->saturdays()
            ->at('8:35')
            ->withoutOverlapping();

        // ── Giá Vàng SJC (sjc.com.vn) ────────────────────────────────
        $schedule->command('gold:fetch-sjc')
            ->everyTenMinutes()
            ->between('8:00', '19:00')
            ->withoutOverlapping();

        $schedule->command('gold:fetch-sjc')
            ->weekdays()
            ->at('8:35')
            ->withoutOverlapping();

        $schedule->command('gold:fetch-sjc')
            ->saturdays()
            ->at('8:35')
            ->withoutOverlapping();

        // ── AI Nhận định xu hướng giá bạc ────────────────────────────────────
        // Chạy 1 lần/ngày lúc 19:30 (sau khi thị trường đóng, có đủ giá cả ngày)
        // Chỉ T2–T6 (T7, CN không giao dịch)
        $schedule->command('silver:generate-trend')
            ->weekdays()
            ->at('19:30')
            ->withoutOverlapping();

        // ── Tự động đánh giá độ chính xác nhận định AI ───────────────────────
        // Chạy hàng ngày lúc 8:00 sáng – sau khi có giá D+1 của ngày hôm trước
        $schedule->command('silver:evaluate-accuracy')
            ->dailyAt('8:00')
            ->withoutOverlapping();

        // ── Crypto Signal Scanner (Binance: BNB/USDT | MA + RSI) ───────────────
        // Khung nen 1H → check moi 15 phut (bat tin hieu som, khong qua nhieu)
        // Gio VN: 08:00–23:59 | Moi ngay: 64 lan check
        $schedule->command('crypto:scan-signal')
            ->everyMinute()          // 15 phut/lan — hop ly voi khung 1H
            ->between('08:00', '00:00')      // 8 gio sang den nua dem
            ->withoutOverlapping();

        // ── Danh gia ket qua tin hieu (WIN/LOSS/EXPIRED) ───────────────────
        // Chay moi 15 phut, check gia hien tai vs target/stop_loss
//        $schedule->command('crypto:evaluate-signal')
//            ->everyFifteenMinutes()
//            ->between('08:00', '23:59')
//            ->withoutOverlapping()
//            ->appendOutputTo(storage_path('logs/cron-crypto-signal.log'));
//
//        // Thong ke win rate luc 23:30 moi ngay
//        $schedule->command('crypto:evaluate-signal --stats')
//            ->dailyAt('23:30')
//            ->appendOutputTo(storage_path('logs/cron-crypto-signal.log'));
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
//        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
