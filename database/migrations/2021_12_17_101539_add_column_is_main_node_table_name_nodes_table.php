<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Constants\DbConstant as cn;

class AddColumnIsMainNodeTableNameNodesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table(cn::NODES_TABLE_NAME,function(Blueprint $table){
            $table->after('weakness_name',function($table){
                $table->tinyInteger(cn::NODES_IS_MAIN_NODE_COL)->default(0)->nullable();
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
        Schema::table('nodes', function (Blueprint $table) {
            //
        });
    }
}
