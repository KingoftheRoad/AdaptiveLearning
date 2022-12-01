<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Constants\DbConstant as cn;

class AddIsAvailableQuestionInLearningObjectivesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table(cn::LEARNING_OBJECTIVES_TABLE_NAME, function (Blueprint $table) {
            $table->after(cn::LEARNING_OBJECTIVES_CODE_COL, function($table){
                $table->enum(cn::LEARNING_OBJECTIVES_IS_AVAILABLE_QUESTIONS_COL,['yes','no'])->default('yes')->nullable();
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
        Schema::table(cn::LEARNING_OBJECTIVES_TABLE_NAME, function (Blueprint $table) {
            $table->dropColumn(cn::LEARNING_OBJECTIVES_IS_AVAILABLE_QUESTIONS_COL);
        });
    }
}
