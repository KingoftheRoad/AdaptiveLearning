<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Constants\DbConstant as cn;

class AddClassIdInUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table(cn::USERS_TABLE_NAME, function (Blueprint $table) {
            $table->after(cn::USERS_GRADE_ID_COL, function($table){
                $table->unsignedBigInteger(cn::USERS_CLASS_ID_COL)->nullable();
                $table->foreign(cn::USERS_CLASS_ID_COL)->references(cn::GRADE_CLASS_MAPPING_ID_COL)->on(cn::GRADE_CLASS_MAPPING_TABLE_NAME)->onDelete(cn::DB_ENGINE_ONDELETE_NAME);
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
            $table->dropForeign([cn::USERS_CLASS_ID_COL]);
            $table->dropColumn(cn::USERS_CLASS_ID_COL);
        });
    }
}
