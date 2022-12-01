<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Constants\DbConstant as cn;

class ChangeBeforeExamSurveyAfterExamSurveyColNullableInAttemptExamTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(){
        Schema::table(cn::ATTEMPT_EXAMS_TABLE_NAME, function (Blueprint $table) {
            DB::statement("ALTER TABLE ".cn::ATTEMPT_EXAMS_TABLE_NAME." CHANGE COLUMN ".cn::ATTEMPT_EXAMS_BEFORE_EXAM_SURVEY_COL." ".cn::ATTEMPT_EXAMS_BEFORE_EXAM_SURVEY_COL."  TINYINT comment '1-sad 2-Happy' NULL");
            DB::statement("ALTER TABLE ".cn::ATTEMPT_EXAMS_TABLE_NAME." CHANGE COLUMN ".cn::ATTEMPT_EXAMS_AFTER_EXAM_SURVEY_COL." ".cn::ATTEMPT_EXAMS_AFTER_EXAM_SURVEY_COL."  TINYINT comment '1-sad 2-Happy' NULL");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
       
    }
}
