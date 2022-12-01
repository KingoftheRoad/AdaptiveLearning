<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Constants\DbConstant as cn;

class AddColumnsNodesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table(cn::NODES_TABLE_NAME, function (Blueprint $table) {
            $table->renameColumn('node_title', cn::NODES_NODE_TITLE_EN_COL);
            $table->renameColumn('node_description', cn::NODES_DESCRIPTION_EN_COL);
            $table->renameColumn('weakness_name', cn::NODES_WEAKNESS_NAME_EN_COL);
        });

        Schema::table(cn::NODES_TABLE_NAME, function (Blueprint $table) {
            $table->after(cn::NODES_NODE_TITLE_EN_COL, function($table){
                $table->text(cn::NODES_NODE_TITLE_CH_COL)->nullable();
            });
            $table->after(cn::NODES_DESCRIPTION_EN_COL, function($table){
                $table->text(cn::NODES_DESCRIPTION_CH_COL)->nullable();
            });
            $table->after(cn::NODES_WEAKNESS_NAME_EN_COL, function($table){
                $table->text(cn::NODES_WEAKNESS_NAME_CH_COL)->nullable();
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
