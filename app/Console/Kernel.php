<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
	protected $commands = [
		\App\Console\Commands\FetchPhuQuySilverPrice::class,
		\App\Console\Commands\FetchAncaratSilverPrice::class,
		\App\Console\Commands\FetchDojiSilverPrice::class,
		\App\Console\Commands\FetchKimNganPhucSilverPrice::class,
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
            // Thứ 2 → Thứ 6: 8:00 – 19:00
            $schedule->command($cmd)
                ->everyTenMinutes()
                ->weekdays()
                ->between('8:00', '19:00')
                ->withoutOverlapping();

            // Thứ 7: 8:00 – 10:00
            $schedule->command($cmd)
                ->everyTenMinutes()
                ->saturdays()
                ->between('8:00', '10:00')
                ->withoutOverlapping();
        }

        // ── Kim Ngân Phúc ────────────────────────────────────────────────────
        // Hàng ngày (T2–CN): 8h–19h, mỗi 10 phút
        $schedule->command('silver:fetch-kimnganphuc')
            ->everyTenMinutes()
            ->between('8:00', '19:00')
            ->withoutOverlapping();
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
