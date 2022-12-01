<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Constants\DbConstant as cn;
use Illuminate\Support\Facades\Artisan;


class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(cn::USERS_TABLE_NAME, function (Blueprint $table) {
            $table->id();
            $table->string(cn::USERS_NAME_COL);
            $table->string(cn::USERS_EMAIL_COL)->unique();
            $table->string(cn::USERS_PASSWORD_COL);
            $table->bigInteger(cn::USERS_MOBILENO_COL);
            $table->String(cn::USERS_ADDRESS_COL);
            $table->Text(cn::USERS_GENDER_COL);
            $table->date(cn::USERS_DATE_OF_BIRTH_COL);
            $table->String(cn::USERS_CITY_COL);
            $table->bigInteger(cn::USERS_CLASS_ID_COL)->nullable();
            $table->bigInteger('section_id')->nullable();
            $table->bigInteger(cn::USERS_ROLE_ID_COL);
            $table->timestamp(cn::USERS_EMAIL_VERIFID_AT_COL)->nullable();
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists(cn::USERS_TABLE_NAME);
    }
}
