<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Constants\DbConstant as cn;

class CreateSubjectMappingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(cn::SUBJECT_MAPPING_TABLE_NAME, function (Blueprint $table) {
            $table->bigIncrements(cn::SUBJECT_MAPPING_ID_COL);
            $table->unsignedBigInteger(cn::SUBJECT_MAPPING_SCHOOL_ID_COL);
            $table->unsignedBigInteger(cn::SUBJECT_MAPPING_SUBJECT_ID_COL);
            $table->enum(cn::SUBJECT_MAPPING_STATUS_COL,['active','inactive'])->default('active');
            $table->timestamps();
            $table->softDeletes();

            // foreign key of school and subject table
            $table->foreign(cn::SUBJECT_MAPPING_SCHOOL_ID_COL)->references(cn::SCHOOL_ID_COLS)->on(cn::SCHOOL_TABLE_NAME)->onDelete(cn::DB_ENGINE_ONDELETE_NAME);
            $table->foreign(cn::SUBJECT_MAPPING_SUBJECT_ID_COL)->references(cn::SUBJECTS_ID_COL)->on(cn::SUBJECTS_TABLE_NAME)->onDelete(cn::DB_ENGINE_ONDELETE_NAME);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists(cn::SUBJECT_MAPPING_TABLE_NAME);
    }
}
