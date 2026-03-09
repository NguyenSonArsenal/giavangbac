<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class FetchAllSilverPrice extends Command
{
    protected $signature   = 'silver:fetch-all';
    protected $description = 'Gọi tất cả cron fetch giá bạc (dùng để test)';

    public function handle(): int
    {
        $commands = [
            'silver:fetch-phuquy',
            'silver:fetch-ancarat',
            'silver:fetch-doji',
            'silver:fetch-kimnganphuc',
        ];

        foreach ($commands as $cmd) {
            $this->info("▶ Chạy: {$cmd}");
            $this->call($cmd);
            $this->line('');
        }

        $this->info('✅ Đã chạy xong tất cả cron fetch.');
        return Command::SUCCESS;
    }
}
