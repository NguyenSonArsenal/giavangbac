<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('comments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('post_id');
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->string('name');
            $table->string('email');
            $table->text('body');
            $table->boolean('is_admin')->default(false);
            $table->tinyInteger('status')->default(1)->comment('1: approved, -1: pending');
            $table->timestamps();
            $table->softDeletes();

            $table->index('post_id');
            $table->index('parent_id');
            $table->index('status');
            $table->index('created_at');

            $table->foreign('post_id')->references('id')->on('posts')->onDelete('cascade');
            $table->foreign('parent_id')->references('id')->on('comments')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('comments');
    }
};
