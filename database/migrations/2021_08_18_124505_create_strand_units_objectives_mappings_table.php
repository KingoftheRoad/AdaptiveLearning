<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Constants\DbConstant as cn;

class CreateStrandUnitsObjectivesMappingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(cn::OBJECTIVES_MAPPINGS_TABLE_NAME, function (Blueprint $table) {
            $table->engine = cn::DB_ENGINE_NAME;
            $table->bigIncrements(cn::OBJECTIVES_MAPPINGS_ID_COL);
            $table->unsignedBigInteger(cn::OBJECTIVES_MAPPINGS_GRADE_ID_COL);
            $table->unsignedBigInteger(cn::OBJECTIVES_MAPPINGS_SUBJECT_ID_COL);
            $table->unsignedBigInteger(cn::OBJECTIVES_MAPPINGS_STRAND_ID_COL);
            $table->unsignedBigInteger(cn::OBJECTIVES_MAPPINGS_LEARNING_UNIT_ID_COL);
            $table->unsignedBigInteger(cn::OBJECTIVES_MAPPINGS_LEARNING_OBJECTIVES_ID_COL);

            $table->foreign(cn::OBJECTIVES_MAPPINGS_GRADE_ID_COL)->references(cn::GRADES_ID_COL)->on(cn::GRADES_TABLE_NAME)->onDelete('cascade');
            $table->foreign(cn::OBJECTIVES_MAPPINGS_SUBJECT_ID_COL)->references(cn::SUBJECTS_ID_COL)->on(cn::SUBJECTS_TABLE_NAME)->onDelete('cascade');
            $table->foreign(cn::OBJECTIVES_MAPPINGS_STRAND_ID_COL)->references(cn::STRANDS_ID_COL)->on(cn::STRANDS_TABLE_NAME)->onDelete('cascade');
            $table->foreign(cn::OBJECTIVES_MAPPINGS_LEARNING_UNIT_ID_COL)->references(cn::LEARNING_UNITS_ID_COL)->on(cn::LEARNING_UNITS_TABLE_NAME)->onDelete('cascade');
            $table->foreign(cn::OBJECTIVES_MAPPINGS_LEARNING_OBJECTIVES_ID_COL)->references(cn::LEARNING_OBJECTIVES_ID_COL)->on(cn::LEARNING_OBJECTIVES_TABLE_NAME)->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists(cn::OBJECTIVES_MAPPINGS_TABLE_NAME);
    }
}
