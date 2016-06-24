<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGoodsPrice extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('goods_price', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('good_id');
            $table->unsignedInteger('miner_id')->nullable();
            $table->decimal('weight', 4, 2);
            $table->string('address', 5000);
            $table->unsignedInteger('reserve');
            $table->unsignedInteger('cost');
            $table->timestamps();

            $table->foreign('good_id')
                ->references('id')->on('goods')
                ->onDelete('cascade');

            $table->foreign('miner_id')
                ->references('id')->on('miners')
                ->onDelete('set null');

            $table->index('weight');
            $table->index('cost');
            $table->index('reserve');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('goods_price');
    }
}
