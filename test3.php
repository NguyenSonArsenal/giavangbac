<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$req = Illuminate\Http\Request::create('/api/gold/btmc/history?days=7&type=NHAN_TRON', 'GET');
$req->merge(['days' => 7, 'type' => 'NHAN_TRON']);
$c = new \App\Http\Controllers\Api\BtmcGoldController();
$res = $c->history($req);
echo $res->content();
