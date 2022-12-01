<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Constants\DbConstant as cn;

class AddCurriculumYearMappingIdColInUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table(cn::USERS_TABLE_NAME, function (Blueprint $table) {
            $table->after(cn::USERS_CURRICULUM_YEAR_ID_COL, function($table){
                $table->unsignedBigInteger('curriculum_year_mapping_id')->nullable();
                $table->foreign('curriculum_year_mapping_id')->references(cn::CURRICULUM_YEAR_STUDENT_MAPPING_ID_COL)->on(cn::CURRICULUM_YEAR_STUDENT_MAPPING_TABLE)->onDelete(cn::DB_ENGINE_ONDELETE_NAME);
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
        Schema::table(cn::USERS_TABLE_NAME, function (Blueprint $table) {
            $table->dropForeign(['curriculum_year_mapping_id']);
            $table->dropColumn('curriculum_year_mapping_id');
        });
    }
}
