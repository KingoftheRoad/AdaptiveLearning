<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Constants\DbConstant as cn;

class CreateTableAttemptExamStudentMapping extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(cn::ATTEMPT_EXAM_STUDENT_MAPPING_TABLE_NAME, function (Blueprint $table) {
            $table->bigIncrements(cn::ATTEMPT_EXAM_STUDENT_MAPPING_ID_COL);
            $table->unsignedBigInteger(cn::ATTEMPT_EXAM_STUDENT_MAPPING_EXAM_ID_COL)->nullable();
            $table->unsignedBigInteger(cn::ATTEMPT_EXAM_STUDENT_MAPPING_STUDENT_ID_COL)->nullable();
            $table->enum(cn::ATTEMPT_EXAM_STUDENT_MAPPING_STATUS_COL,[0,1])->default(1);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign(cn::ATTEMPT_EXAM_STUDENT_MAPPING_EXAM_ID_COL)->references(cn::EXAM_TABLE_ID_COLS)->on(cn::EXAM_TABLE_NAME)->onDelete(cn::DB_ENGINE_ONDELETE_NAME);
            $table->foreign(cn::ATTEMPT_EXAM_STUDENT_MAPPING_STUDENT_ID_COL)->references(cn::USERS_ID_COL)->on(cn::USERS_TABLE_NAME)->onDelete(cn::DB_ENGINE_ONDELETE_NAME);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table(cn::ATTEMPT_EXAM_STUDENT_MAPPING_TABLE_NAME, function (Blueprint $table) {
            $table->dropForeign([cn::ATTEMPT_EXAM_STUDENT_MAPPING_EXAM_ID_COL]);
            $table->dropColumn(cn::ATTEMPT_EXAM_STUDENT_MAPPING_STUDENT_ID_COL);
        });
        Schema::dropIfExists(cn::ATTEMPT_EXAM_STUDENT_MAPPING_TABLE_NAME);
    }
}
