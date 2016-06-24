<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class GoodsOrdersRenameGoodsPriceIdAddWeight extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('goods_orders', function (Blueprint $table) {
            $table->dropForeign(['goods_price_id']);
            $table->renameColumn('goods_price_id', 'goods_id');
            $table->decimal('weight', 4, 2)->after('client_id');

            $table->foreign('goods_id')
                ->references('id')->on('goods')
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
