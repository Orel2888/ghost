<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class GoodsOrdersAddColumnCost extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('goods_orders', function (Blueprint $table) {
            //
            $table->unsignedInteger('cost')->after('weight');
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
