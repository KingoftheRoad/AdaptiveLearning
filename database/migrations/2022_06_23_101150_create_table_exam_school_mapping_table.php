<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Constants\DbConstant as cn;

class CreateTableExamSchoolMappingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(cn::EXAM_SCHOOL_MAPPING_TABLE, function (Blueprint $table) {
            $table->bigIncrements(cn::EXAM_SCHOOL_MAPPING_ID_COL);
            $table->unsignedBigInteger(cn::EXAM_SCHOOL_MAPPING_SCHOOL_ID_COL)->nullable();
            $table->unsignedBigInteger(cn::EXAM_SCHOOL_MAPPING_EXAM_ID_COL)->nullable();
            $table->enum(cn::EXAM_SCHOOL_MAPPING_STATUS_COL,['draft','publish','inactive'])->nullable();
            $table->timestamps();
            $table->SoftDeletes();

            $table->foreign(cn::EXAM_SCHOOL_MAPPING_EXAM_ID_COL)->references(cn::EXAM_TABLE_ID_COLS)->on(cn::EXAM_TABLE_NAME)->onDelete(cn::DB_ENGINE_ONDELETE_NAME);
            $table->foreign(cn::EXAM_SCHOOL_MAPPING_SCHOOL_ID_COL)->references(cn::SCHOOL_ID_COLS)->on(cn::SCHOOL_TABLE_NAME)->onDelete(cn::DB_ENGINE_ONDELETE_NAME);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists(cn::EXAM_SCHOOL_MAPPING_TABLE);
    }
}
