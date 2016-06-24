<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCommentGoodsPurchases extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('goods_purchases', function (Blueprint $table) {
            $table->unsignedInteger('status')->comment('1 - ок, 2 - фэйл')->change();
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
            $table->unsignedInteger('status')->change();
        });
    }
}
