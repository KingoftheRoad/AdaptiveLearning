<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Constants\DbConstant as cn;
use Illuminate\Support\Facades\Artisan;

class CreateTeachersSubjectTimetableTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(cn::SUBJECT_ASSIGN_TABLE_NAME, function (Blueprint $table) {
            $table->bigIncrements(cn::SUBJECT_ASSIGN_ID_COL);
            $table->bigInteger(cn::SUBJECT_ASSIGN_ASSIGN_ID_COL)->nullable();
            $table->bigInteger(cn::SUBJECT_ASSIGN_SUBJECT_ID_COL)->nullable();
            $table->string(cn::SUBJECT_ASSIGN_WEEKDAY_COL)->nullable();
            $table->string(cn::SUBJECT_ASSIGN_START_TIME_COL)->nullable();
            $table->string(cn::SUBJECT_ASSIGN_END_TIME_COL)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists(cn::SUBJECT_ASSIGN_TABLE_NAME);
    }
}
