<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Constants\DbConstant as cn;

class CreateTablePeerGroups extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(cn::PEER_GROUP_TABLE_NAME, function (Blueprint $table) {
            $table->engine = cn::DB_ENGINE_NAME;
            $table->bigIncrements(cn::PEER_GROUP_ID_COL);
            $table->unsignedBigInteger(cn::PEER_GROUP_SCHOOL_ID_COL);
            $table->string(cn::PEER_GROUP_GROUP_NAME_EN_COL,100)->nullable();
            $table->String(cn::PEER_GROUP_GROUP_NAME_CH_COL,100)->nullable();
            $table->unsignedBigInteger(cn::PEER_GROUP_CREATED_BY_TEACHER_ID_COL)->nullable();
            $table->unsignedBigInteger(cn::PEER_GROUP_SUBJECT_ID_COL)->nullable();
            $table->boolean(cn::PEER_GROUP_STATUS_COL)->default(1)->comment('1 = Active, 0 = InActive');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign(cn::PEER_GROUP_SCHOOL_ID_COL)->references(cn::SCHOOL_ID_COLS)->on(cn::SCHOOL_TABLE_NAME)->onDelete(cn::DB_ENGINE_ONDELETE_NAME);
            $table->foreign(cn::PEER_GROUP_CREATED_BY_TEACHER_ID_COL)->references(cn::USERS_ID_COL)->on(cn::USERS_TABLE_NAME)->onDelete(cn::DB_ENGINE_ONDELETE_NAME);
            $table->foreign(cn::PEER_GROUP_SUBJECT_ID_COL)->references(cn::SUBJECTS_ID_COL)->on(cn::SUBJECTS_TABLE_NAME)->onDelete(cn::DB_ENGINE_ONDELETE_NAME);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists(cn::PEER_GROUP_TABLE_NAME);
    }
}
