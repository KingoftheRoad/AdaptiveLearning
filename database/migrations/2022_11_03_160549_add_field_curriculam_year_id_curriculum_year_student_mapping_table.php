<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Constants\DbConstant as cn;

class AddFieldCurriculamYearIdCurriculumYearStudentMappingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(Schema::hasColumn(cn::CURRICULUM_YEAR_STUDENT_MAPPING_TABLE, 'curriculm_year')){
            Schema::table(cn::CURRICULUM_YEAR_STUDENT_MAPPING_TABLE, function (Blueprint $table){
                $table->dropColumn('curriculm_year');
            });
        }

        Schema::table(cn::CURRICULUM_YEAR_STUDENT_MAPPING_TABLE, function (Blueprint $table) {
            $table->after(cn::CURRICULUM_YEAR_STUDENT_MAPPING_ID_COL, function($table){
                $table->unsignedBigInteger(cn::CURRICULUM_YEAR_STUDENT_MAPPING_CURRICULUM_YEAR_ID_COL)->nullable();
                $table->foreign(cn::CURRICULUM_YEAR_STUDENT_MAPPING_CURRICULUM_YEAR_ID_COL)->references(cn::CURRICULUM_YEAR_ID_COL)->on(cn::CURRICULUM_YEAR_TABLE_NAME)->onDelete(cn::DB_ENGINE_ONDELETE_NAME);
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
        Schema::table(cn::CURRICULUM_YEAR_STUDENT_MAPPING_TABLE, function (Blueprint $table) {
            $table->dropForeign([cn::CURRICULUM_YEAR_STUDENT_MAPPING_CURRICULUM_YEAR_ID_COL.'_foreign']);
            $table->dropColumn(cn::CURRICULUM_YEAR_STUDENT_MAPPING_CURRICULUM_YEAR_ID_COL);
        });
    }
}
