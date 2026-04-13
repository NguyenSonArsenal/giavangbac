<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Artisan;

class TestController extends Controller
{
    public function test()
    {
        $a = Artisan::call('silver:generate-trend');
        dd($a);
    }
}
