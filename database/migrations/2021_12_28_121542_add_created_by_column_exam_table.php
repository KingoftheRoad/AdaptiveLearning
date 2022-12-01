<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Constants\DbConstant as cn;

class AddCreatedByColumnExamTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table(cn::EXAM_TABLE_NAME,function(Blueprint $table){
            $table->after(cn::EXAM_TABLE_TEMPLATE_ID,function($table){
                $table->unsignedBigInteger(cn::EXAM_TABLE_CREATED_BY_COL)->nullable();
                $table->foreign(cn::EXAM_TABLE_CREATED_BY_COL)->nullable()->references(cn::USERS_ID_COL)->on(cn::USERS_TABLE_NAME)->onDelete(cn::DB_ENGINE_ONDELETE_NAME);
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
