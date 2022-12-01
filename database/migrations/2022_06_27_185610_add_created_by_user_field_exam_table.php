<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Constants\DbConstant as cn;

class AddCreatedByUserFieldExamTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table(cn::EXAM_TABLE_NAME, function (Blueprint $table) {
            $table->after(cn::EXAM_TABLE_CREATED_BY_COL,function($table){
                $table->enum(cn::EXAM_TABLE_CREATED_BY_USER_COL,['super_admin','school_admin','teacher','principal','student'])->nullable();
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
            $table->dropColumn(cn::EXAM_TABLE_CREATED_BY_USER_COL);
        });
    }
}
