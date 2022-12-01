<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Constants\DbConstant as cn;

class AddStrandIdColLearningUnitsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasColumn(cn::LEARNING_UNITS_TABLE_NAME, cn::LEARNING_UNITS_STRANDID_COL)){
            Schema::table(cn::LEARNING_UNITS_TABLE_NAME, function (Blueprint $table) {
                $table->dropColumn(cn::LEARNING_UNITS_STRANDID_COL);
            });
        }

        Schema::table(cn::LEARNING_UNITS_TABLE_NAME, function (Blueprint $table) {
            $table->after(cn::LEARNING_UNITS_NAME_COL, function($table){
                $table->unsignedBigInteger(cn::LEARNING_UNITS_STRANDID_COL)->nullable();
            });

            $table->foreign(cn::LEARNING_UNITS_STRANDID_COL)->references(cn::STRANDS_ID_COL)->on(cn::STRANDS_TABLE_NAME)->onDelete(cn::DB_ENGINE_ONDELETE_NAME);
        });

        
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
