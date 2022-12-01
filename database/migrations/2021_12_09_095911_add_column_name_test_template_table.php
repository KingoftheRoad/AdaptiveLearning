<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Constants\DbConstant as cn;

class AddColumnNameTestTemplateTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table(cn::TEST_TEMPLATE_TABLE_NAME, function (Blueprint $table) {
            $table->after(cn::TEST_TEMPLATE_ID_COL, function($table){
                $table->string(cn::TEST_TEMPLATE_NAME_COL,50)->nullable();
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
