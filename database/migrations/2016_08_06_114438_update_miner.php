<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateMiner extends Migration
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
            $table->renameColumn('count_goods', 'counter_goods');
            $table->renameColumn('count_goods_ok', 'counter_goods_ok');
            $table->renameColumn('count_goods_fail', 'counter_goods_fail');
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
