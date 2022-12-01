<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Constants\DbConstant as cn;

class CreateNodesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(cn::NODES_TABLE_NAME, function (Blueprint $table) {
            $table->bigIncrements(cn::NODES_NODE_ID_COL);
            $table->bigInteger(cn::NODES_SCHOOL_ID_COL)->nullable();
            $table->string(cn::NODES_NODEID_COL)->nullable();
            $table->string('node_title',50)->nullable();
            $table->longText('node_description')->nullable();
            $table->string('weakness_name')->nullable();
            $table->bigInteger(cn::NODES_CREATED_BY_COL)->nullable();
            $table->enum(cn::NODES_STATUS_COL,['active','inactive'])->default('active'); 
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists(cn::NODES_TABLE_NAME);
    }
}
