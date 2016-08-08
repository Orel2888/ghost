<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class MinerAddColumns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('miners', function (Blueprint $table) {
            //
            $table->unsignedInteger('counter_total_goods')->after('counter_goods_fail');
            $table->unsignedInteger('balance')->after('ante');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('miners', function (Blueprint $table) {
            //
        });
    }
}
