<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Constants\DbConstant as cn;
use Illuminate\Support\Facades\Artisan;

class CreateClassTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(cn::CLASS_TABLE_NAME, function (Blueprint $table) {
            $table->bigIncrements(cn::CLASS_ID_COL);
            $table->String(cn::CLASS_CLASS_NAME_COL);
            $table->tinyInteger(cn::CLASS_ACTIVE_STATUS_COL)->comment('0: InActive; 1: Active;');
            $table->integer(cn::CLASS_SCHOOL_ID_COL);
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
        Schema::dropIfExists(cn::CLASS_TABLE_NAME);
    }   
}
