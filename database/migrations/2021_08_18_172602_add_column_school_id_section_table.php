<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Constants\DbConstant as cn;

class AddColumnSchoolIdSectionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasColumn(cn::SECTION_TABLE_NAME, cn::SECTION_SCHOOL_ID_COL)){
            Schema::table(cn::SECTION_TABLE_NAME, function (Blueprint $table) {
                $table->dropColumn(cn::SECTION_SCHOOL_ID_COL);
            });
        }
        
        Schema::table(cn::SECTION_TABLE_NAME, function (Blueprint $table) {
            $table->after(cn::SECTION_ID_COL, function($table){
                $table->bigInteger(cn::SECTION_SCHOOL_ID_COL)->nullable();
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
        Schema::table(cn::SECTION_TABLE_NAME, function (Blueprint $table) {
            $table->dropColumn(cn::SECTION_SCHOOL_ID_COL);
        });
    }
}
