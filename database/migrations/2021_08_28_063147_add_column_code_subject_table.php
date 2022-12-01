<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Constants\DbConstant as cn;

class AddColumnCodeSubjectTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasColumn(cn::SUBJECTS_TABLE_NAME,cn::SUBJECTS_CODE_COL)){
            Schema::table(cn::SUBJECTS_TABLE_NAME, function (Blueprint $table) {
                $table->dropColumn(cn::SUBJECTS_CODE_COL);            
            });
        }

        Schema::table(cn::SUBJECTS_TABLE_NAME, function (Blueprint $table) {
            $table->after(cn::SUBJECTS_NAME_COL, function($table){
                $table->string(cn::SUBJECTS_CODE_COL,20)->nullable();
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
    
    }
}
