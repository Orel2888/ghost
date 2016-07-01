<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class GoodsOrdersRenameGoodId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('goods_orders', function (Blueprint $table) {
            $table->dropForeign('goods_orders_good_price_id_foreign');
            $table->renameColumn('good_price_id', 'goods_price_id');

            $table->foreign('goods_price_id')
                ->references('id')->on('goods_price')
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
        Schema::table('goods_orders', function (Blueprint $table) {
            //
        });
    }
}
