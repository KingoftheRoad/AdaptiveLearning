<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Constants\DbConstant as cn;

class AddGroupNameColInPeerGroupTable extends Migration
{
    public function up()
    {
        Schema::table(cn::PEER_GROUP_TABLE_NAME, function (Blueprint $table) {
            $table->after(cn::PEER_GROUP_SCHOOL_ID_COL,function($table){
                $table->string(cn::PEER_GROUP_GROUP_NAME_COL)->nullable();
            });
        });
    }

    public function down(){
        Schema::table(cn::PEER_GROUP_TABLE_NAME, function (Blueprint $table) {
            $table->dropColumn(cn::PEER_GROUP_GROUP_NAME_COL);
        });
    }
    
}
