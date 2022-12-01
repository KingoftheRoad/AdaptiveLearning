<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Constants\DbConstant as cn;

class CreateNodeRelationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(cn::NODES_RELATION_TABLE_NAME, function (Blueprint $table) {
            $table->bigIncrements(cn::NODES_RELATION_ID_COL);
            $table->unsignedBigInteger(cn::NODES_RELATION_PARENT_NODE_ID_COL)->default(0)->nullable();
            $table->unsignedBigInteger(cn::NODES_RELATION_CHILD_NODE_ID_COL)->default(0)->nullable(); 
            $table->enum(cn::NODES_RELATION_STATUS,['active','inactive'])->default('active'); 
            $table->timestamps();
            $table->softDeletes();
            // Set foreign key "nodes table"
            $table->foreign(cn::NODES_RELATION_PARENT_NODE_ID_COL)->references(cn::NODES_NODE_ID_COL)->on(cn::NODES_TABLE_NAME)->onDelete(cn::DB_ENGINE_ONDELETE_NAME);
            $table->foreign(cn::NODES_RELATION_CHILD_NODE_ID_COL)->references(cn::NODES_NODE_ID_COL)->on(cn::NODES_TABLE_NAME)->onDelete(cn::DB_ENGINE_ONDELETE_NAME);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('node_relation');
    }
}
