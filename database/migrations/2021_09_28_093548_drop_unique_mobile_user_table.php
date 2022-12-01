<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Constants\DbConstant as cn;

class DropUniqueMobileUserTable extends Migration
{
   
    public function up()
    {
        Schema::table(cn::USERS_TABLE_NAME, function (Blueprint $table) {
            $table->dropUnique([cn::USERS_MOBILENO_COL]);
        });
    }
  
    public function down()
    {
        //
    }
}





