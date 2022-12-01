<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Constants\DbConstant as cn;

class AddColumnCodeGradesTable extends Migration
{
    public function up()
    {
        Schema::table(cn::GRADES_TABLE_NAME, function (Blueprint $table) {
            $table->after(cn::GRADES_NAME_COL, function($table){
                $table->string(cn::GRADES_CODE_COL)->nullable();
            });
        });
    }

    public function down()
    {
        //
    }
}
