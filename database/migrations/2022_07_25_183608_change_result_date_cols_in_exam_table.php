<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Constants\DbConstant as cn;

class ChangeResultDateColsInExamTable extends Migration
{
    public function up(){
        Schema::table(cn::EXAM_TABLE_NAME, function (Blueprint $table) {
            $table->date(cn::EXAM_TABLE_RESULT_DATE_COLS)->change()->nullable();
        });
    }

    
    public function down(){
        Schema::table(cn::EXAM_TABLE_NAME, function (Blueprint $table) {
            $table->date(cn::EXAM_TABLE_RESULT_DATE_COLS)->nullable();
        });
    }
}
