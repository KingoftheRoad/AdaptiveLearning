<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Constants\DbConstant as cn;

class AddColumnServerDetailsAttemptExamTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasColumn(cn::ATTEMPT_EXAMS_TABLE_NAME,cn::ATTEMPT_EXAMS_SERVER_DETAILS_COL)){
            Schema::table(cn::ATTEMPT_EXAMS_TABLE_NAME, function (Blueprint $table) {
                $table->dropColumn(cn::ATTEMPT_EXAMS_SERVER_DETAILS_COL);            
            });
        }

        Schema::table(cn::ATTEMPT_EXAMS_TABLE_NAME, function (Blueprint $table) {
            $table->after(cn::ATTEMPT_EXAMS_EXAM_TAKING_TIMING, function($table){
                $table->longtext(cn::ATTEMPT_EXAMS_SERVER_DETAILS_COL)->nullable();
            });
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
