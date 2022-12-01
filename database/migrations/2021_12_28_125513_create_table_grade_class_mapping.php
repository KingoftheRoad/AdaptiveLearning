<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Constants\DbConstant as cn;

class CreateTableGradeClassMapping extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(cn::GRADE_CLASS_MAPPING_TABLE_NAME, function (Blueprint $table) {
            $table->bigIncrements(cn::GRADE_CLASS_MAPPING_ID_COL);
            $table->unsignedBigInteger(cn::GRADE_CLASS_MAPPING_SCHOOL_ID_COL);
            $table->unsignedBigInteger(cn::GRADE_CLASS_MAPPING_GRADE_ID_COL);
            $table->String(cn::GRADE_CLASS_MAPPING_NAME_COL);
            $table->enum(cn::PRE_CONFIGURE_DIFFICULTY_STATUS_COL,['active','inactive'])->default('active');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign(cn::GRADE_CLASS_MAPPING_SCHOOL_ID_COL)->references(cn::SCHOOL_ID_COLS)->on(cn::SCHOOL_TABLE_NAME)->onDelete(cn::DB_ENGINE_ONDELETE_NAME);
            $table->foreign(cn::GRADE_CLASS_MAPPING_GRADE_ID_COL)->references(cn::GRADES_ID_COL)->on(cn::GRADES_TABLE_NAME)->onDelete(cn::DB_ENGINE_ONDELETE_NAME);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists(cn::GRADE_CLASS_MAPPING_TABLE_NAME);
    }
}
