<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Constants\DbConstant as cn;

class ChangeGradeIdAndClassIdInCurriculumYearStudentMappingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::disableForeignKeyConstraints();
        Schema::table(cn::CURRICULUM_YEAR_STUDENT_MAPPING_TABLE, function (Blueprint $table) {
            $table->unsignedBigInteger(cn::CURRICULUM_YEAR_STUDENT_MAPPING_GRADE_ID_COL)->nullable()->change();
            $table->unsignedBigInteger(cn::CURRICULUM_YEAR_STUDENT_MAPPING_CLASS_ID_COL)->nullable()->change();
        });
        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(){
    }
}
