<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Constants\DbConstant as cn;

class CreateOtherRoleTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(cn::OTHER_ROLE_TABLE_NAME, function (Blueprint $table) {
            $table->bigIncrements(cn::OTHER_ROLE_ID_COL);
            $table->text(cn::OTHER_ROLE_NAME_COL);
            $table->enum(cn::OTHER_ROLE_ACTIVE_STATUS_COL,['active','inactive'])->default('active'); 
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
        Schema::dropIfExists(cn::OTHER_ROLE_TABLE_NAME);
    }
}
