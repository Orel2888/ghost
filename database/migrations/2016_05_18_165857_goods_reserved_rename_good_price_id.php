<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class GoodsReservedRenameGoodPriceId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('goods_reserved', function (Blueprint $table) {
            $table->dropForeign(['good_price_id']);
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
        Schema::table('goods_reserved', function (Blueprint $table) {

        });
    }
}
