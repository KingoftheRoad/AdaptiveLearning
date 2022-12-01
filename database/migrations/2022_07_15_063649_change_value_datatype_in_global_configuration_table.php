<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Constants\DbConstant as cn;

class ChangeValueDatatypeInGlobalConfigurationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table(cn::GLOBAL_CONFIGURATION_TABLE_NAME, function (Blueprint $table) {
            $table->text(cn::GLOBAL_CONFIGURATION_VALUE_COL)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table(cn::GLOBAL_CONFIGURATION_TABLE_NAME, function (Blueprint $table) {
            $table->text(cn::GLOBAL_CONFIGURATION_VALUE_COL)->change();
        });
    }
}
