<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Constants\DbConstant as cn;

class DropColumnMainNodeIdNodesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasColumn(cn::NODES_TABLE_NAME, cn::NODES_MAIN_ID_COL) && Schema::hasColumn(cn::NODES_TABLE_NAME, cn::NODES_FIRST_MAIN_ID_COL)){
            Schema::table(cn::NODES_TABLE_NAME, function (Blueprint $table) {
                $table->dropColumn(cn::NODES_MAIN_ID_COL);
                $table->dropColumn(cn::NODES_FIRST_MAIN_ID_COL);
            });
        }
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
