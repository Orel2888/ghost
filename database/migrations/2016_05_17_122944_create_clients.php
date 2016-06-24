<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateClients extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('clients', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->unsignedInteger('tg_chaid');
            $table->string('tg_username')->nullable();
            $table->decimal('rating');
            $table->unsignedInteger('count_purchases');
            $table->string('comment')->nullable();
            $table->timestamps();

            $table->index('tg_username');
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
        Schema::drop('clients');
    }
}
