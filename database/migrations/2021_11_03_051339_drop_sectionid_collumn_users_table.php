<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Constants\DbConstant as cn;

class DropSectionidCollumnUsersTable extends Migration
{
    public function up(){
        Schema::table(cn::USERS_TABLE_NAME, function($table) {
            $table->dropColumn('section_id');
        });
    }

    public function down()
    {
        //
    }
}
