<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Constants\DbConstant as cn;

class CreateCurriculumYearStudentMappingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(cn::CURRICULUM_YEAR_STUDENT_MAPPING_TABLE, function (Blueprint $table) {
            $table->engine = cn::DB_ENGINE_NAME;
            $table->bigIncrements(cn::CURRICULUM_YEAR_STUDENT_MAPPING_ID_COL);
            $table->unsignedBigInteger(cn::CURRICULUM_YEAR_STUDENT_MAPPING_USER_ID_COL);
            $table->unsignedBigInteger(cn::CURRICULUM_YEAR_STUDENT_MAPPING_SCHOOL_ID_COL);
            $table->unsignedBigInteger(cn::CURRICULUM_YEAR_STUDENT_MAPPING_GRADE_ID_COL);
            $table->unsignedBigInteger(cn::CURRICULUM_YEAR_STUDENT_MAPPING_CLASS_ID_COL);
            $table->string('curriculm_year',10)->nullable();
            $table->boolean(cn::CURRICULUM_YEAR_STUDENT_MAPPING_STATUS_COL)->default(1)->comment('1 = Active, 0 = InActive');
            $table->timestamps();
            $table->softDeletes();


            $table->foreign(cn::CURRICULUM_YEAR_STUDENT_MAPPING_USER_ID_COL)->references(cn::USERS_ID_COL)->on(cn::USERS_TABLE_NAME)->onDelete(cn::DB_ENGINE_ONDELETE_NAME);
            $table->foreign(cn::CURRICULUM_YEAR_STUDENT_MAPPING_SCHOOL_ID_COL)->references(cn::SCHOOL_ID_COLS)->on(cn::SCHOOL_TABLE_NAME)->onDelete(cn::DB_ENGINE_ONDELETE_NAME);
            $table->foreign(cn::CURRICULUM_YEAR_STUDENT_MAPPING_GRADE_ID_COL)->references(cn::GRADES_ID_COL)->on(cn::GRADES_TABLE_NAME)->onDelete(cn::DB_ENGINE_ONDELETE_NAME);
            $table->foreign(cn::CURRICULUM_YEAR_STUDENT_MAPPING_CLASS_ID_COL)->references(cn::GRADE_CLASS_MAPPING_ID_COL)->on(cn::GRADE_CLASS_MAPPING_TABLE_NAME)->onDelete(cn::DB_ENGINE_ONDELETE_NAME);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('curriculum_year_student_mapping');
    }
}
