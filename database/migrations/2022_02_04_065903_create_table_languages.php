<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Constants\DbConstant as cn;

class CreateTableLanguages extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(cn::LANGUAGES_TABLE_NAME, function (Blueprint $table) {
            $table->bigIncrements(cn::LANGUAGES_ID_COL);
            $table->String(cn::LANGUAGES_NAME_COL);
            $table->String(cn::LANGUAGES_CODE_COL);
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
        Schema::dropIfExists(cn::LANGUAGES_TABLE_NAME);
    }
}
