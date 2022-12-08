<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Constants\DbConstant as cn;

class CreateRemainderUpdateSchoolYearDataTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(cn::REMAINDER_UPDATE_SCHOOL_YEAR_DATA_TABLE, function (Blueprint $table) {
            $table->bigIncrements(cn::REMAINDER_UPDATE_SCHOOL_YEAR_DATA_ID_COL);
            $table->unsignedBigInteger(cn::REMAINDER_UPDATE_SCHOOL_YEAR_DATA_CURRICULUM_YEAR_ID_COL)->nullable();
            $table->unsignedBigInteger(cn::REMAINDER_UPDATE_SCHOOL_YEAR_DATA_SCHOOL_ID_COL)->nullable();
            $table->date(cn::REMAINDER_UPDATE_SCHOOL_YEAR_DATA_IMPORTED_DATE_COL)->nullable();
            $table->unsignedBigInteger(cn::REMAINDER_UPDATE_SCHOOL_YEAR_DATA_UPLOADED_BY_COL)->nullable();
            $table->enum(cn::REMAINDER_UPDATE_SCHOOL_YEAR_DATA_STATUS_COL,['pending','complete'])->default('pending');
            $table->timestamps();
            $table->softDeletes();
            $table->foreign(cn::REMAINDER_UPDATE_SCHOOL_YEAR_DATA_CURRICULUM_YEAR_ID_COL)->references(cn::CURRICULUM_YEAR_ID_COL)->on(cn::CURRICULUM_YEAR_TABLE_NAME)->onDelete(cn::DB_ENGINE_ONDELETE_NAME);
            $table->foreign(cn::REMAINDER_UPDATE_SCHOOL_YEAR_DATA_SCHOOL_ID_COL)->references(cn::SCHOOL_ID_COLS)->on(cn::SCHOOL_TABLE_NAME)->onDelete(cn::DB_ENGINE_ONDELETE_NAME);
            $table->foreign(cn::REMAINDER_UPDATE_SCHOOL_YEAR_DATA_UPLOADED_BY_COL)->references(cn::USERS_ID_COL)->on(cn::USERS_TABLE_NAME)->onDelete(cn::DB_ENGINE_ONDELETE_NAME);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {   
        Schema::dropIfExists(cn::REMAINDER_UPDATE_SCHOOL_YEAR_DATA_TABLE);
    }
}
