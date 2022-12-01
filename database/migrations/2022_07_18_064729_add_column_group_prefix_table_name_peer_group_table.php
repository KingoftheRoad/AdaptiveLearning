<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Constants\DbConstant as cn;

class AddColumnGroupPrefixTableNamePeerGroupTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table(cn::PEER_GROUP_TABLE_NAME, function (Blueprint $table) {
            $table->after(cn::PEER_GROUP_GROUP_NAME_COL,function($table){
                $table->string(cn::PEER_GROUP_GROUP_PREFIX_COL)->nullable();
            });
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table(cn::PEER_GROUP_TABLE_NAME, function (Blueprint $table) {
            $table->string(cn::PEER_GROUP_GROUP_PREFIX_COL)->nullable();
        });
    }
}
