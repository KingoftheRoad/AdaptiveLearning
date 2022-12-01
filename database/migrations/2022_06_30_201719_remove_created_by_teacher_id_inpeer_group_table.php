<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Constants\DbConstant as cn;

class RemoveCreatedByTeacherIdInpeerGroupTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasColumn(cn::PEER_GROUP_TABLE_NAME, 'created_by_teacher_id')){
            Schema::table(cn::PEER_GROUP_TABLE_NAME, function($table) {
                $table->dropForeign('peer_group_created_by_teacher_id_foreign');
                $table->dropColumn(cn::PEER_GROUP_CREATED_BY_TEACHER_ID_COL);
            });
        }

        Schema::table(cn::PEER_GROUP_TABLE_NAME, function (Blueprint $table){
            $table->after(cn::PEER_GROUP_SUBJECT_ID_COL,function($table){
                $table->unsignedBigInteger(cn::PEER_GROUP_CREATED_BY_USER_ID_COL)->nullable();
                $table->foreign(cn::PEER_GROUP_CREATED_BY_USER_ID_COL)->references(cn::USERS_ID_COL)->on(cn::USERS_TABLE_NAME)->onDelete(cn::DB_ENGINE_ONDELETE_NAME);
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
