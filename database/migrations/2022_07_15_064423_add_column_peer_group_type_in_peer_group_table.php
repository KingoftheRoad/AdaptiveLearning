<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Constants\DbConstant as cn;

class AddColumnPeerGroupTypeInPeerGroupTable extends Migration
{
    public function up(){
        Schema::table(cn::PEER_GROUP_TABLE_NAME, function (Blueprint $table) {
            $table->after(cn::PEER_GROUP_SUBJECT_ID_COL,function($table){
                $table->enum(cn::PEER_GROUP_CREATED_TYPE_COL,['auto','manual'])->default('manual')->comment('auto','manual');
                $table->enum(cn::PEER_GROUP_AUTO_GROUP_BY_COL,[0,1])->nullable()->comment('0- Round Robin  1- Sequence');
            });
        });
    }

    public function down(){
        Schema::table(cn::PEER_GROUP_TABLE_NAME, function (Blueprint $table) {
            $table->dropColumn(cn::PEER_GROUP_CREATED_TYPE_COL);
            $table->dropColumn(cn::PEER_GROUP_AUTO_GROUP_BY_COL);
        });
    }
}
