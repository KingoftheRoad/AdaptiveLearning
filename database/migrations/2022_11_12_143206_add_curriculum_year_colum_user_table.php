<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Constants\DbConstant as cn;

class AddCurriculumYearColumUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //add curriculum_year_id in grades_school_mapping table
        Schema::table(cn::USERS_TABLE_NAME, function (Blueprint $table) {
            $table->after(cn::USERS_ID_COL, function($table){
                $table->unsignedBigInteger(cn::USERS_CURRICULUM_YEAR_ID_COL)->nullable();
                $table->foreign(cn::USERS_CURRICULUM_YEAR_ID_COL)->references(cn::CURRICULUM_YEAR_ID_COL)->on(cn::CURRICULUM_YEAR_TABLE_NAME)->onDelete(cn::DB_ENGINE_ONDELETE_NAME);
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
        //remove foreign key and remove curriculum year id column in user table
        Schema::table(cn::USERS_TABLE_NAME, function (Blueprint $table) {
            $table->dropForeign([cn::USERS_CURRICULUM_YEAR_ID_COL]);
            $table->dropColumn(cn::USERS_CURRICULUM_YEAR_ID_COL);
        });
    }
}
