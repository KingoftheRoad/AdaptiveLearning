<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Constants\DbConstant as cn;

class CreateExamConfigurationsDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(cn::EXAM_CONFIGURATIONS_DETAILS_TABLE_NAME, function (Blueprint $table) {
            $table->bigIncrements(cn::EXAM_CONFIGURATIONS_DETAILS_ID_COL);
            $table->unsignedBigInteger(cn::EXAM_CONFIGURATIONS_DETAILS_EXAM_ID_COL);
            $table->unsignedBigInteger(cn::EXAM_CONFIGURATIONS_DETAILS_CREATED_BY_USER_ID_COL)->nullable();
            $table->longText(cn::EXAM_CONFIGURATIONS_DETAILS_STRAND_IDS_COL)->nullable();
            $table->longText(cn::EXAM_CONFIGURATIONS_DETAILS_LEARNING_UNIT_IDS_COL)->nullable();
            $table->longText(cn::EXAM_CONFIGURATIONS_DETAILS_LEARNING_OBJECTIVES_IDS_COL)->nullable();
            $table->longText(cn::EXAM_CONFIGURATIONS_DETAILS_DIFFICULTY_MODE_COL)->nullable();
            $table->longText(cn::EXAM_CONFIGURATIONS_DETAILS_DIFFICULTY_LEVELS_COL)->nullable();
            $table->Integer(cn::EXAM_CONFIGURATIONS_DETAILS_NO_OF_QUESTIONS_COL)->nullable()->default(0);
            $table->string(cn::EXAM_CONFIGURATIONS_DETAILS_TIME_DURATION_COL,50)->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign(cn::EXAM_CONFIGURATIONS_DETAILS_EXAM_ID_COL)->references(cn::EXAM_TABLE_ID_COLS)->on(cn::EXAM_TABLE_NAME)->onDelete(cn::DB_ENGINE_ONDELETE_NAME);
            $table->foreign(cn::EXAM_CONFIGURATIONS_DETAILS_CREATED_BY_USER_ID_COL)->references(cn::USERS_ID_COL)->on(cn::USERS_TABLE_NAME)->onDelete(cn::DB_ENGINE_ONDELETE_NAME);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists(cn::EXAM_CONFIGURATIONS_DETAILS_TABLE_NAME);
    }
}
