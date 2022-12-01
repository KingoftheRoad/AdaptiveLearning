<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Constants\DbConstant as cn;

class CreatePeerGroupMembersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(cn::PEER_GROUP_MEMBERS_TABLE, function (Blueprint $table) {
            $table->engine = cn::DB_ENGINE_NAME;
            $table->bigIncrements(cn::PEER_GROUP_MEMBERS_ID_COL);
            $table->unsignedBigInteger(cn::PEER_GROUP_MEMBERS_PEER_GROUP_ID_COL);
            $table->unsignedBigInteger(cn::PEER_GROUP_MEMBERS_MEMBER_ID_COL);
            $table->boolean(cn::PEER_GROUP_MEMBERS_STATUS_COL)->default(1)->comment('0 = Pending, 1 = Active, 2 = InActive, 3 = blocked');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign(cn::PEER_GROUP_MEMBERS_PEER_GROUP_ID_COL)->references(cn::PEER_GROUP_ID_COL)->on(cn::PEER_GROUP_TABLE_NAME)->onDelete(cn::DB_ENGINE_ONDELETE_NAME);
            $table->foreign(cn::PEER_GROUP_MEMBERS_MEMBER_ID_COL)->references(cn::USERS_ID_COL)->on(cn::USERS_TABLE_NAME)->onDelete(cn::DB_ENGINE_ONDELETE_NAME);
        });
        
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists(cn::PEER_GROUP_MEMBERS_TABLE);
    }
}
