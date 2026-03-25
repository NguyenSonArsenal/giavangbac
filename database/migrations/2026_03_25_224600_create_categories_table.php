<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCategoriesTable extends Migration
{
    public function up()
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('tên category');
            $table->string('slug')->unique()->comment('URL SEO');
            $table->text('description')->nullable()->comment('mô tả hiển thị trang category');
            $table->tinyInteger('status')->default(1)->comment('1: active, -1: inactive');
            $table->string('meta_title')->nullable()->comment('title Google');
            $table->text('meta_description')->nullable()->comment('description Google');
            $table->string('thumbnail')->nullable()->comment('ảnh banner category');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('categories');
    }
}
