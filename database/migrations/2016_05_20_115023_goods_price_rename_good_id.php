<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class GoodsPriceRenameGoodId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('goods_price', function (Blueprint $table) {
            $table->dropForeign(['good_id']);
            $table->renameColumn('good_id', 'goods_id');

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
        Schema::table('goods_price', function (Blueprint $table) {
            //
        });
    }
}
