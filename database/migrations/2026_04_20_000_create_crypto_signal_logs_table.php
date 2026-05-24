<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('crypto_signal_logs', function (Blueprint $table) {
            $table->id();
            $table->string('symbol', 20);           // Ví dụ: BNBUSDT, BTCUSDT
            $table->string('interval', 10)->default('15m'); // Khung nến
            $table->decimal('price', 16, 4);        // Giá hiện tại
            $table->decimal('ma7',   16, 4)->nullable();
            $table->decimal('ma25',  16, 4)->nullable();
            $table->decimal('ma99',  16, 4)->nullable();
            $table->decimal('rsi',   8, 2)->nullable();
            $table->decimal('volume_current', 20, 4)->nullable();
            $table->decimal('volume_avg',     20, 4)->nullable();
            $table->integer('score')->default(0);   // Điểm tín hiệu (0–10)
            $table->string('signal_type', 30);      // STRONG_BUY | WATCH | NO_SIGNAL
            $table->text('reasons')->nullable();     // JSON: các lý do nên mua
            $table->text('warnings')->nullable();    // JSON: cảnh báo
            $table->timestamp('scanned_at');         // Thời điểm quét
            $table->timestamps();

            $table->index(['symbol', 'scanned_at']);
            $table->index('signal_type');
            $table->index('score');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('crypto_signal_logs');
    }
};
