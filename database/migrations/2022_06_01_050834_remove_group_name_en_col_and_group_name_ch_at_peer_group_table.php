<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Constants\DbConstant as cn;

class RemoveGroupNameEnColAndGroupNameChAtPeerGroupTable extends Migration
{
    public function up(){
        if (Schema::hasColumn(cn::PEER_GROUP_TABLE_NAME, cn::PEER_GROUP_GROUP_NAME_EN_COL)) //check the column
        {
            Schema::table(cn::PEER_GROUP_TABLE_NAME, function (Blueprint $table)
            {
                $table->dropColumn(cn::PEER_GROUP_GROUP_NAME_EN_COL);
            });
        }
        if (Schema::hasColumn(cn::PEER_GROUP_TABLE_NAME, cn::PEER_GROUP_GROUP_NAME_CH_COL)) //check the column
        {
            Schema::table(cn::PEER_GROUP_TABLE_NAME, function (Blueprint $table)
            {
                $table->dropColumn(cn::PEER_GROUP_GROUP_NAME_CH_COL); 
            });
        }
    }

    public function down(){
        //
    }
}
