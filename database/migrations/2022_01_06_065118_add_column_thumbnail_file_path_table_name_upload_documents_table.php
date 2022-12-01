<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Constants\DbConstant as cn;

class AddColumnThumbnailFilePathTableNameUploadDocumentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table(cn::UPLOAD_DOCUMENTS_TABLE_NAME, function (Blueprint $table) {
            $table->after(cn::UPLOAD_DOCUMENTS_FILE_PATH_COL, function($table){
                $table->longText(cn::UPLOAD_DOCUMENTS_THUMBNAIL_FILE_PATH_COL)->nullable();
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
        Schema::table('upload_documents', function (Blueprint $table) {
            //
        });
    }
}
