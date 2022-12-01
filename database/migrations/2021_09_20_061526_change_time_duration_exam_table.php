<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Constants\DbConstant as cn;

class ChangeTimeDurationExamTable extends Migration
{
    public function up()
    {
        Schema::table(cn::EXAM_TABLE_NAME, function ($table) {
            $table->Integer(cn::EXAM_TABLE_TIME_DURATIONS_COLS)->nullable()->change();
        });
    }

    public function down()
    {
        //
    }
}
