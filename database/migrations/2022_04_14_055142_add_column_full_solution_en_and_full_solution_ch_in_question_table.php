<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Constants\DbConstant as cn;

class AddColumnFullSolutionEnAndFullSolutionChInQuestionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table(cn::QUESTION_TABLE_NAME, function (Blueprint $table) {
            $table->after(cn::QUESTION_GENERAL_HINTS_VIDEO_ID_CH,function($table){
                $table->longText(cn::QUESTION_FULL_SOLUTION_EN)->nullable();
                $table->longText(cn::QUESTION_FULL_SOLUTION_CH)->nullable();
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
