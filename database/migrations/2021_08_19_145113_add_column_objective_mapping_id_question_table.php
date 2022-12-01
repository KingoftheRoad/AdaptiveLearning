<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Constants\DbConstant as cn;

class AddColumnObjectiveMappingIdQuestionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table(cn::QUESTION_TABLE_NAME, function (Blueprint $table) {
            $table->after(cn::QUESTION_TABLE_ID_COL, function($table){
                $table->unsignedBigInteger(cn::QUESTION_OBJECTIVE_MAPPING_ID_COL)->nullable();
                $table->foreign(cn::QUESTION_OBJECTIVE_MAPPING_ID_COL)->references(cn::OBJECTIVES_MAPPINGS_ID_COL)->on(cn::OBJECTIVES_MAPPINGS_TABLE_NAME)->onDelete(cn::DB_ENGINE_ONDELETE_NAME);
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
        Schema::table(cn::QUESTION_TABLE_NAME, function (Blueprint $table) {
            $table->dropColumn(cn::QUESTION_OBJECTIVE_MAPPING_ID_COL);
        });
    }
}
