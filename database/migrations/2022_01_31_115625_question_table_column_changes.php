<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Constants\DbConstant as cn;

class QuestionTableColumnChanges extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasColumn(cn::QUESTION_TABLE_NAME, cn::QUESTION_GENERAL_HINTS_VIDEO_ID_EN)){
            Schema::table(cn::QUESTION_TABLE_NAME, function($table) {
                $table->dropColumn(cn::QUESTION_GENERAL_HINTS_VIDEO_ID_EN);
            });
        }

        Schema::table(cn::QUESTION_TABLE_NAME, function (Blueprint $table) {
            $table->after(cn::QUESTION_GENERAL_HINTS_CH, function($table){
                $table->unsignedBigInteger(cn::QUESTION_GENERAL_HINTS_VIDEO_ID_EN)->nullable();
            });
            $table->foreign(cn::QUESTION_GENERAL_HINTS_VIDEO_ID_EN)->references(cn::UPLOAD_DOCUMENTS_ID_COL)->on(cn::UPLOAD_DOCUMENTS_TABLE_NAME)->onDelete(cn::DB_ENGINE_ONDELETE_NAME);
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
