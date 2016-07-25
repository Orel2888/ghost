<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class GoodsOrdersChangeCommentStatus extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('goods_orders', function (Blueprint $table) {
            $table->unsignedInteger('status')->comment('0 - Не обработанный, 1 - Обработанный, 2 - Нет товара, 3 - Не достаточно средств')->change();
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
