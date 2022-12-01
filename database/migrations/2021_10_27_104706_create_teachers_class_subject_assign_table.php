<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Constants\DbConstant as cn;
use Illuminate\Support\Facades\Artisan;

class CreateTeachersClassSubjectAssignTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(cn::TEACHER_CLASS_SUBJECT_TABLE_NAME, function (Blueprint $table) {
            $table->bigIncrements(cn::TEACHER_CLASS_SUBJECT_ID_COL);
            $table->unsignedBigInteger(cn::TEACHER_CLASS_SUBJECT_SCHOOL_ID_COL)->default('0');
            $table->foreign(cn::TEACHER_CLASS_SUBJECT_SCHOOL_ID_COL)->references(cn::SCHOOL_ID_COLS)->on(cn::SCHOOL_TABLE_NAME);
            $table->unsignedBigInteger(cn::TEACHER_CLASS_SUBJECT_TEACHER_ID_COL)->default('0');
            $table->foreign(cn::TEACHER_CLASS_SUBJECT_TEACHER_ID_COL)->references(cn::USERS_ID_COL)->on(cn::USERS_TABLE_NAME);
            $table->unsignedBigInteger(cn::TEACHER_CLASS_SUBJECT_CLASS_ID_COL)->default('0');
            $table->foreign(cn::TEACHER_CLASS_SUBJECT_CLASS_ID_COL)->references(cn::GRADES_ID_COL)->on(cn::GRADES_TABLE_NAME);
            $table->longText(cn::TEACHER_CLASS_SUBJECT_SUBJECT_ID_COL)->nullable();
            $table->enum(cn::TEACHER_CLASS_SUBJECT_STATUS_COL,['active','inactive'])->default('active'); 
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
        Schema::dropIfExists(cn::TEACHER_CLASS_SUBJECT_TABLE_NAME);
    }
}
