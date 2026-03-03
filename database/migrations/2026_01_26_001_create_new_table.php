<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNewTable extends Migration
{
    public function up()
    {
        Schema::create('new', function (Blueprint $table) {
            $table->id();
            $table->string('title')->comment('Tiêu đề bài viết');
            $table->string('slug')->unique()->comment('Slug SEO, duy nhất');
            $table->string('des',255)->nullable()->comment('Mô tả ngắn');
            $table->longText('content')->comment('Nội dung HTML từ editor');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('new');
    }
}
