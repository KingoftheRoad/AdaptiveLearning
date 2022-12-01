<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Constants\DbConstant as cn;

class CreateTableExam extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(cn::EXAM_TABLE_NAME, function (Blueprint $table) {
           $table->bigIncrements(cn::EXAM_TABLE_ID_COLS);
           $table->text(cn::EXAM_TABLE_TITLE_COLS);
           $table->dateTime(cn::EXAM_TABLE_FROM_DATE_COLS);
           $table->dateTime(cn::EXAM_TABLE_TO_DATE_COLS);
           $table->dateTime(cn::EXAM_TABLE_RESULT_DATE_COLS);
           $table->timestamp(cn::EXAM_TABLE_TIME_DURATIONS_COLS);
           $table->longText(cn::EXAM_TABLE_DESCRIPTION_COLS);
           $table->enum(cn::EXAM_TABLE_STATUS_COLS,['pending','active','inactive','complete'])->default('pending'); 
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
        Schema::dropIfExists(cn::EXAM_TABLE_NAME);
    }
}
