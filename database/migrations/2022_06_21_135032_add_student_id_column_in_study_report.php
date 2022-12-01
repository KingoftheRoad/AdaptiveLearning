<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Constants\DbConstant as cn;

class AddStudentIdColumnInStudyReport extends Migration
{

    public function up()
    {
        Schema::table(cn::STUDY_REPORT_TABLE, function (Blueprint $table) {
            $table->after(cn::STUDY_REPORT_EXAM_ID_COL,function($table){
                $table->unsignedBigInteger(cn::STUDY_REPORT_STUDENT_ID_COL)->nullable();
            });
            $table->foreign(cn::STUDY_REPORT_STUDENT_ID_COL)->references(cn::USERS_ID_COL)->on(cn::USERS_TABLE_NAME)->onDelete(cn::DB_ENGINE_ONDELETE_NAME);
        });
    }
    
    public function down()
    {
        Schema::table(cn::STUDY_REPORT_TABLE, function (Blueprint $table) {
            $table->dropForeign([cn::STUDY_REPORT_STUDENT_ID_COL]);
            $table->dropColumn(cn::STUDY_REPORT_STUDENT_ID_COL);
        });
    }
}
