<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Constants\DbConstant as cn;



class CreateAnswerTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(cn::ANSWER_TABLE_NAME, function (Blueprint $table) {
            $table->id(cn::ANSWER_ID_COL);
            $table->bigInteger(cn::ANSWER_QUESTION_ID_COL);
            $table->longText(cn::ANSWER_ANSWER1_EN_COL);
            $table->longText(cn::ANSWER_ANSWER2_EN_COL);
            $table->longText(cn::ANSWER_ANSWER3_EN_COL);
            $table->longText(cn::ANSWER_ANSWER4_EN_COL);
            $table->longText(cn::ANSWER_HINT_ANSWER1_EN_COL);
            $table->longText(cn::ANSWER_HINT_ANSWER2_EN_COL);
            $table->longText(cn::ANSWER_HINT_ANSWER3_EN_COL);
            $table->longText(cn::ANSWER_HINT_ANSWER4_EN_COL);
            $table->longText(cn::ANSWER_ANSWER1_CH_COL);
            $table->longText(cn::ANSWER_ANSWER2_CH_COL);
            $table->longText(cn::ANSWER_ANSWER3_CH_COL);
            $table->longText(cn::ANSWER_ANSWER4_CH_COL);
            $table->longText(cn::ANSWER_HINT_ANSWER1_CH_COL);
            $table->longText(cn::ANSWER_HINT_ANSWER2_CH_COL);
            $table->longText(cn::ANSWER_HINT_ANSWER3_CH_COL);
            $table->longText(cn::ANSWER_HINT_ANSWER4_CH_COL);
            $table->bigInteger(cn::ANSWER_CORRECT_ANSWER_COL);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists(cn::ANSWER_TABLE_NAME);
    }
}
