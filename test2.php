<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$count = \App\Models\GoldPriceHistory::count();
echo "Total Gold: $count\n";

$rows = \Illuminate\Support\Facades\DB::table('metal_prices')->orderByDesc('id')->limit(5)->get();
foreach ($rows as $r) {
    echo "ID: {$r->id}, metal_type: {$r->metal_type}, source: {$r->source}, unit: {$r->unit}\n";
}
