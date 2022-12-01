<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Constants\DbConstant as cn;

class AddNewFieldsSchoolTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table(cn::SCHOOL_TABLE_NAME, function (Blueprint $table) {
            $table->after(cn::SCHOOL_SCHOOL_NAME_COL, function($table){
                $table->String(cn::SCHOOL_SCHOOL_NAME_EN_COL)->nullable();
                $table->String(cn::SCHOOL_SCHOOL_NAME_CH_COL)->nullable();
            });

            $table->after(cn::SCHOOL_SCHOOL_ADDRESS, function($table){
                $table->longText(cn::SCHOOL_SCHOOL_ADDRESS_EN_COL)->nullable();
                $table->longText(cn::SCHOOL_SCHOOL_ADDRESS_CH_COL)->nullable();
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
        Schema::table(cn::SCHOOL_TABLE_NAME, function (Blueprint $table) {
            $table->dropColumn(cn::SCHOOL_SCHOOL_NAME_EN_COL);
            $table->dropColumn(cn::SCHOOL_SCHOOL_NAME_CH_COL);
            $table->dropColumn(cn::SCHOOL_SCHOOL_ADDRESS_EN_COL);
            $table->dropColumn(cn::SCHOOL_SCHOOL_ADDRESS_CH_COL);
        });
    }
}
