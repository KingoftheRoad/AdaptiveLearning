<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Constants\DbConstant as cn;

class AddColumnPermissionRoleTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table(cn::ROLES_TABLE_NAME, function (Blueprint $table) {
            $table->after(cn::ROLES_ROLE_SLUG_COL, function($table){
                $table->String(cn::ROLES_PERMISSION_COL,900)->nullable();
                $table->enum(cn::ROLES_STATUS_COL, ['active', 'inactive'])->default('active');
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
