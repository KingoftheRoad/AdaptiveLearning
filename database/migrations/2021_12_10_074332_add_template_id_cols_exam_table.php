<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Constants\DbConstant as cn;

class AddTemplateIdColsExamTable extends Migration
{
   
    public function up()
    {
        Schema::table(cn::EXAM_TABLE_NAME,function(Blueprint $table){
            $table->after('group_ids',function($table){
                $table->bigInteger(cn::EXAM_TABLE_TEMPLATE_ID)->nullable();
            });
        });
    }

    public function down()
    {
        //
    }
}
