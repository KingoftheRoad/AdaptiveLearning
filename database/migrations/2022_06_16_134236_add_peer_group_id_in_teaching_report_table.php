<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Constants\DbConstant as cn;

class AddPeerGroupIdInTeachingReportTable extends Migration
{
    public function up(){
        Schema::table(cn::TEACHING_REPORT_TABLE, function (Blueprint $table) {
            $table->after(cn::TEACHING_REPORT_CLASS_ID_COL,function($table){
                $table->unsignedBigInteger(cn::TEACHING_REPORT_PEER_GROUP_ID)->nullable();
            });
            $table->foreign(cn::TEACHING_REPORT_PEER_GROUP_ID)->references(cn::PEER_GROUP_ID_COL)->on(cn::PEER_GROUP_TABLE_NAME)->onDelete(cn::DB_ENGINE_ONDELETE_NAME);
        });
    }

    public function down(){
        Schema::table(cn::TEACHING_REPORT_TABLE, function (Blueprint $table) {
            $table->dropForeign([cn::TEACHING_REPORT_PEER_GROUP_ID]);
            $table->dropColumn(cn::ATTEMPT_EXAMS_STUDENT_GRADE_ID);
        });
    }
}
