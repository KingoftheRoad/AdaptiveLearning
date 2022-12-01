<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Constants\DbConstant as cn;

class ChangeExamStatusValueInExamTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table(cn::EXAM_TABLE_NAME, function (Blueprint $table) {
            \DB::statement("ALTER TABLE `exam` CHANGE `status` `status` ENUM('draft','pending','publish','active','inactive','complete') default 'draft';");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table(cn::EXAM_TABLE_NAME, function (Blueprint $table) {
            \DB::statement("ALTER TABLE `exam` CHANGE `status` `status` ENUM('pending','publish','active','inactive','complete') default 'pending';");
        });
    }
}
