<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateContactTable extends Migration
{
    public function up()
    {
        Schema::create('contact', function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('Họ và tên');
            $table->string('email')->comment('Email liên hệ');
            $table->string('phone')->comment('Số điện thoại');
            $table->string('subject')->nullable()->comment('Tiêu đề');
            $table->text('message')->comment('Nội dung liên hệ');
            $table->tinyInteger('status')->default(0)->comment('-1: chưa đọc, 1: đã đọc');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('contact');
    }
}
