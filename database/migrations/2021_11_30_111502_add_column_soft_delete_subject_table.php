<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Constants\DbConstant as cn;

class AddColumnSoftDeleteSubjectTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasColumn(cn::SUBJECTS_TABLE_NAME,cn::SUBJECTS_DELETED_AT_COL ))
        {
            Schema::table(cn::SUBJECTS_TABLE_NAME, function (Blueprint $table)
            {
                $table->dropColumn(cn::SUBJECTS_DELETED_AT_COL);
            });
        }
        
        Schema::table(cn::SUBJECTS_TABLE_NAME, function (Blueprint $table) {
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
       
    }
}
