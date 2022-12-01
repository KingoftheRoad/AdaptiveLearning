<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Constants\DbConstant As cn;

class CreateSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(cn::SETTINGS_TABLE_NAME, function (Blueprint $table) {
            $table->bigIncrements(cn::SETTINGS_ID_COL);
            $table->String(cn::SETTINGS_SITE_NAME_COL)->nullable();
            $table->String(cn::SETTINGS_SITE_URL_COL)->nullable();
            $table->String(cn::SETTINGS_EMAIL_COL)->nullable();
            $table->String(cn::SETTINGS_CONTACT_NUMBER_COL)->nullable();
            $table->longtext(cn::SETTINGS_FAV_ICON_COL)->nullable();
            $table->longtext(cn::SETTINGS_LOGO_IMAGE_COL)->nullable();
            $table->string(cn::SETTINGS_SMTP_DRIVER_COL)->nullable();
            $table->string(cn::SETTINGS_SMTP_HOST_COL)->nullable();
            $table->string(cn::SETTINGS_SMTP_PORT_COL)->nullable();
            $table->string(cn::SETTINGS_SMTP_USERNAME_COL)->nullable();
            $table->string(cn::SETTINGS_SMTP_EMAIL_COL)->nullable();
            $table->string(cn::SETTINGS_SMTP_PASSWORD_COL)->nullable();
            $table->string(cn::SETTINGS_SMTP_ENCRYPTION_COL)->nullable();
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
        Schema::dropIfExists(cn::SETTINGS_TABLE_NAME);
    }
}
