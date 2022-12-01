<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Constants\DbConstant as cn;
class CreateStudyReportTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(cn::STUDY_REPORT_TABLE, function (Blueprint $table) {
            $table->engine = cn::DB_ENGINE_NAME;
            $table->bigIncrements(cn::STUDY_REPORT_ID_COL);
            $table->enum(cn::STUDY_REPORT_REPORT_TYPE_COL,['assignment_test','self_learning'])->nullable()->comment('Assignment/Test,Self Learning');
            $table->tinyInteger(cn::STUDY_REPORT_STUDY_TYPE_COL)->comment('1: Exercise; 2: Test;');
            $table->unsignedBigInteger(cn::STUDY_REPORT_SCHOOL_ID_COL)->nullable();
            $table->unsignedBigInteger(cn::STUDY_REPORT_EXAM_ID_COL)->nullable();
            $table->unsignedBigInteger(cn::STUDY_REPORT_GRADE_ID_COL)->nullable();
            $table->unsignedBigInteger(cn::STUDY_REPORT_CLASS_ID_COL)->nullable();
            $table->longText(cn::STUDY_REPORT_AVERAGE_ACCURACY_COL)->nullable();
            $table->longText(cn::STUDY_REPORT_STUDY_STATUS_COL)->nullable();
            $table->longText(cn::STUDY_REPORT_QUESTIONS_DIFFICULTIES_COL)->nullable();
            $table->dateTime(cn::STUDY_REPORT_DATE_TIME_COL);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign(cn::STUDY_REPORT_SCHOOL_ID_COL)->references(cn::SCHOOL_ID_COLS)->on(cn::SCHOOL_TABLE_NAME)->onDelete(cn::DB_ENGINE_ONDELETE_NAME);
            $table->foreign(cn::STUDY_REPORT_EXAM_ID_COL)->references(cn::EXAM_TABLE_ID_COLS)->on(cn::EXAM_TABLE_NAME)->onDelete(cn::DB_ENGINE_ONDELETE_NAME);
            $table->foreign(cn::STUDY_REPORT_GRADE_ID_COL)->references(cn::GRADES_ID_COL)->on(cn::GRADES_TABLE_NAME)->onDelete(cn::DB_ENGINE_ONDELETE_NAME);
            $table->foreign(cn::STUDY_REPORT_CLASS_ID_COL)->references(cn::GRADE_CLASS_MAPPING_ID_COL)->on(cn::GRADE_CLASS_MAPPING_TABLE_NAME)->onDelete(cn::DB_ENGINE_ONDELETE_NAME);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists(cn::STUDY_REPORT_TABLE);
    }
}
