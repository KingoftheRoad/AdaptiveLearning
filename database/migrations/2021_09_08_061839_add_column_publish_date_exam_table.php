<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Constants\DbConstant as cn;

class AddColumnPublishDateExamTable extends Migration
{
    public function up()
    {
        Schema::table(cn::EXAM_TABLE_NAME, function (Blueprint $table) {
            $table->after(cn::EXAM_TABLE_RESULT_DATE_COLS, function($table){
                $table->timestamp(cn::EXAM_TABLE_PUBLISH_DATE_COL)->nullable();
            });
        });
    }

    public function down()
    {
        //
    }
}
