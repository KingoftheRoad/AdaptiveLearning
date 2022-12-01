<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Constants\DbConstant as cn;

class ModifyCreditPointTypeInUserCreditPointHistoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table(cn::USER_CREDIT_POINT_HISTORY, function (Blueprint $table) {
            \DB::statement("ALTER TABLE `".cn::USER_CREDIT_POINT_HISTORY."` CHANGE `".cn::USER_CREDIT_POINT_HISTORY_CREDIT_POINT_TYPE_COL."` `".cn::USER_CREDIT_POINT_HISTORY_CREDIT_POINT_TYPE_COL."` ENUM('after_submission','accuracy_level','ability_level','manual_credit_point') default NULL;");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table(cn::USER_CREDIT_POINT_HISTORY, function (Blueprint $table) {
            //
        });
    }
}
