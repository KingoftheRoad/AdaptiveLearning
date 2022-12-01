<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Constants\DbConstant as cn;

class AddColumnSchoolEmailInSchoolTable extends Migration
{
    public function up(){
        Schema::table(cn::SCHOOL_TABLE_NAME, function (Blueprint $table) {
            $table->after(cn::SCHOOL_SCHOOL_CODE_COL,function($table){
                $table->string(cn::SCHOOL_SCHOOL_EMAIL_COL)->nullable();
            });
        });
    }

    public function down(){
        Schema::table(cn::SCHOOL_TABLE_NAME, function (Blueprint $table) {
            $table->dropColumn(cn::SCHOOL_SCHOOL_EMAIL_COL);  
        });
    }
}
