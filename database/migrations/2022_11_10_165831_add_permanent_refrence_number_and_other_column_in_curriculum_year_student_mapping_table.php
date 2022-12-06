<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Constants\DbConstant as cn;

class AddPermanentRefrenceNumberAndOtherColumnInCurriculumYearStudentMappingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table(cn::CURRICULUM_YEAR_STUDENT_MAPPING_TABLE, function (Blueprint $table) {
            $table->after(cn::CURRICULUM_YEAR_STUDENT_MAPPING_CLASS_ID_COL, function($table){
                $table->String(cn::CURRICULUM_YEAR_STUDENT_NUMBER_WITHIN_CLASS_COL)->nullable();
                $table->String(cn::CURRICULUM_YEAR_STUDENT_CLASS)->nullable();
                $table->String(cn::CURRICULUM_YEAR_CLASS_STUDENT_NUMBER)->nullable();
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
            $table->dropColumn(Cn::CURRICULUM_YEAR_STUDENT_NUMBER_WITHIN_CLASS_COL);
            $table->dropColumn(Cn::CURRICULUM_YEAR_STUDENT_CLASS);
            $table->dropColumn(Cn::CURRICULUM_YEAR_CLASS_STUDENT_NUMBER);
        });
    }
}
