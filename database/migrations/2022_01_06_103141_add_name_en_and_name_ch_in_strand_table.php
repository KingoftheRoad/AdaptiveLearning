<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Constants\DbConstant as cn;

class AddNameEnAndNameChInStrandTable extends Migration
{
    public function up()
    {
        Schema::table(cn::STRANDS_TABLE_NAME, function (Blueprint $table) {
            $table->after(cn::STRANDS_NAME_COL, function($table){
                $table->String(cn::STRANDS_NAME_EN_COL)->nullable();
                $table->String(cn::STRANDS_NAME_CH_COL)->nullable();
            });
        });
    }

    public function down()
    {
       //
    }
}
