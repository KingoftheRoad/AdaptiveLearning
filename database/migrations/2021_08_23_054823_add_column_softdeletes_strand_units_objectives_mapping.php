<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Constants\DbConstant as cn;

class AddColumnSoftdeletesStrandUnitsObjectivesMapping extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table(cn::OBJECTIVES_MAPPINGS_TABLE_NAME, function (Blueprint $table) {
            $table->after(cn::OBJECTIVES_MAPPINGS_LEARNING_OBJECTIVES_ID_COL, function($table){
                $table->softDeletes();
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
        Schema::dropIfExists(cn::OBJECTIVES_MAPPINGS_TABLE_NAME);
    }
}
