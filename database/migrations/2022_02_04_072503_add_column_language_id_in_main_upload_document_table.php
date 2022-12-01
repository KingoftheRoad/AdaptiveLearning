<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Constants\DbConstant as cn;

class AddColumnLanguageIdInMainUploadDocumentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table(cn::MAIN_UPLOAD_DOCUMENT_TABLE_NAME, function (Blueprint $table) {
            $table->after(cn::MAIN_UPLOAD_DOCUMENT_UPLOAD_BY_COL, function($table){
                $table->unsignedBigInteger(cn::MAIN_UPLOAD_DOCUMENT_LANGUAGE_ID)->nullable();
            });
            $table->foreign(cn::MAIN_UPLOAD_DOCUMENT_LANGUAGE_ID)->references(cn::LANGUAGES_ID_COL)->on(cn::LANGUAGES_TABLE_NAME)->onDelete(cn::DB_ENGINE_ONDELETE_NAME);
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
