<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Constants\DbConstant as cn;

class AddColumnAssignSchoolStatusInExamTable extends Migration
{
    public function up(){
        Schema::table(cn::EXAM_TABLE_NAME, function (Blueprint $table) {
            $table->after(cn::EXAM_TABLE_CREATED_BY_COL,function($table){
                $table->enum(cn::EXAM_TABLE_ASSIGN_SCHOOL_STATUS,['draft','send_to_school'])->default('draft');
            });
        });
    }

    public function down(){
        Schema::table(cn::EXAM_TABLE_NAME, function (Blueprint $table) {
            $table->dropColumn(cn::EXAM_TABLE_ASSIGN_SCHOOL_STATUS);
        });
    }
}
