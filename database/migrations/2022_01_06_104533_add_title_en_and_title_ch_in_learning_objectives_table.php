<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Constants\DbConstant as cn;

class AddTitleEnAndTitleChInLearningObjectivesTable extends Migration
{
  
    public function up()
    {
        Schema::table(cn::LEARNING_OBJECTIVES_TABLE_NAME, function (Blueprint $table) {
            $table->after(cn::LEARNING_OBJECTIVES_TITLE_COL, function($table){
                $table->String(cn::LEARNING_OBJECTIVES_TITLE_EN_COL)->nullable();
                $table->String(cn::LEARNING_OBJECTIVES_TITLE_CH_COL)->nullable();
            });
        });
    }

    public function down()
    {
        //
    }
}
