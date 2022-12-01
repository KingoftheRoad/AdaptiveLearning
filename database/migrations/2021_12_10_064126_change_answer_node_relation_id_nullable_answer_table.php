<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Constants\DbConstant as cn;

class ChangeAnswerNodeRelationIdNullableAnswerTable extends Migration
{
    
    public function up()
    {
        Schema::table(cn::ANSWER_TABLE_NAME, function ($table) {
            $table->bigInteger(cn::ANSWER1_NODE_RELATION_ID_EN_COL)->nullable()->change();
            $table->bigInteger(cn::ANSWER2_NODE_RELATION_ID_EN_COL)->nullable()->change();
            $table->bigInteger(cn::ANSWER3_NODE_RELATION_ID_EN_COL)->nullable()->change();
            $table->bigInteger(cn::ANSWER4_NODE_RELATION_ID_EN_COL)->nullable()->change();
            $table->bigInteger(cn::ANSWER1_NODE_RELATION_ID_CH_COL)->nullable()->change();
            $table->bigInteger(cn::ANSWER2_NODE_RELATION_ID_CH_COL)->nullable()->change();
            $table->bigInteger(cn::ANSWER3_NODE_RELATION_ID_CH_COL)->nullable()->change();
            $table->bigInteger(cn::ANSWER4_NODE_RELATION_ID_CH_COL)->nullable()->change();
        });
    }

    public function down()
    {
        //
    }
}
