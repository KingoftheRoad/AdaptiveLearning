<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Constants\DbConstant as cn;

class AddExamTypeColsExamTable extends Migration
{
    public function up()
    {
        Schema::table(cn::EXAM_TABLE_NAME,function(Blueprint $table){
            $table->after(cn::EXAM_TABLE_ID_COLS,function($table){
                $table->enum(cn::EXAM_TYPE_COLS,['1','2','3'])->comment('1 = Self-Learning, 2 = Excercise, 3 = Test')->nullable();
            });
        });
    }

    public function down()
    {
        //
    }
}
