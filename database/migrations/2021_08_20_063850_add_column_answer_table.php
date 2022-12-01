<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Constants\DbConstant as cn;

class AddColumnAnswerTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table(cn::ANSWER_TABLE_NAME, function (Blueprint $table) {
            $table->after(cn::ANSWER_CORRECT_ANSWER_COL, function($table){
                $table->unsignedBigInteger(cn::ANSWER_CORRECT_ANSWER_EN_COL)->nullable();
                $table->unsignedBigInteger(cn::ANSWER_CORRECT_ANSWER_CH_COL)->nullable();
            });
            $table->dropColumn([cn::ANSWER_CORRECT_ANSWER_COL]);
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
