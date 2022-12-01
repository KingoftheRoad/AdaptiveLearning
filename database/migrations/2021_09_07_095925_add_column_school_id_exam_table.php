<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Constants\DbConstant as cn;

class AddColumnSchoolIdExamTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasColumn(cn::EXAM_TABLE_NAME,cn::EXAM_TABLE_SCHOOL_COLS)){
            Schema::table(cn::EXAM_TABLE_NAME, function (Blueprint $table) {
                $table->dropColumn(cn::EXAM_TABLE_SCHOOL_COLS);            
            });
        }

        if (Schema::hasColumn(cn::EXAM_TABLE_NAME,cn::EXAM_TABLE_RESULT_DECLARE_COL)){
            Schema::table(cn::EXAM_TABLE_NAME, function (Blueprint $table) {
                $table->dropColumn(cn::EXAM_TABLE_RESULT_DECLARE_COL);            
            });
        }

        Schema::table(cn::EXAM_TABLE_NAME, function (Blueprint $table) {
            $table->after(cn::EXAM_TABLE_TITLE_COLS, function($table){
                $table->bigInteger(cn::EXAM_TABLE_SCHOOL_COLS)->nullable();
                
            });

            $table->after(cn::EXAM_TABLE_STATUS_COLS, function($table){
                $table->enum(cn::EXAM_TABLE_RESULT_DECLARE_COL,['true','false'])->default('false')->nullable();
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
