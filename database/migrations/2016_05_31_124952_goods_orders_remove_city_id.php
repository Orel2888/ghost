<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class GoodsOrdersRemoveCityId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('goods_orders', function (Blueprint $table) {
            $table->dropForeign(['city_id']);
            $table->dropColumn('city_id');
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
