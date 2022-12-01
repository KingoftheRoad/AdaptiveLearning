<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Constants\DbConstant as cn;

class AddColumnLanugageAttemptExamTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasColumn(cn::ATTEMPT_EXAMS_TABLE_NAME,cn::ATTEMPT_EXAMS_LANGUAGE_COL)){
            Schema::table(cn::ATTEMPT_EXAMS_TABLE_NAME, function (Blueprint $table) {
                $table->dropColumn(cn::ATTEMPT_EXAMS_LANGUAGE_COL);            
            });
        }

        Schema::table(cn::ATTEMPT_EXAMS_TABLE_NAME, function (Blueprint $table) {
            $table->after(cn::ATTEMPT_EXAMS_STUDENT_STUDENT_ID, function($table){
                $table->enum(cn::ATTEMPT_EXAMS_LANGUAGE_COL, ['en', 'ch']);
                $table->unsignedBigInteger(cn::ATTEMPT_EXAMS_TOTAL_CORRECT_ANSWERS)->nullable()->change();
                $table->unsignedBigInteger(cn::ATTEMPT_EXAMS_TOTAL_WRONG_ANSWERS)->nullable()->change();
                $table->unsignedBigInteger(cn::ATTEMPT_EXAMS_EXAM_TAKING_TIMING)->nullable()->change();
                $table->boolean(cn::ATTEMPT_EXAMS_STATUS_COL)->default(1)->comment('1 = Active')->change();
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
