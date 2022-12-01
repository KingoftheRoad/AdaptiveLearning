<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Constants\DbConstant as cn;


class AddGeneralHitsColumnAndChangeColumnQuestionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table(cn::QUESTION_TABLE_NAME, function (Blueprint $table) {
            $table->after('general_hints', function($table){
                $table->longText(cn::QUESTION_GENERAL_HINTS_CH)->nullable();
            });
            $table->renameColumn('general_hints', cn::QUESTION_GENERAL_HINTS_EN);
            
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
