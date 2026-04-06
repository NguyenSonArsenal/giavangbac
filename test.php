<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Test currentPrice
$latest = App\Models\GoldPriceHistory::where('source', 'btmh')
    ->where('unit', 'KGB')
    ->orderByDesc('recorded_at')
    ->first();

if ($latest) {
    echo "✅ Latest BTMH record:\n";
    echo "  Buy:       " . number_format($latest->buy_price) . "\n";
    echo "  Sell:      " . number_format($latest->sell_price) . "\n";
    echo "  Date:      " . $latest->price_date . "\n";
    echo "  Recorded:  " . $latest->recorded_at . "\n";
} else {
    echo "❌ No BTMH records found\n";
}

// Count
$total = App\Models\GoldPriceHistory::where('source', 'btmh')->count();
echo "\nTotal BTMH records: {$total}\n";

// Test history
$history = App\Models\GoldPriceHistory::getHistory('btmh', 'KGB', 30);
echo "History 30 days: " . $history->count() . " records\n";
