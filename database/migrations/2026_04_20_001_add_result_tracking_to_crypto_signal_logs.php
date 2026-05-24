<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Thêm các cột tracking kết quả vào bảng crypto_signal_logs
 * Mục đích: Thống kê tỷ lệ thắng/thua của tool theo thời gian thực
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('crypto_signal_logs', function (Blueprint $table) {
            // Giá mục tiêu & cắt lỗ (tính tại thời điểm tool báo)
            $table->decimal('target_price',    16, 4)->nullable()->after('score');
            $table->decimal('stop_loss_price', 16, 4)->nullable()->after('target_price');
            $table->decimal('target_pct',       8, 4)->default(1.0)->after('stop_loss_price');  // % chốt lời
            $table->decimal('stop_pct',         8, 4)->default(0.5)->after('target_pct');       // % cắt lỗ

            // Kết quả sau khi giá chạm target hoặc stop loss
            $table->enum('result', ['PENDING', 'WIN', 'LOSS', 'EXPIRED'])
                  ->default('PENDING')->after('stop_pct');
            $table->decimal('resolved_price', 16, 4)->nullable()->after('result');  // Giá lúc chốt
            $table->decimal('profit_pct',      8, 4)->nullable()->after('resolved_price'); // % lời/lỗ thực tế
            $table->timestamp('resolved_at')->nullable()->after('profit_pct');       // Thời điểm chốt

            $table->index('result');
            $table->index('resolved_at');
        });
    }

    public function down(): void
    {
        Schema::table('crypto_signal_logs', function (Blueprint $table) {
            $table->dropColumn([
                'target_price', 'stop_loss_price', 'target_pct', 'stop_pct',
                'result', 'resolved_price', 'profit_pct', 'resolved_at',
            ]);
        });
    }
};
