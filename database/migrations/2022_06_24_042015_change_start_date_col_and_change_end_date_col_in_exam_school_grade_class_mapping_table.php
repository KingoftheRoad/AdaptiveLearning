<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Constants\DbConstant as cn;

class ChangeStartDateColAndChangeEndDateColInExamSchoolGradeClassMappingTable extends Migration
{
    public function up(){
        Schema::table(cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_TABLE, function (Blueprint $table) {
            $table->date(cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_START_DATE_COL)->change()->nullable();
            $table->date(cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_END_DATE_COL)->change()->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table(cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_TABLE, function (Blueprint $table) {
            $table->dateTime(cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_START_DATE_COL)->nullable();
            $table->dateTime(cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_END_DATE_COL)->nullable();
        });
    }
}
