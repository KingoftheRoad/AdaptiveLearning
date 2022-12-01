<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Constants\DbConstant as cn;

class AddNewConfigurationsFieldsExamsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table(cn::EXAM_TABLE_NAME, function (Blueprint $table) {
            $table->after(cn::EXAM_TABLE_ID_COLS,function($table){
                $table->integer(cn::EXAM_TABLE_PARENT_EXAM_ID_COLS)->nullable();
            });
            $table->after(cn::EXAM_TABLE_ID_COLS,function($table){
                $table->tinyInteger(cn::EXAM_TABLE_USE_OF_MODE_COLS)->nullable()->comment('1 = As a Test/Exercise, 2 = As a Collection of Questions');
            });
            $table->after(cn::EXAM_TABLE_END_TIME_COL,function($table){
                $table->enum(cn::EXAM_TABLE_REPORT_TYPE_COLS, ['end_date', 'after_submit','custom_date'])->nullable();
            });
            $table->after(cn::EXAM_TABLE_IS_RANDOMIZED_ORDER_COL,function($table){
                $table->longText(cn::EXAM_TABLE_LEARNING_OBJECTIVES_CONFIGURATIONS_COL)->nullable();
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
            $table->dropColumn(cn::EXAM_TABLE_PARENT_EXAM_ID_COLS);
            $table->dropColumn(cn::EXAM_TABLE_USE_OF_MODE_COLS);
            $table->dropColumn(cn::EXAM_TABLE_REPORT_TYPE_COLS);
            $table->dropColumn(cn::EXAM_TABLE_LEARNING_OBJECTIVES_CONFIGURATIONS_COL);
        });
    }
}
