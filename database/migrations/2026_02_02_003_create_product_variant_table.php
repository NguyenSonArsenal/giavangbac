<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductVariantTable extends Migration
{
    public function up()
    {
        Schema::create('product_variant', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_id')->comment('ID sản phẩm');
            
            // Thông tin variant
            $table->string('unit', 50)->comment('Đơn vị (Hộp, Vỉ, Chai, Gói...)');
            $table->string('quantity_per_unit', 100)->nullable()
                  ->comment('Số lượng/đơn vị (30 gói x 1.6g, 6 vỉ x 4 viên...)');
            
            // Giá cả
            $table->unsignedInteger('price')->default(0)->comment('Giá gốc (VNĐ)');
            $table->unsignedInteger('price_sale')->default(0)->comment('Giá sau giảm (VNĐ)');
            $table->tinyInteger('sale')->default(0)->comment('% giảm giá (0-100)');
            
            // Quản lý
            $table->integer('stock')->default(0)->comment('Số lượng tồn kho');
            $table->string('sku', 100)->nullable()->comment('Mã SKU riêng cho variant');
            $table->tinyInteger('status')->default(1)->comment('1: active, -1: inactive');
            $table->integer('sort')->default(0)->comment('Thứ tự hiển thị');
            
            $table->timestamps();
            $table->softDeletes();
        
            $table->index(['product_id', 'status']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('product_variant');
    }
}
