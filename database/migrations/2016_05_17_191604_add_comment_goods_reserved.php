<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCommentGoodsReserved extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('goods_reserved', function (Blueprint $table) {
            $table->unsignedInteger('status')->comment('0 - Не обработан, 1 - обработан')->change();
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
            $table->unsignedInteger('status')->change();
        });
    }
}
