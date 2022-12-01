<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Constants\DbConstant as cn;

class AddSchoolIdColClassSubjectMappingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table(cn::CLASS_SUBJECT_MAPPING_TABLE_NAME, function (Blueprint $table) {
            $table->after(cn::CLASS_SUBJECT_MAPPING_CLASS_ID_COL, function($table){
                $table->unsignedBigInteger(cn::CLASS_SUBJECT_MAPPING_SCHOOL_ID_COL)->nullable();
                $table->foreign(cn::CLASS_SUBJECT_MAPPING_SCHOOL_ID_COL)->nullable()->references(cn::SCHOOL_ID_COLS)->on(cn::SCHOOL_TABLE_NAME)->onDelete(cn::DB_ENGINE_ONDELETE_NAME);
            });
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
       
    }
}
