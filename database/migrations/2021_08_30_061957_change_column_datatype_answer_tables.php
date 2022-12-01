<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Constants\DbConstant as cn;

class ChangeColumnDatatypeAnswerTables extends Migration
{
    public function up()
    {
        Schema::table(cn::ANSWER_TABLE_NAME, function (Blueprint $table) {
            $table->longText(cn::ANSWER_ANSWER3_EN_COL)->nullable()->change();
            $table->longText(cn::ANSWER_ANSWER4_EN_COL)->nullable()->change();
            $table->longText(cn::ANSWER_ANSWER3_CH_COL)->nullable()->change();
            $table->longText(cn::ANSWER_ANSWER4_CH_COL)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
