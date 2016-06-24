<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGoodsPurchases extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('goods_purchases', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('good_id');
            $table->unsignedInteger('miner_id');
            $table->unsignedInteger('client_id');
            $table->decimal('weight', 4, 2);
            $table->string('address', 5000);
            $table->unsignedInteger('cost');
            $table->unsignedInteger('status')->default(1);
            $table->timestamps();

            $table->index('good_id');
            $table->index('miner_id');
            $table->index('client_id');
            $table->index('cost');
            $table->index('status');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('goods_purchases');
    }
}
