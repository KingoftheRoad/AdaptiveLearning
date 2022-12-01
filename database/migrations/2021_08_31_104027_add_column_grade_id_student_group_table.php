<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Constants\DbConstant as cn;

class AddColumnGradeIdStudentGroupTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasColumn(cn::STUDENT_GROUP_TABLE_NAME,cn::STUDENT_GROUP_GRADE_ID_COL)){
            Schema::table(cn::STUDENT_GROUP_TABLE_NAME, function (Blueprint $table) {
                $table->dropColumn(cn::STUDENT_GROUP_GRADE_ID_COL);            
            });
        }

        Schema::table(cn::STUDENT_GROUP_TABLE_NAME, function (Blueprint $table) {
            $table->after(cn::STUDENT_GROUP_NAME_COL, function($table){
                $table->unsignedBigInteger(cn::STUDENT_GROUP_GRADE_ID_COL)->nullable();
                $table->foreign(cn::STUDENT_GROUP_GRADE_ID_COL)->references(cn::GRADES_ID_COL)->on(cn::GRADES_TABLE_NAME)->onDelete(cn::DB_ENGINE_ONDELETE_NAME);
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
