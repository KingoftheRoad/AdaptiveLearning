<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Constants\DbConstant as cn;

class CreatePasswordResetsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(cn::PASSWORD_RESETS_TABLE_NAME, function (Blueprint $table) {
            $table->string(cn::PASSWORD_RESETS_EMAIL_COL)->index();
            $table->string(cn::PASSWORD_RESETS_TOKEN_COL);
            $table->timestamp(cn::PASSWORD_RESETS_CREATED_AT_COL)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists(cn::PASSWORD_RESETS_TABLE_NAME);
    }
}
