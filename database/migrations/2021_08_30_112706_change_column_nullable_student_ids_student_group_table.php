<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Constants\DbConstant as cn;

class ChangeColumnNullableStudentIdsStudentGroupTable extends Migration
{
    public function up()
    {
        if (Schema::hasColumn(cn::STUDENT_GROUP_TABLE_NAME,cn::STUDENT_GROUP_STUDENT_ID_COL)){
            Schema::table(cn::STUDENT_GROUP_TABLE_NAME, function (Blueprint $table) {
                $table->longText(cn::STUDENT_GROUP_STUDENT_ID_COL)->nullable()->change();            
            });
        }
    }

    public function down()
    {
        //
    }
}
