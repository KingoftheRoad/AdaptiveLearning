<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Constants\DbConstant as cn;

class AddColumnsPreConfiguredDifficultyTable extends Migration
{
    public function up(){
        Schema::table(cn::PRE_CONFIGURE_DIFFICULTY_TABLE_NAME, function (Blueprint $table) {
            $table->after(cn::PRE_CONFIGURE_DIFFICULTY_DIFFICULTY_LEVEL_NAME_COL,function($table){
                $table->String(cn::PRE_CONFIGURE_DIFFICULTY_DIFFICULTY_LEVEL_NAME_EN_COL)->nullable();
                $table->String(cn::PRE_CONFIGURE_DIFFICULTY_DIFFICULTY_LEVEL_NAME_CH_COL)->nullable();
            });          
        });
    }

    public function down(){
        Schema::table(cn::MAIN_UPLOAD_DOCUMENT_TABLE_NAME, function($table) {
            $table->dropColumn(cn::PRE_CONFIGURE_DIFFICULTY_DIFFICULTY_LEVEL_NAME_EN_COL);
            $table->dropColumn(cn::PRE_CONFIGURE_DIFFICULTY_DIFFICULTY_LEVEL_NAME_CH_COL);
        });
    }
}
