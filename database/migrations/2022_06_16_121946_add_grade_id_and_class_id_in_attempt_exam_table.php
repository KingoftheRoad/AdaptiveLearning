<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Constants\DbConstant as cn;

class AddGradeIdAndClassIdInAttemptExamTable extends Migration
{
    public function up(){
        Schema::table(cn::ATTEMPT_EXAMS_TABLE_NAME, function (Blueprint $table) {
            $table->after(cn::ATTEMPT_EXAMS_STUDENT_STUDENT_ID,function($table){
                $table->unsignedBigInteger(cn::ATTEMPT_EXAMS_STUDENT_GRADE_ID)->nullable();
                $table->unsignedBigInteger(cn::ATTEMPT_EXAMS_STUDENT_CLASS_ID)->nullable();
            });
            $table->foreign(cn::ATTEMPT_EXAMS_STUDENT_GRADE_ID)->references(cn::GRADES_ID_COL)->on(cn::GRADES_TABLE_NAME)->onDelete(cn::DB_ENGINE_ONDELETE_NAME);
            $table->foreign(cn::ATTEMPT_EXAMS_STUDENT_CLASS_ID)->references(cn::GRADE_CLASS_MAPPING_ID_COL)->on(cn::GRADE_CLASS_MAPPING_TABLE_NAME)->onDelete(cn::DB_ENGINE_ONDELETE_NAME);
        });
    }

    public function down(){
        Schema::table(cn::ATTEMPT_EXAMS_TABLE_NAME, function (Blueprint $table) {
            $table->dropForeign([cn::ATTEMPT_EXAMS_STUDENT_GRADE_ID]);
            $table->dropForeign([cn::ATTEMPT_EXAMS_STUDENT_CLASS_ID]);
            $table->dropColumn(cn::ATTEMPT_EXAMS_STUDENT_GRADE_ID);
            $table->dropColumn(cn::ATTEMPT_EXAMS_STUDENT_CLASS_ID);
        });
    }
}
