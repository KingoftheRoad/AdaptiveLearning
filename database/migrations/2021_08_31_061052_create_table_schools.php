<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Constants\DbConstant as cn;

class CreateTableSchools extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(cn::SCHOOL_TABLE_NAME, function (Blueprint $table) {
            $table->bigIncrements(cn::SCHOOL_ID_COLS);
            $table->String(cn::SCHOOL_SCHOOL_NAME_COL);
            $table->String(cn::SCHOOL_SCHOOL_CODE_COL);
            $table->String(cn::SCHOOL_SCHOOL_ADDRESS);
            $table->String(cn::SCHOOL_SCHOOL_CITY);
            $table->enum(cn::SCHOOL_SCHOOL_STATUS,['active','inactive'])->default('active'); 
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
        Schema::dropIfExists(cn::SCHOOL_TABLE_NAME);
    }
}
