<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Constants\DbConstant as cn;

class CreateAuditLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(cn::AUDIT_LOGS_TABLE_NAME, function (Blueprint $table) {
            $table->bigIncrements(cn::AUDIT_LOGS_ID_COL);
            $table->string(cn::AUDIT_LOGS_ROLE_TYPE_COL)->nullable();
            $table->unsignedBigInteger(cn::AUDIT_LOGS_USER_ID_COL)->default('0');
            $table->longtext(cn::AUDIT_LOGS_NAME_COL)->nullable();
            $table->longtext(cn::AUDIT_LOGS_PAYLOAD_COL)->nullable();
            $table->string(cn::AUDIT_LOGS_TABLE_NAME_COL,50)->nullable();
            $table->string(cn::AUDIT_LOGS_PAGE_NAME_COL,50)->nullable();
            $table->string(cn::AUDIT_LOGS_IP_ADDRESS_COL,30)->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->foreign(cn::AUDIT_LOGS_USER_ID_COL)->references(cn::USERS_ID_COL)->on(cn::USERS_TABLE_NAME)->onDelete(cn::DB_ENGINE_ONDELETE_NAME);

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists(cn::AUDIT_LOGS_TABLE_NAME);
    }
}
