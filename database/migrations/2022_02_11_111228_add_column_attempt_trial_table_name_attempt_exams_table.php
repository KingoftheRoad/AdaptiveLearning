<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Constants\DbConstant as cn;

class AddColumnAttemptTrialTableNameAttemptExamsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table(cn::ATTEMPT_EXAMS_TABLE_NAME, function (Blueprint $table) {
            $table->after(cn::ATTEMPT_EXAMS_QUESTION_ANSWER_COL,function($table){
                $table->longText(cn::ATTEMPT_EXAMS_ATTEMPT_FIRST_TRIAL_COL)->nullable();
                $table->longText(cn::ATTEMPT_EXAMS_ATTEMPT_SECOND_TRIAL_COL)->nullable();
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
        Schema::table(cn::ATTEMPT_EXAMS_TABLE_NAME, function (Blueprint $table) {
            //
        });
    }
}
