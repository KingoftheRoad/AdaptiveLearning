<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Constants\DbConstant as cn;

class AddColumnIsUnlimitedExamTable extends Migration
{
    
    public function up()
    {
        Schema::table(cn::EXAM_TABLE_NAME, function (Blueprint $table) {
                $table->after(cn::EXAM_TABLE_RESULT_DECLARE_COL, function($table){
                $table->boolean(cn::EXAM_TABLE_IS_UNLIMITED)->default('0');
            });
        });
    }

    public function down()
    {
        //
    }
}
