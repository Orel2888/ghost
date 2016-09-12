<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMinerPayments extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('miner_payments', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('miner_id');
            $table->unsignedInteger('amount');
            $table->unsignedInteger('counter_goods_ok');
            $table->unsignedInteger('counter_goods_fail');
            $table->unsignedInteger('status')->comment('0 - в обработке, 1 - выплачено');
            $table->timestamps();

            $table->foreign('miner_id')
                ->references('id')->on('miners')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('miner_payments');
    }
}
