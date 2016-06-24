<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class GoodsPurchasesRenameGoodId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('goods_purchases', function (Blueprint $table) {
            $table->renameColumn('good_id', 'goods_id');
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
            //
        });
    }
}
