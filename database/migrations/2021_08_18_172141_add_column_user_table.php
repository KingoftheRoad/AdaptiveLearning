<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Constants\DbConstant as cn;

class AddColumnUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasColumn(cn::USERS_TABLE_NAME, cn::USERS_MOBILENO_COL)){
            Schema::table(cn::USERS_TABLE_NAME, function (Blueprint $table) {
                $table->dropColumn(cn::USERS_MOBILENO_COL);
            });
        }

        if (Schema::hasColumn(cn::USERS_TABLE_NAME, cn::USERS_ADDRESS_COL)){
            Schema::table(cn::USERS_TABLE_NAME, function (Blueprint $table) {
                $table->dropColumn(cn::USERS_ADDRESS_COL);
            });
        }

        if (Schema::hasColumn(cn::USERS_TABLE_NAME, cn::USERS_GENDER_COL)){
            Schema::table(cn::USERS_TABLE_NAME, function (Blueprint $table) {
                $table->dropColumn(cn::USERS_GENDER_COL);
            });
        }
        if (Schema::hasColumn(cn::USERS_TABLE_NAME, cn::USERS_DATE_OF_BIRTH_COL)){
            Schema::table(cn::USERS_TABLE_NAME, function (Blueprint $table) {
                $table->dropColumn(cn::USERS_DATE_OF_BIRTH_COL);
            });
        }
        if (Schema::hasColumn(cn::USERS_TABLE_NAME, cn::USERS_CITY_COL)){
            Schema::table(cn::USERS_TABLE_NAME, function (Blueprint $table) {
                $table->dropColumn(cn::USERS_CITY_COL);
            });
        }
        if (Schema::hasColumn(cn::USERS_TABLE_NAME, 'section_id')){
            Schema::table(cn::USERS_TABLE_NAME, function (Blueprint $table) {
                $table->dropColumn('section_id');
            });
        }
        if (Schema::hasColumn(cn::USERS_TABLE_NAME, cn::USERS_CLASS_ID_COL)){
            Schema::table(cn::USERS_TABLE_NAME, function (Blueprint $table) {
                $table->dropColumn(cn::USERS_CLASS_ID_COL);
            });
        }
        if (Schema::hasColumn(cn::USERS_TABLE_NAME, cn::USERS_SCHOOL_ID_COL)){
            Schema::table(cn::USERS_TABLE_NAME, function (Blueprint $table) {
                $table->dropColumn(cn::USERS_SCHOOL_ID_COL);
            });
        }

        
        Schema::table(cn::USERS_TABLE_NAME, function (Blueprint $table) {
            $table->after(cn::USERS_ROLE_ID_COL, function($table){
                $table->bigInteger(cn::USERS_CLASS_ID_COL)->nullable();
                $table->bigInteger('section_id')->nullable();
                $table->bigInteger(cn::USERS_SCHOOL_ID_COL)->nullable();
            });
            $table->after(cn::USERS_EMAIL_COL, function($table){
                $table->smallInteger(cn::USERS_MOBILENO_COL)->nullable()->unique();
                $table->longText(cn::USERS_ADDRESS_COL)->nullable();
                $table->enum(cn::USERS_GENDER_COL, ['male', 'female','other'])->nullable();
                $table->string(cn::USERS_CITY_COL)->nullable();
                $table->date(cn::USERS_DATE_OF_BIRTH_COL)->nullable();
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
            $table->dropColumn(cn::USERS_CLASS_ID_COL);
            $table->dropColumn('section_id');
            $table->dropColumn(cn::USERS_SCHOOL_ID_COL);
            $table->dropColumn(cn::USERS_MOBILENO_COL);
            $table->dropColumn(cn::USERS_GENDER_COL);
            $table->dropColumn(cn::USERS_CITY_COL);
            $table->dropColumn(cn::USERS_DATE_OF_BIRTH_COL);
        });
    }
}
