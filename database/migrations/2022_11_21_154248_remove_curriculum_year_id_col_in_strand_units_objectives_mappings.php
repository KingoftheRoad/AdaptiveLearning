<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Constants\DbConstant as cn;

class RemoveCurriculumYearIdColInStrandUnitsObjectivesMappings extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasColumn(cn::OBJECTIVES_MAPPINGS_TABLE_NAME, 'curriculum_year_id')){
            Schema::table(cn::OBJECTIVES_MAPPINGS_TABLE_NAME, function($table) {
                $table->dropForeign(['curriculum_year_id']);
                $table->dropColumn('curriculum_year_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table(cn::OBJECTIVES_MAPPINGS_TABLE_NAME, function (Blueprint $table) {
            $table->after(cn::OBJECTIVES_MAPPINGS_ID_COL, function($table){
                $table->unsignedBigInteger('curriculum_year_id')->nullable();
                $table->foreign('curriculum_year_id')->references(cn::CURRICULUM_YEAR_ID_COL)->on(cn::CURRICULUM_YEAR_TABLE_NAME)->onDelete(cn::DB_ENGINE_ONDELETE_NAME);
            });
        });
    }
}
