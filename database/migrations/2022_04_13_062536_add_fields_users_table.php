<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Constants\DbConstant as cn;

class AddFieldsUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table(cn::USERS_TABLE_NAME, function (Blueprint $table) {
            $table->after(cn::USERS_SCHOOL_ID_COL,function($table){
                $table->String(cn::USERS_PERMANENT_REFERENCE_NUMBER)->nullable();
                $table->String(cn::STUDENT_NUMBER_WITHIN_CLASS)->nullable();
                $table->String(cn::USERS_CLASS)->nullable();
                $table->String(cn::USERS_CLASS_STUDENT_NUMBER)->nullable();
            });

            $table->after(cn::USERS_STATUS_COL,function($table){
                $table->timestamp(cn::USERS_IMPORT_DATE_COL)->nullable();
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
        Schema::table(cn::USERS_TABLE_NAME, function($table) {
            $table->dropColumn(cn::USERS_PERMANENT_REFERENCE_NUMBER);
            $table->dropColumn(cn::STUDENT_NUMBER_WITHIN_CLASS);
            $table->dropColumn(cn::USERS_CLASS);
            $table->dropColumn(cn::USERS_CLASS_STUDENT_NUMBER);
            $table->dropColumn(cn::USERS_IMPORT_DATE_COL);
        });
    }
}
