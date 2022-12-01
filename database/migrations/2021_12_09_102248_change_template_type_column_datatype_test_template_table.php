<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Constants\DbConstant as cn;

class ChangeTemplateTypeColumnDatatypeTestTemplateTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasColumn(cn::TEST_TEMPLATE_TABLE_NAME, cn::TEST_TEMPLATE_TYPE)){
            Schema::table(cn::TEST_TEMPLATE_TABLE_NAME, function (Blueprint $table) {
                $table->dropColumn(cn::TEST_TEMPLATE_TYPE);
            });
        }
        
        Schema::table(cn::TEST_TEMPLATE_TABLE_NAME, function (Blueprint $table) {
            $table->after(cn::TEST_TEMPLATE_NAME_COL, function($table){
                $table->enum(cn::TEST_TEMPLATE_TYPE,[1,2,3])->comment('1 = Self-Learning, 2 = Excercise, 3 = Test')->nullable();
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
