<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Constants\DbConstant As cn;

class CreateModulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(cn::MODULES_TABLE_NAME, function (Blueprint $table) {
            $table->bigIncrements(cn::MODULES_ID_COL);
            $table->String(cn::MODULES_MODULE_NAME_COL)->nullable();
            $table->String(cn::MODULES_MODULE_SLUG_COL)->nullable();
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
        Schema::dropIfExists('modules');
    }
}
