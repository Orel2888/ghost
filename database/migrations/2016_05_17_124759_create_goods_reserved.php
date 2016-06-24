<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGoodsReserved extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('goods_reserved', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('good_price_id');
            $table->unsignedInteger('client_id');
            $table->timestamp('time');
            $table->unsignedInteger('status');
            $table->timestamps();

            $table->foreign('good_price_id')
                ->references('id')->on('goods_price')
                ->onDelete('cascade');

            $table->index('client_id');
            $table->index('time');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('goods_reserved');
    }
}
