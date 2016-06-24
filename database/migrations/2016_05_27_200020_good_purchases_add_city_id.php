<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class GoodPurchasesAddCityId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('goods_purchases', function (Blueprint $table) {
            $table->unsignedInteger('city_id')->after('id');

            $table->index('city_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('goods_purchases', function (Blueprint $table) {
            $table->removeColumn('city_id');
        });
    }
}
