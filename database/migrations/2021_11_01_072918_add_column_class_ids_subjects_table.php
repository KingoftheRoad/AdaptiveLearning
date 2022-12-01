<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Constants\DbConstant as cn;
use Illuminate\Support\Facades\Artisan;

class AddColumnClassIdsSubjectsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table(cn::SUBJECTS_TABLE_NAME, function (Blueprint $table) {
            $table->after(cn::SUBJECTS_CODE_COL, function($table){
                $table->longtext(cn::SUBJECTS_CLASSIDS_COL)->nullable();
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
        Schema::table('subjects', function (Blueprint $table) {
            //
        });
    }
}
