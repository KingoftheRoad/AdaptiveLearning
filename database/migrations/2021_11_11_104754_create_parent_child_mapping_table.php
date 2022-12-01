<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Constants\DbConstant as cn;

class CreateParentChildMappingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(cn::PARANT_CHILD_MAPPING_TABLE_NAME, function (Blueprint $table) {
            $table->engine = cn::DB_ENGINE_NAME;
            $table->bigIncrements(cn::PARANT_CHILD_MAPPING_ID_COL);
            $table->unsignedBigInteger(cn::PARANT_CHILD_MAPPING_PARENT_ID_COL);
            $table->unsignedBigInteger(cn::PARANT_CHILD_MAPPING_STUDENT_ID_COL);
            
            $table->foreign(cn::PARANT_CHILD_MAPPING_STUDENT_ID_COL)->references(cn::USERS_ID_COL)->on(cn::USERS_TABLE_NAME)->onDelete(cn::DB_ENGINE_ONDELETE_NAME);

            $table->foreign(cn::PARANT_CHILD_MAPPING_PARENT_ID_COL)->references(cn::USERS_ID_COL)->on(cn::USERS_TABLE_NAME)->onDelete(cn::DB_ENGINE_ONDELETE_NAME);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists(cn::PARANT_CHILD_MAPPING_TABLE_NAME);
    }
}
