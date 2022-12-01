<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Constants\DbConstant as cn;

class AddSelfLearningTestTypeColExamTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table(cn::EXAM_TABLE_NAME, function (Blueprint $table) {
            $table->after(cn::EXAM_TABLE_TEMPLATE_ID, function($table){
                $table->enum(cn::EXAM_TABLE_SELF_LEARNING_TEST_TYPE_COL,[1,2])->nullable()->comment('1-Excercise,2-Test');
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
            $table->dropColumn(cn::EXAM_TABLE_SELF_LEARNING_TEST_TYPE_COL);
        });
    }
}
