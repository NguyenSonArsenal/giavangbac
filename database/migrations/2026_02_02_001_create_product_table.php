<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductTable extends Migration
{
    public function up()
    {
        Schema::create('product', function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('Tên sản phẩm');
            $table->string('slug')->unique()->comment('Slug SEO, duy nhất');
            $table->decimal('price', 12, 2)->default(0)->comment('Giá gốc');
            $table->tinyInteger('sale')->default(0)->comment('% giảm giá');
            $table->decimal('price_sale', 12, 2)->default(0)->comment('Giá sau giảm');
            $table->text('content')->nullable()->comment('Mô tả chi tiết sản phẩm');
            $table->unsignedBigInteger('category_id')->comment('Liên kết đến bảng categories');
            $table->string('image')->nullable()->comment('Ảnh đại diện (thumbnail)');
            $table->tinyInteger('status')->default(1)->comment('Trạng thái (1: active, -1: inactive)');

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('product');
    }
}
