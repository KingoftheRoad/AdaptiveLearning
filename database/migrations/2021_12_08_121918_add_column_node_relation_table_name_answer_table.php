<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Constants\DbConstant as cn;

class AddColumnNodeRelationTableNameAnswerTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table(cn::ANSWER_TABLE_NAME, function (Blueprint $table) {
            $table->after(cn::ANSWER_NODE_HINT_ANSWER4_CH_COL, function($table){
                $table->bigInteger(cn::ANSWER1_NODE_RELATION_ID_EN_COL)->nullable()->default(0);
                $table->bigInteger(cn::ANSWER2_NODE_RELATION_ID_EN_COL)->nullable()->default(0);
                $table->bigInteger(cn::ANSWER3_NODE_RELATION_ID_EN_COL)->nullable()->default(0);
                $table->bigInteger(cn::ANSWER4_NODE_RELATION_ID_EN_COL)->nullable()->default(0);
                $table->bigInteger(cn::ANSWER1_NODE_RELATION_ID_CH_COL)->nullable()->default(0);
                $table->bigInteger(cn::ANSWER2_NODE_RELATION_ID_CH_COL)->nullable()->default(0);
                $table->bigInteger(cn::ANSWER3_NODE_RELATION_ID_CH_COL)->nullable()->default(0);
                $table->bigInteger(cn::ANSWER4_NODE_RELATION_ID_CH_COL)->nullable()->default(0);
            });
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table(cn::ANSWER_TABLE_NAME, function (Blueprint $table) {
            //
        });
    }
}
