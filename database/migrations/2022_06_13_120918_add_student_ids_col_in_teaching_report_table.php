<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Constants\DbConstant as cn;

class AddStudentIdsColInTeachingReportTable extends Migration
{

    public function up(){
        Schema::table(cn::TEACHING_REPORT_TABLE, function (Blueprint $table) {
            $table->after(cn::TEACHING_REPORT_GRADE_WITH_CLASS_COL,function($table){
                $table->longText(cn::TEACHING_REPORT_STUDENT_IDS_COL)->nullable();
            });
        });
    }

    public function down(){
        Schema::table(cn::TEACHING_REPORT_TABLE, function (Blueprint $table) {
            //
        });
    }
}
