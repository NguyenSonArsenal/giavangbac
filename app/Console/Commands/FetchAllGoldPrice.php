<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class FetchAllGoldPrice extends Command
{
    protected $signature   = 'gold:fetch-all';
    protected $description = 'Gọi tất cả cron fetch giá vàng (dùng để test)';

    public function handle(): int
    {
        $commands = [
//            'gold:fetch-btmc',
            'gold:fetch-btmh',
            'gold:fetch-phuquy',
//            'gold:fetch-sjc',
        ];

        foreach ($commands as $cmd) {
            $this->info("▶ Chạy: {$cmd}");
            $this->call($cmd);
            $this->line('');
        }

        $this->info('✅ Đã chạy xong tất cả cron fetch vàng.');
        return Command::SUCCESS;
    }
}
