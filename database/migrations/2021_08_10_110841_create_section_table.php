<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Constants\DbConstant as cn;
use Illuminate\Support\Facades\Artisan;

class CreateSectionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(cn::SECTION_TABLE_NAME, function (Blueprint $table) {
            $table->bigIncrements(cn::SECTION_ID_COL);
            $table->String(cn::SECTION_SECTION_NAME_COL);
            $table->boolean(cn::SECTION_ACTIVE_STATUS_COL);
            $table->integer(cn::SECTION_SCHOOL_ID_COL);
            $table->integer(cn::SECTION_CREATED_BY_COL);
            $table->integer(cn::SECTION_UPDATED_BY_COL);
            $table->timestamps();
            $table->softDeletes();
            $table->engine = cn::DB_ENGINE_NAME;
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists(cn::SECTION_TABLE_NAME);
    }
}
