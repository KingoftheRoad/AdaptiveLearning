<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Constants\DbConstant as cn;

class CreateTableCurriculumYear extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(cn::CURRICULUM_YEAR_TABLE_NAME, function (Blueprint $table) {
            $table->bigIncrements(cn::CURRICULUM_YEAR_ID_COL);
            $table->string(cn::CURRICULUM_YEAR_YEAR_COL);
            $table->enum(cn::CURRICULUM_YEAR_STATUS_COL,['active','inactive'])->default('active');
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
        Schema::dropIfExists(cn::CURRICULUM_YEAR_TABLE_NAME);
    }
}
