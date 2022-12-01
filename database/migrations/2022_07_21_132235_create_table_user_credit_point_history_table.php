<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Constants\DbConstant as cn;

class CreateTableUserCreditPointHistoryTable extends Migration
{
    public function up(){
        Schema::create(cn::USER_CREDIT_POINT_HISTORY, function (Blueprint $table) {
            $table->bigIncrements(cn::USER_CREDIT_POINT_HISTORY_ID_COL);
            $table->unsignedBigInteger(cn::USER_CREDIT_POINT_HISTORY_EXAM_ID_COL)->nullable();
            $table->unsignedBigInteger(cn::USER_CREDIT_POINT_HISTORY_USER_ID_COL)->nullable();
            $table->enum(cn::USER_CREDIT_POINT_HISTORY_TEST_TYPE_COL,['assignment','self_learning'])->nullable();
            $table->enum(cn::USER_CREDIT_POINT_HISTORY_SELF_LEARNING_TYPE_COL,['test','exercise'])->nullable();
            $table->enum(cn::USER_CREDIT_POINT_HISTORY_CREDIT_POINT_TYPE_COL,['after_submission','accuracy_level','ability_level'])->nullable();
            $table->BigInteger(cn::USER_CREDIT_POINT_HISTORY_NO_OF_CREDIT_POINT_COL)->nullable();
            $table->longText(cn::USER_CREDIT_POINT_HISTORY_CREDIT_POINT_HISTORY_COL)->nullable();
            $table->timestamps();
            $table->SoftDeletes();

            $table->foreign(cn::USER_CREDIT_POINT_HISTORY_USER_ID_COL)->references(cn::USERS_ID_COL)->on(cn::USERS_TABLE_NAME)->onDelete(cn::DB_ENGINE_ONDELETE_NAME);
            $table->foreign(cn::USER_CREDIT_POINT_HISTORY_EXAM_ID_COL)->references(cn::EXAM_TABLE_ID_COLS)->on(cn::EXAM_TABLE_NAME)->onDelete(cn::DB_ENGINE_ONDELETE_NAME);
        });
    }

    public function down(){
        Schema::dropIfExists(cn::USER_CREDIT_POINT_HISTORY);
    }
}
