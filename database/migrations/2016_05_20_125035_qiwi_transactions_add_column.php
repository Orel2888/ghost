<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class QiwiTransactionsAddColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('qiwi_transactions', function (Blueprint $table) {
            $table->unsignedInteger('status')->after('qiwi_date')->comment('0 - Не обработан, 1 - обработан');
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
            $table->dropColumn('status');
        });
    }
}
