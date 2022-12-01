<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Constants\DbConstant as cn;

class AddCodeColumnLearningObjectivesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table(cn::LEARNING_OBJECTIVES_TABLE_NAME, function (Blueprint $table) {
            $table->after(cn::LEARNING_OBJECTIVES_TITLE_COL, function($table){
                $table->string(cn::LEARNING_OBJECTIVES_CODE_COL)->nullable();
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
        //
    }
}
