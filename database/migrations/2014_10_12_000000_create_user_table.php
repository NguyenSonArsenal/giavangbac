<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user', function (Blueprint $table) {
            $table->id();
            $table->string('username');
            $table->string('email')->unique()->comment('Địa chỉ email');;
            $table->string('phone')->unique()->nullable();
            $table->string('password');
            $table->tinyInteger('gender')->nullable()->default(null)->comment('1 boy, 2 girl');
            $table->string('address')->nullable()->comment('Địa chỉ');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user');
    }
}
