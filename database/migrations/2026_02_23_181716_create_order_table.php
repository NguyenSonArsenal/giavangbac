<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order', function (Blueprint $table) {
            $table->id();
            $table->string('code', 20)->unique()->comment('Mã đơn hàng: DH-XXXXXX');
            $table->string('customer_name', 100);
            $table->string('customer_phone', 20);
            $table->string('customer_email', 100)->nullable();
            $table->text('customer_address');
            $table->text('note')->nullable();
            $table->unsignedBigInteger('total_amount');
            // 0: Chờ xác nhận | 1: Đã xác nhận | 2: Đang giao | 3: Hoàn thành | 4: Huỷ
            $table->tinyInteger('status')->default(0);
            $table->timestamps();
        });

        Schema::create('order_item', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('order')->onDelete('cascade');
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('variant_id')->nullable();
            $table->string('product_name', 255);
            $table->string('unit', 50)->default('Sản phẩm');
            $table->unsignedBigInteger('price');
            $table->integer('qty');
            $table->unsignedBigInteger('subtotal');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_item');
        Schema::dropIfExists('order');
    }
};
