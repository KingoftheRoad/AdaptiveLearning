<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Constants\DbConstant as cn;

class AddCommentColumnQuestionTypeQuestionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table(cn::QUESTION_TABLE_NAME, function ($table) {
            $table->string(cn::QUESTION_QUESTION_TYPE_COL)->nullable()->change()->comment('1 = Self-Learning, 2 = Exercise/Assignment, 3 = Testing, 4 = Seed');
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
