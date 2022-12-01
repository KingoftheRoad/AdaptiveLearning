<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Constants\DbConstant as cn;

class CreateUserCreditPointsTable extends Migration
{
    public function up(){
        Schema::create(cn::USER_CREDIT_POINTS_TABLE, function (Blueprint $table) {
            $table->bigIncrements(cn::USER_CREDIT_POINTS_ID_COL);
            $table->unsignedBigInteger(cn::USER_CREDIT_USER_ID_COL)->nullable();
            $table->BigInteger(cn::USER_NO_OF_CREDIT_POINTS_COL)->nullable();
            $table->timestamps();
            $table->SoftDeletes();

            $table->foreign(cn::USER_CREDIT_USER_ID_COL)->references(cn::USERS_ID_COL)->on(cn::USERS_TABLE_NAME)->onDelete(cn::DB_ENGINE_ONDELETE_NAME);
        });
    }

    public function down(){
        Schema::dropIfExists(cn::USER_CREDIT_POINTS_TABLE);
    }
}
