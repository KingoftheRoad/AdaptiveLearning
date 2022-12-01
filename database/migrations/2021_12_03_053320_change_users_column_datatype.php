<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Constants\DbConstant as cn;

class ChangeUsersColumnDatatype extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table(cn::USERS_TABLE_NAME, function ($table) {
            $table->longtext(cn::USERS_NAME_EN_COL)->change();
            $table->longtext(cn::USERS_NAME_CH_COL)->change();
            $table->longtext(cn::USERS_MOBILENO_COL)->change();
            $table->longtext(cn::USERS_GENDER_COL)->change();
            $table->longtext(cn::USERS_CITY_COL)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table(cn::USERS_TABLE_NAME, function ($table) {
            $table->longtext(cn::USERS_NAME_EN_COL)->change();
            $table->longtext(cn::USERS_NAME_CH_COL)->change();
            $table->longtext(cn::USERS_MOBILENO_COL)->change();
            $table->longtext(cn::USERS_GENDER_COL)->change();
            $table->longtext(cn::USERS_CITY_COL)->change();
        });
    }
}
