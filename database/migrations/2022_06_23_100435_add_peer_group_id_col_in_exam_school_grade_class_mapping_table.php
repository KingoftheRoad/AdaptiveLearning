<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Constants\DbConstant as cn;

class AddPeerGroupIdColInExamSchoolGradeClassMappingTable extends Migration
{
    public function up(){
        Schema::table(cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_TABLE, function (Blueprint $table) {
            $table->after(cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_CLASS_ID_COL,function($table){
                $table->unsignedBigInteger(cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_PEER_GROUP_ID_COL)->nullable();

                $table->foreign(cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_PEER_GROUP_ID_COL)->references(cn::PEER_GROUP_ID_COL)->on(cn::PEER_GROUP_TABLE_NAME)->onDelete(cn::DB_ENGINE_ONDELETE_NAME);
            });
        });
    }

    public function down(){
        Schema::table(cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_TABLE, function (Blueprint $table) {
            $table->dropForeign([cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_PEER_GROUP_ID_COL]);
            $table->dropColumn(cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_PEER_GROUP_ID_COL);
        });
    }
}
