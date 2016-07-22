<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class QiwiTransactionsAddColumnPurse extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('qiwi_transactions', function (Blueprint $table) {
            $table->unsignedBigInteger('purse')->after('qiwi_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('qiwi_transactions', function (Blueprint $table) {
            //
        });
    }
}
