<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductImageTable extends Migration
{
    public function up()
    {
        Schema::create('product_image', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_id')->comment('Liên kết đến sản phẩm');
            $table->string('path')->comment('Đường dẫn ảnh');
            $table->integer('sort')->default(0);
            $table->tinyInteger('is_main')->default(-1)->comment('1: ảnh chính, -1: ảnh phụ');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('product_image');
    }
}
