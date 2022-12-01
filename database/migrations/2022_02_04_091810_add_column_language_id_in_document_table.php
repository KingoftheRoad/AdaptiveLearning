<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Constants\DbConstant as cn;

class AddColumnLanguageIdInDocumentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table(cn::UPLOAD_DOCUMENTS_TABLE_NAME, function (Blueprint $table) {
            $table->after(cn::UPLOAD_DOCUMENTS_THUMBNAIL_FILE_PATH_COL, function($table){
                $table->unsignedBigInteger(cn::UPLOAD_DOCUMENTS_LANGUAGE_ID)->nullable();
            });
            $table->foreign(cn::UPLOAD_DOCUMENTS_LANGUAGE_ID)->references(cn::LANGUAGES_ID_COL)->on(cn::LANGUAGES_TABLE_NAME)->onDelete(cn::DB_ENGINE_ONDELETE_NAME);
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
