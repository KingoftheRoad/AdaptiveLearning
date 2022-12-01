<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Constants\DbConstant as cn;

class AddColumnClassNameIdColTeachersClassSubjectAssign extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table(cn::TEACHER_CLASS_SUBJECT_TABLE_NAME, function (Blueprint $table) {
            $table->after(cn::TEACHER_CLASS_SUBJECT_SUBJECT_ID_COL, function($table){
                $table->longText(cn::TEACHER_CLASS_SUBJECT_CLASS_NAME_ID_COL);
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
