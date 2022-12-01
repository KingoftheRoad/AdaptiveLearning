<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Constants\DbConstant as cn;

class AddBeforeExamSurveyAfterExamSurveyColInAttemptExamsTable extends Migration
{

    public function up(){
        Schema::table(cn::ATTEMPT_EXAMS_TABLE_NAME, function (Blueprint $table) {
            $table->after(cn::ATTEMPT_EXAMS_SERVER_DETAILS_COL, function($table){
                $table->tinyInteger(cn::ATTEMPT_EXAMS_BEFORE_EXAM_SURVEY_COL)->comment('1-sad 2-Happy');
                $table->tinyInteger(cn::ATTEMPT_EXAMS_AFTER_EXAM_SURVEY_COL)->comment('1-sad 2-Happy');
            });
        });
    }

    public function down(){
        Schema::table(cn::ATTEMPT_EXAMS_TABLE_NAME, function (Blueprint $table) {
            $table->dropColumn(cn::ATTEMPT_EXAMS_BEFORE_EXAM_SURVEY_COL);
            $table->dropColumn(cn::ATTEMPT_EXAMS_AFTER_EXAM_SURVEY_COL);
        });
    }
}
