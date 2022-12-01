<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Constants\DbConstant as cn;

class AddColumnExamTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table(cn::EXAM_TABLE_NAME, function (Blueprint $table) {
            $table->after(cn::EXAM_TABLE_DESCRIPTION_COLS, function($table){
                $table->longText(cn::EXAM_TABLE_QUESTION_IDS_COL)->nullable();
                $table->longText(cn::EXAM_TABLE_STUDENT_IDS_COL)->nullable();
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
            $table->dropColumn(cn::EXAM_TABLE_QUESTION_IDS_COL);
            $table->dropColumn(cn::EXAM_TABLE_STUDENT_IDS_COL);
        });
    }
}
