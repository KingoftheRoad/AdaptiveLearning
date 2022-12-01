<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Constants\DbConstant as cn;

class CreateClassSubjectMappingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(cn::CLASS_SUBJECT_MAPPING_TABLE_NAME, function (Blueprint $table) {
            $table->engine = cn::DB_ENGINE_NAME;
            $table->bigIncrements(cn::CLASS_SUBJECT_MAPPING_ID_COL);
            $table->unsignedBigInteger(cn::CLASS_SUBJECT_MAPPING_SUBJECT_ID_COL);
            $table->unsignedBigInteger(cn::CLASS_SUBJECT_MAPPING_CLASS_ID_COL);
            $table->unsignedTinyInteger(cn::CLASS_SUBJECT_MAPPING_STATUS_COL)->default(1)->comment('1 = Active, 0 = InActive');
            $table->timestamps();
            $table->softDeletes();

            // Set foreignkey between class_subject_mapping table & subjects table
            $table->foreign(cn::CLASS_SUBJECT_MAPPING_SUBJECT_ID_COL)->references(cn::SUBJECTS_ID_COL)->on(cn::SUBJECTS_TABLE_NAME)->onDelete(cn::DB_ENGINE_ONDELETE_NAME);

            // Set foreignkey between class_subject_mapping table & class table
            $table->foreign(cn::CLASS_SUBJECT_MAPPING_CLASS_ID_COL)->references(cn::GRADES_ID_COL)->on(cn::GRADES_TABLE_NAME)->onDelete(cn::DB_ENGINE_ONDELETE_NAME);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists(cn::CLASS_SUBJECT_MAPPING_TABLE_NAME);
    }
}
