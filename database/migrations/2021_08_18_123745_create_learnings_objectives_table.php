<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Constants\DbConstant as cn;

class CreateLearningsObjectivesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(cn::LEARNING_OBJECTIVES_TABLE_NAME, function (Blueprint $table) {
            $table->engine = cn::DB_ENGINE_NAME;
            $table->bigIncrements(cn::LEARNING_OBJECTIVES_ID_COL);
            $table->mediumText(cn::LEARNING_OBJECTIVES_STUDY_FOCI_COL);
            $table->longText(cn::LEARNING_OBJECTIVES_TITLE_COL);
            $table->unsignedTinyInteger(cn::LEARNING_UNITS_STATUS_COL)->default(1)->comment('1 = Active, 0 = InActive');
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
        Schema::dropIfExists(cn::LEARNING_OBJECTIVES_TABLE_NAME);
    }
}
