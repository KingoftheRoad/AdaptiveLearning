<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Constants\DbConstant as cn;

class CreateTableClassAssignmentStudents extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(cn::CLASS_ASSIGNMENT_STUDENTS_TABLE_NAME, function (Blueprint $table) {
            $table->bigIncrements(cn::CLASS_ASSIGNMENT_ID_COL);
            $table->Integer(cn::CLASS_ASSIGNMENT_SCHOOL_ID_COL)->nullable();
            $table->Integer(cn::CLASS_ASSIGNMENT_CLASS_ID_COL)->nullable();
            $table->Integer(cn::CLASS_ASSIGNMENT_STUDENT_ID_COL)->nullable();
            $table->enum(cn::CLASS_ASSIGNMENT_STATUS_COL,['active','inactive'])->default('active');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('table_class_assignment_students');
    }
}
