<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Constants\DbConstant as cn;

class RenameColumnExamTableTotalDurationToTotalNoSecond extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table(cn::EXAM_TABLE_NAME, function (Blueprint $table) {
            $table->Integer(cn::EXAM_TABLE_TIME_DURATIONS_COLS)->comment("total_no_seconds")->change(); 
        });
    }

    
    
    public function down()
    {
       
    }
}
