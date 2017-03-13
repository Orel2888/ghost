<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class QiwiTransactionsAddBl extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('qiwi_transactions', function (Blueprint $table) {
            //
            $table->unsignedInteger('bl')->comment('Юзер в черном списке 0-нет, 1-да')->after('status');
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
            $table->dropColumn('bl');
        });
    }
}
