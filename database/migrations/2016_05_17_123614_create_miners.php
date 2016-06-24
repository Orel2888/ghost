<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMiners extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('miners', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->unsignedInteger('ante');
            $table->unsignedInteger('count_goods');
            $table->unsignedInteger('count_goods_ok');
            $table->unsignedInteger('count_goods_fail');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('miners');
    }
}
