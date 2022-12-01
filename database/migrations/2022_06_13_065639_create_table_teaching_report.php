<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Constants\DbConstant as cn;

class CreateTableTeachingReport extends Migration
{
    public function up(){
        Schema::create(cn::TEACHING_REPORT_TABLE, function (Blueprint $table) {
            $table->engine = cn::DB_ENGINE_NAME;
            $table->bigIncrements(cn::TEACHING_REPORT_ID_COL);
            $table->enum(cn::TEACHING_REPORT_REPORT_TYPE_COL,['assignment_test','self_learning'])->nullable()->comment('Assignment/Test,Self Learning');
            $table->tinyInteger(cn::TEACHING_REPORT_STUDY_TYPE_COL)->comment('1: Exercise; 2: Test;');
            $table->unsignedBigInteger(cn::TEACHING_REPORT_SCHOOL_ID_COL)->nullable();
            $table->unsignedBigInteger(cn::TEACHING_REPORT_EXAM_ID_COL)->nullable();
            $table->unsignedBigInteger(cn::TEACHING_REPORT_GRADE_ID_COL)->nullable();
            $table->unsignedBigInteger(cn::TEACHING_REPORT_CLASS_ID_COL)->nullable();
            $table->longText(cn::TEACHING_REPORT_GRADE_WITH_CLASS_COL)->nullable();
            $table->BigInteger(cn::TEACHING_TABLE_NO_OF_STUDENTS_COL)->nullable();
            $table->longText(cn::TEACHING_REPORT_STUDENT_PROGRESS_COL)->nullable();
            $table->longText(cn::TEACHING_REPORT_AVERAGE_ACCURACY_COL)->nullable();
            $table->longText(cn::TEACHING_REPORT_STUDY_STATUS_COL)->nullable();
            $table->longText(cn::TEACHING_REPORT_QUESTIONS_DIFFICULTIES_COL)->nullable();
            $table->dateTime(cn::TEACHING_REPORT_DATE_AND_TIME_COL);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign(cn::TEACHING_REPORT_SCHOOL_ID_COL)->references(cn::SCHOOL_ID_COLS)->on(cn::SCHOOL_TABLE_NAME)->onDelete(cn::DB_ENGINE_ONDELETE_NAME);
            $table->foreign(cn::TEACHING_REPORT_EXAM_ID_COL)->references(cn::EXAM_TABLE_ID_COLS)->on(cn::EXAM_TABLE_NAME)->onDelete(cn::DB_ENGINE_ONDELETE_NAME);
            $table->foreign(cn::TEACHING_REPORT_GRADE_ID_COL)->references(cn::GRADES_ID_COL)->on(cn::GRADES_TABLE_NAME)->onDelete(cn::DB_ENGINE_ONDELETE_NAME);
            $table->foreign(cn::TEACHING_REPORT_CLASS_ID_COL)->references(cn::GRADE_CLASS_MAPPING_ID_COL)->on(cn::GRADE_CLASS_MAPPING_TABLE_NAME)->onDelete(cn::DB_ENGINE_ONDELETE_NAME);
        });
    }

    public function down(){
        Schema::dropIfExists(cn::TEACHING_REPORT_TABLE);
    }
}
