<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Constants\DbConstant as cn;

class ChangeColumnsNullableSchoolTable extends Migration
{
 
    public function up()
    {
        Schema::table(cn::SCHOOL_TABLE_NAME, function (Blueprint $table) {
            $table->after(cn::SCHOOL_SCHOOL_NAME_COL, function($table){
                $table->String(cn::SCHOOL_SCHOOL_CODE_COL)->nullable()->change();
                $table->String(cn::SCHOOL_SCHOOL_ADDRESS)->nullable()->change();
                $table->String(cn::SCHOOL_SCHOOL_CITY)->nullable()->change();
            });
        });
    }

    public function down()
    {
        //
    }
}
