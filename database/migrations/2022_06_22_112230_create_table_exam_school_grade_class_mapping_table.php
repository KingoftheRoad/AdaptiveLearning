<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Constants\DbConstant as cn;

class CreateTableExamSchoolGradeClassMappingTable extends Migration
{
  
    public function up(){
        Schema::create(cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_TABLE, function (Blueprint $table) {
            $table->bigIncrements(cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_ID_COL);
            $table->unsignedBigInteger(cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_EXAM_ID_COL)->nullable();
            $table->unsignedBigInteger(cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_SCHOOL_ID_COL)->nullable();
            $table->unsignedBigInteger(cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_GRADE_ID_COL)->nullable();
            $table->unsignedBigInteger(cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_CLASS_ID_COL)->nullable();
            $table->longText(cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_STUDENT_IDS_COL)->nullable();
            $table->dateTime(cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_START_DATE_COL);
            $table->dateTime(cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_END_DATE_COL);
            $table->time(cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_START_TIME_COL)->nullable();
            $table->time(cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_END_TIME_COL)->nullable();
            $table->enum(cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_STATUS_COL,['draft','publish','inactive'])->default('draft');
            $table->timestamps();
            $table->SoftDeletes();

            $table->foreign(cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_EXAM_ID_COL)->references(cn::EXAM_TABLE_ID_COLS)->on(cn::EXAM_TABLE_NAME)->onDelete(cn::DB_ENGINE_ONDELETE_NAME);
            $table->foreign(cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_SCHOOL_ID_COL)->references(cn::SCHOOL_ID_COLS)->on(cn::SCHOOL_TABLE_NAME)->onDelete(cn::DB_ENGINE_ONDELETE_NAME);
            $table->foreign(cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_GRADE_ID_COL)->references(cn::GRADES_ID_COL)->on(cn::GRADES_TABLE_NAME)->onDelete(cn::DB_ENGINE_ONDELETE_NAME);
            $table->foreign(cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_CLASS_ID_COL)->references(cn::GRADE_CLASS_MAPPING_ID_COL)->on(cn::GRADE_CLASS_MAPPING_TABLE_NAME)->onDelete(cn::DB_ENGINE_ONDELETE_NAME);
        });
    }

    public function down(){
        Schema::dropIfExists(cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_TABLE);
    }
}
