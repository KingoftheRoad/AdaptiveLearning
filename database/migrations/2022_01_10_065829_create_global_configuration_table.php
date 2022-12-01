<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Constants\DbConstant as cn;

class CreateGlobalConfigurationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(cn::GLOBAL_CONFIGURATION_TABLE_NAME, function (Blueprint $table) {
            $table->id();
            $table->string(cn::GLOBAL_CONFIGURATION_KEY_COL)->nullable();
            $table->string(cn::GLOBAL_CONFIGURATION_VALUE_COL)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists(cn::GLOBAL_CONFIGURATION_TABLE_NAME);
    }
}
