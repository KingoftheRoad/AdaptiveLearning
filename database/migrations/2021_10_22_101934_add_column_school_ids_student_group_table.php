<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Constants\DbConstant as cn;

class AddColumnSchoolIdsStudentGroupTable extends Migration
{
   
    public function up()
    {
        if (Schema::hasColumn(cn::STUDENT_GROUP_TABLE_NAME, cn::STUDENT_GROUP_SCHOOL_ID_COL)){
            Schema::table(cn::STUDENT_GROUP_TABLE_NAME, function (Blueprint $table) {
                $table->dropColumn(cn::STUDENT_GROUP_SCHOOL_ID_COL);
            });
        }
        Schema::table(cn::STUDENT_GROUP_TABLE_NAME, function (Blueprint $table) {
            $table->after(cn::STUDENT_GROUP_STUDENT_ID_COL, function($table){
                $table->longText(cn::STUDENT_GROUP_SCHOOL_ID_COL)->nullable();
            });
        });
    }
}
