<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBlackListUsers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('black_list_users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('username')->nullable();
            $table->bigInteger('phone')->nullable();
            $table->enum('type', ['username', 'phone']);
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
        Schema::dropIfExists('black_list_users');
    }
}
