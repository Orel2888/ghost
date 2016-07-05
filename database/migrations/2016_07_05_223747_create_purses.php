<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePurses extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('purses', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('phone');
            $table->string('pass');
            $table->decimal('balance');
            $table->unsignedInteger('selected');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('purses');
    }
}
