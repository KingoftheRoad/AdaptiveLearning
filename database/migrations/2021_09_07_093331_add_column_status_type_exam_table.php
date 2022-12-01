<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Constants\DbConstant as cn;

class AddColumnStatusTypeExamTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasColumn(cn::EXAM_TABLE_NAME,cn::EXAM_TABLE_STATUS_COLS)){
            Schema::table(cn::EXAM_TABLE_NAME, function (Blueprint $table) {
                $table->dropColumn(cn::EXAM_TABLE_STATUS_COLS);
            });
        }

        Schema::table(cn::EXAM_TABLE_NAME, function (Blueprint $table) {
            $table->after(cn::EXAM_TABLE_STUDENT_IDS_COL, function($table){
                $table->enum(cn::EXAM_TABLE_STATUS_COLS, ['pending','publish','active', 'inactive', 'complete'])->default('pending')->nullable();
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
