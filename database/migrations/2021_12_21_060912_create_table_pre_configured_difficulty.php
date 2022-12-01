<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Constants\Dbconstant as cn;

class CreateTablePreConfiguredDifficulty extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(cn::PRE_CONFIGURE_DIFFICULTY_TABLE_NAME, function (Blueprint $table) {
            $table->bigIncrements(cn::PRE_CONFIGURE_DIFFICULTY_ID_COL);
            $table->Integer(cn::PRE_CONFIGURE_DIFFICULTY_DIFFICULTY_LEVEL_COL);
            $table->String(cn::PRE_CONFIGURE_DIFFICULTY_TITLE_COL,50);
            $table->enum(cn::PRE_CONFIGURE_DIFFICULTY_STATUS_COL,['active','inactive'])->default('active');
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
        Schema::dropIfExists(cn::PRE_CONFIGURE_DIFFICULTY_TABLE_NAME);
    }
}
