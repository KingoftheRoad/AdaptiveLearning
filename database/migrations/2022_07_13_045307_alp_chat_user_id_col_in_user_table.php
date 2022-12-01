<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Constants\DbConstant as cn;

class AlpChatUserIdColInUserTable extends Migration{
   
    public function up(){
        Schema::table(cn::USERS_TABLE_NAME, function (Blueprint $table) {
            $table->after(cn::USERS_ID_COL,function($table){
                $table->String(cn::USERS_ALP_CHAT_USER_ID_COL)->nullable();
            });
        });
    }

    public function down(){
        Schema::table(cn::USERS_TABLE_NAME, function (Blueprint $table) {
            $table->dropColumn(cn::USERS_ALP_CHAT_USER_ID_COL);
        });
    }
}
