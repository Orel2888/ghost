<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateQiwiTransactions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('qiwi_transactions', function (Blueprint $table) {
            $table->increments('id');
            $table->bigInteger('qiwi_id');
            $table->string('provider', 1000);
            $table->string('comment')->nullable();
            $table->decimal('amount');
            $table->unsignedInteger('type')->comment('1 - Входящий, 2 - исходящий');
            $table->timestamp('qiwi_date');
            $table->timestamps();

            $table->index('amount');
            $table->index('qiwi_date');
            $table->index('created_at');
            $table->index('type');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('qiwi_transactions');
    }
}
