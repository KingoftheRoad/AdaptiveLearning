<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Constants\DbConstant as cn;

class AddColumnStatusModulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table(cn::MODULES_TABLE_NAME, function (Blueprint $table) {
            $table->after(cn::MODULES_MODULE_SLUG_COL, function($table){
            $table->enum(cn::MODULES_STATUS_COL, ['active', 'inactive'])->default('active');
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
