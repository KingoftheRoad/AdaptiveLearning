<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Constants\DbConstant as cn;

class AddColumnMarksQuestion extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table(cn::QUESTION_TABLE_NAME, function (Blueprint $table) {
            $table->after(cn::QUESTION_QUESTION_UNIQUE_CODE_COL, function($table){
                $table->unsignedBigInteger(cn::QUESTION_MARKS_COL)->nullable();
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
        Schema::dropIfExists(cn::QUESTION_TABLE_NAME);
    }
}
