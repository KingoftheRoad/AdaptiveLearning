<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Constants\DbConstant as cn;

class AddColumnIsTeachingReportSyncInExamTable extends Migration
{
    public function up(){
        Schema::table(cn::EXAM_TABLE_NAME, function (Blueprint $table) {
            $table->after(cn::EXAM_TABLE_IS_UNLIMITED,function($table){
                $table->enum(cn::EXAM_TABLE_IS_TEACHING_REPORT_SYNC,['true','false'])->default('true');
            });
        });
    }

    public function down(){
        Schema::table(cn::EXAM_TABLE_NAME, function (Blueprint $table) {
          $table->dropColumn(cn::EXAM_TABLE_IS_TEACHING_REPORT_SYNC);
        });
    }
}
