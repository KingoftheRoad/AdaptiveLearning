<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Constants\DbConstant as cn;

class AddLearningUnitIdColLearningObjectivesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasColumn(cn::LEARNING_OBJECTIVES_TABLE_NAME, cn::LEARNING_OBJECTIVES_LEARNING_UNITID_COL)){
            Schema::table(cn::LEARNING_OBJECTIVES_TABLE_NAME, function (Blueprint $table) {
                $table->dropColumn(cn::LEARNING_OBJECTIVES_LEARNING_UNITID_COL);
            });
        }

        Schema::table(cn::LEARNING_OBJECTIVES_TABLE_NAME, function (Blueprint $table) {
            $table->after(cn::LEARNING_OBJECTIVES_TITLE_COL, function($table){
                $table->unsignedBigInteger(cn::LEARNING_OBJECTIVES_LEARNING_UNITID_COL)->nullable();
            });

            $table->foreign(cn::LEARNING_OBJECTIVES_LEARNING_UNITID_COL)->references(cn::LEARNING_UNITS_ID_COL)->on(cn::LEARNING_UNITS_TABLE_NAME)->onDelete(cn::DB_ENGINE_ONDELETE_NAME);
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
