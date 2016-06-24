<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class GoodsPriceAddCityId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('goods_price', function (Blueprint $table) {
            $table->unsignedInteger('city_id')->after('id');

            $table->foreign('city_id')
                ->references('id')->on('citys')
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
        Schema::table('goods_price', function (Blueprint $table) {
            $table->removeColumn('city_id');
        });
    }
}
