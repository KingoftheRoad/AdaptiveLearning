<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Constants\DbConstant as cn;

class CreateAttemptExamsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(cn::ATTEMPT_EXAMS_TABLE_NAME, function (Blueprint $table) {
            $table->engine = cn::DB_ENGINE_NAME;
            $table->bigIncrements(cn::ATTEMPT_EXAMS_ID_COL);
            $table->unsignedBigInteger(cn::ATTEMPT_EXAMS_EXAM_ID);
            $table->unsignedBigInteger(cn::ATTEMPT_EXAMS_STUDENT_STUDENT_ID);
            $table->longText(cn::ATTEMPT_EXAMS_QUESTION_ANSWER_COL);
            $table->unsignedBigInteger(cn::ATTEMPT_EXAMS_TOTAL_CORRECT_ANSWERS);
            $table->unsignedBigInteger(cn::ATTEMPT_EXAMS_TOTAL_WRONG_ANSWERS);
            $table->unsignedBigInteger(cn::ATTEMPT_EXAMS_EXAM_TAKING_TIMING);
            $table->boolean(cn::ATTEMPT_EXAMS_STATUS_COL);
            $table->timestamps();

            $table->foreign(cn::ATTEMPT_EXAMS_EXAM_ID)->references(cn::EXAM_TABLE_ID_COLS)->on(cn::EXAM_TABLE_NAME)->onDelete(cn::DB_ENGINE_ONDELETE_NAME);
            $table->foreign(cn::ATTEMPT_EXAMS_STUDENT_STUDENT_ID)->references(cn::USERS_ID_COL)->on(cn::USERS_TABLE_NAME)->onDelete(cn::DB_ENGINE_ONDELETE_NAME);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists(cn::ATTEMPT_EXAMS_TABLE_NAME);
    }
}
