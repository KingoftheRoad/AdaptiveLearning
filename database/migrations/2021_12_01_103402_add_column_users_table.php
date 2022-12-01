<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Constants\DbConstant as cn;

class AddColumnUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Remove existing column into users table
        if (Schema::hasColumn(cn::USERS_TABLE_NAME, cn::USERS_NAME_EN_COL)){
            Schema::table(cn::USERS_TABLE_NAME, function (Blueprint $table) {
                $table->dropColumn(cn::USERS_NAME_EN_COL);
            });
        }
        if (Schema::hasColumn(cn::USERS_TABLE_NAME, cn::USERS_NAME_CH_COL)){
            Schema::table(cn::USERS_TABLE_NAME, function (Blueprint $table) {
                $table->dropColumn(cn::USERS_NAME_CH_COL);
            });
        }
        if (Schema::hasColumn(cn::USERS_TABLE_NAME, cn::USERS_STUDENT_NUMBER)){
            Schema::table(cn::USERS_TABLE_NAME, function (Blueprint $table) {
                $table->dropColumn(cn::USERS_STUDENT_NUMBER);
            });
        }
        if (Schema::hasColumn(cn::USERS_TABLE_NAME, cn::USERS_CLASS_CLASS_STUDENT_NUMBER)){
            Schema::table(cn::USERS_TABLE_NAME, function (Blueprint $table) {
                $table->dropColumn(cn::USERS_CLASS_CLASS_STUDENT_NUMBER);
            });
        }

        // Add new columns into user table
        Schema::table(cn::USERS_TABLE_NAME, function (Blueprint $table) {
            $table->after(cn::USERS_NAME_COL, function($table){
                $table->string(cn::USERS_NAME_EN_COL)->nullable();
                $table->string(cn::USERS_NAME_CH_COL)->nullable();
            });
            $table->after(cn::USERS_SCHOOL_ID_COL, function($table){
                $table->string(cn::USERS_STUDENT_NUMBER)->nullable();
                $table->string(cn::USERS_CLASS_CLASS_STUDENT_NUMBER)->nullable()->comment('class + class student number');
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
            $table->dropColumn(cn::USERS_NAME_EN_COL);
            $table->dropColumn(cn::USERS_NAME_CH_COL);
            $table->dropColumn(cn::USERS_STUDENT_NUMBER);
            $table->dropColumn(cn::USERS_CLASS_CLASS_STUDENT_NUMBER);
        });
    }
}
