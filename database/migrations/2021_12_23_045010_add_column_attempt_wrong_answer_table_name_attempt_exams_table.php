<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Constants\DbConstant as cn;

class AddColumnAttemptWrongAnswerTableNameAttemptExamsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table(cn::ATTEMPT_EXAMS_TABLE_NAME,function(Blueprint $table){
            $table->after(cn::ATTEMPT_EXAMS_QUESTION_ANSWER_COL,function($table){
                $table->longText(cn::ATTEMPT_EXAMS_WRONG_ANSWER_COL)->nullable();
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
