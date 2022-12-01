<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Constants\DbConstant as cn;

class AddColumnNodeHintAnswerTable extends Migration
{
    
    public function up()
    {
        Schema::table(cn::ANSWER_TABLE_NAME, function (Blueprint $table) {
            $table->after(cn::ANSWER_HINT_ANSWER4_CH_COL, function($table){
                $table->longtext(cn::ANSWER_NODE_HINT_ANSWER1_EN_COL)->nullable();
                $table->longtext(cn::ANSWER_NODE_HINT_ANSWER2_EN_COL)->nullable();
                $table->longtext(cn::ANSWER_NODE_HINT_ANSWER3_EN_COL)->nullable();
                $table->longtext(cn::ANSWER_NODE_HINT_ANSWER4_EN_COL)->nullable();
                $table->longtext(cn::ANSWER_NODE_HINT_ANSWER1_CH_COL)->nullable();
                $table->longtext(cn::ANSWER_NODE_HINT_ANSWER2_CH_COL)->nullable();
                $table->longtext(cn::ANSWER_NODE_HINT_ANSWER3_CH_COL)->nullable();
                $table->longtext(cn::ANSWER_NODE_HINT_ANSWER4_CH_COL)->nullable();
            });
        });
    }

    public function down()
    {
        //
    }
}
