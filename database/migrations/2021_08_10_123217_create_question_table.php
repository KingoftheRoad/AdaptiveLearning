<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Constants\DbConstant as cn;
use Illuminate\Support\Facades\Artisan;

class CreateQuestionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(cn::QUESTION_TABLE_NAME, function (Blueprint $table) {
            $table->bigIncrements(cn::QUESTION_TABLE_ID_COL);
            $table->String(cn::QUESTION_QUESTION_CODE_COL);
            $table->String(cn::QUESTION_QUESTION_UNIQUE_CODE_COL);
            $table->Integer(cn::QUESTION_CLASS_ID_COL);
            $table->Integer(cn::QUESTION_BANK_SECTION_ID_COL);
            $table->Integer(cn::QUESTION_BANK_UPDATED_BY_COL);
            $table->Integer(cn::QUESTION_BANK_SCHOOL_ID_COL);
            $table->longText(cn::QUESTION_QUESTION_EN_COL);
            $table->longText(cn::QUESTION_QUESTION_CH_COL);
            $table->String(cn::QUESTION_QUESTION_TYPE_COL);
            $table->Integer(cn::QUESTION_DIFFICULTY_LEVEL_COL);
            $table->boolean(cn::QUESTINON_STATUS_COL);
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
        Schema::dropIfExists(cn::QUESTION_TABLE_NAME);
    }
}
