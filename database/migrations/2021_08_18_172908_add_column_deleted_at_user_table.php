<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Constants\DbConstant as cn;

class AddColumnDeletedAtUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasColumn(cn::USERS_TABLE_NAME, cn::USERS_DELETED_AT_COL)){
            Schema::table(cn::USERS_TABLE_NAME, function (Blueprint $table) {
                $table->dropColumn(cn::USERS_DELETED_AT_COL);
            });
        }
        
        Schema::table(cn::USERS_TABLE_NAME, function (Blueprint $table) {
            $table->after(cn::USERS_UPDATED_AT_COL, function($table){
                $table->dateTime(cn::USERS_DELETED_AT_COL)->nullable();
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
            $table->dropColumn(cn::USERS_DELETED_AT_COL);
        });
    }
}
