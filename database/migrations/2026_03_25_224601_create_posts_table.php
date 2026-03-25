<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePostsTable extends Migration
{
    public function up()
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->string('title')->comment('tiêu đề bài');
            $table->string('slug')->unique()->comment('URL SEO');
            $table->text('excerpt')->nullable()->comment('mô tả ngắn Preview');
            $table->longText('content')->nullable()->comment('nội dung');
            $table->string('thumbnail')->nullable()->comment('ảnh bài viết');
            $table->unsignedBigInteger('category_id')->nullable()->comment('thuộc category nào');
            $table->string('meta_title')->nullable()->comment('title Google');
            $table->text('meta_description')->nullable()->comment('description Google');
            $table->unsignedInteger('view_count')->default(0)->comment('đếm lượt xem');
            $table->boolean('is_featured')->default(false)->comment('bài nổi bật homepage');
            $table->tinyInteger('status')->default(1)->comment('1: active, -1: inactive');
            $table->timestamp('published_at')->nullable()->comment('ngày publish');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('posts');
    }
}
