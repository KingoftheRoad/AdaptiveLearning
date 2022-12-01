<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Constants\DbConstant as cn;

class AddFieldExamTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table(cn::EXAM_TABLE_NAME, function (Blueprint $table) {
            $table->after(cn::EXAM_TABLE_SELF_LEARNING_TEST_TYPE_COL,function($table){
                $table->integer(cn::EXAM_TABLE_NO_OF_TRIALS_PER_QUESTIONS_COL)->nullable();
                $table->enum(cn::EXAM_TABLE_DIFFICULTY_MODE_COL, ['manual', 'auto'])->nullable();
                $table->string(cn::EXAM_TABLE_DIFFICULTY_LEVELS_COL)->nullable();
                $table->enum(cn::EXAM_TABLE_IS_DISPLAY_HINTS_COL, ['yes', 'no'])->nullable();
                $table->enum(cn::EXAM_TABLE_IS_DISPLAY_FULL_SOLUTIONS_COL, ['yes', 'no'])->nullable();
                $table->enum(cn::EXAM_TABLE_IS_DISPLAY_PER_ANSWER_HINTS_COL, ['yes', 'no'])->nullable();
                $table->enum(cn::EXAM_TABLE_IS_RANDOMIZED_ANSWERS_COL, ['yes', 'no'])->nullable();
                $table->enum(cn::EXAM_TABLE_IS_RANDOMIZED_ORDER_COL, ['yes', 'no'])->nullable();
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
        Schema::table(cn::EXAM_TABLE_NAME, function (Blueprint $table) {
            $table->dropColumn(cn::EXAM_TABLE_NO_OF_TRIALS_PER_QUESTIONS_COL);
            $table->dropColumn(cn::EXAM_TABLE_DIFFICULTY_MODE_COL);
            $table->dropColumn(cn::EXAM_TABLE_DIFFICULTY_LEVELS_COL);
            $table->dropColumn(cn::EXAM_TABLE_IS_DISPLAY_HINTS_COL);
            $table->dropColumn(cn::EXAM_TABLE_IS_DISPLAY_FULL_SOLUTIONS_COL);
            $table->dropColumn(cn::EXAM_TABLE_IS_DISPLAY_PER_ANSWER_HINTS_COL);
            $table->dropColumn(cn::EXAM_TABLE_IS_RANDOMIZED_ANSWERS_COL);
            $table->dropColumn(cn::EXAM_TABLE_IS_RANDOMIZED_ORDER_COL);
        });
    }
}
