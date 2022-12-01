<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Constants\DbConstant as cn;

class AddColumnParentNodeIdNodesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table(cn::NODES_TABLE_NAME, function (Blueprint $table) {
            $table->after(cn::NODES_NODE_ID_COL, function($table){
                $table->bigInteger(cn::NODES_MAIN_ID_COL)->default(0)->nullable();
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
        //
    }
}
