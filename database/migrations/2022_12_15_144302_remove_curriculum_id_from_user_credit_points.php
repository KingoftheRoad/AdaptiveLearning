<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Constants\DbConstant as cn;
class RemoveCurriculumIdFromUserCreditPoints extends Migration
{

    public function up()
    {
        Schema::table(cn::USER_CREDIT_POINTS_TABLE, function (Blueprint $table) {
            if (Schema::hasColumn(cn::USER_CREDIT_POINTS_TABLE, 'curriculum_year_id')){
                Schema::table(cn::USER_CREDIT_POINTS_TABLE, function($table) {
                    $table->dropForeign(['curriculum_year_id']);
                    $table->dropColumn('curriculum_year_id');
                });
            }
        });
    }

    public function down()
    {
        Schema::table(cn::USER_CREDIT_POINTS_TABLE, function (Blueprint $table) {
            $table->after(cn::USER_CREDIT_POINTS_ID_COL, function($table){
                $table->unsignedBigInteger('curriculum_year_id')->nullable();
                $table->foreign('curriculum_year_id')->references(cn::CURRICULUM_YEAR_ID_COL)->on(cn::CURRICULUM_YEAR_TABLE_NAME)->onDelete(cn::DB_ENGINE_ONDELETE_NAME);
            });
        });
    }
}
