<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGoodsOrders extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('goods_orders', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('good_price_id');
            $table->unsignedInteger('client_id');
            $table->string('comment', 100);
            $table->unsignedInteger('status')->comment('0 - не обработанный, 1 - обработанный');
            $table->timestamps();

            $table->foreign('good_price_id')
                ->references('id')->on('goods_price')
                ->onDelete('cascade');

            $table->foreign('client_id')
                ->references('id')->on('clients')
                ->onDelete('cascade');

            $table->index('status');
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
        Schema::drop('goods_orders');
    }
}
