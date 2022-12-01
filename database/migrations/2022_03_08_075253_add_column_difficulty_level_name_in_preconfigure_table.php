<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Constants\DbConstant as cn;

class AddColumnDifficultyLevelNameInPreconfigureTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table(cn::PRE_CONFIGURE_DIFFICULTY_TABLE_NAME, function (Blueprint $table) {
            $table->after(cn::PRE_CONFIGURE_DIFFICULTY_ID_COL,function($table){
                $table->String(cn::PRE_CONFIGURE_DIFFICULTY_DIFFICULTY_LEVEL_NAME_COL)->nullable();
                $table->String(cn::PRE_CONFIGURE_DIFFICULTY_DIFFICULTY_LEVEL_COLOR_COL)->nullable();
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
        Schema::table(cn::PRE_CONFIGURE_DIFFICULTY_TABLE_NAME, function (Blueprint $table) {
            //
        });
    }
}
