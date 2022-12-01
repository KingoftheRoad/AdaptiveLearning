<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Constants\DbConstant as cn;

class AddenumtypeInUploadDocumentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table(cn::UPLOAD_DOCUMENTS_TABLE_NAME, function (Blueprint $table) {
            DB::statement("ALTER TABLE ".cn::UPLOAD_DOCUMENTS_TABLE_NAME ." CHANGE COLUMN ".cn::UPLOAD_DOCUMENTS_FILE_TYPE_COL." ".cn::UPLOAD_DOCUMENTS_FILE_TYPE_COL." ENUM('pdf','jpg','png','jpeg','ppt','doc','docx','txt','xls','csv','mp4','mp3','3gp','avi','vob','flv','webm','wmv','ogg','mpeg','mov','m4p','wav','webm','aiff','aac','pptx','url') ");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table(cn::UPLOAD_DOCUMENTS_TABLE_NAME, function (Blueprint $table) {
            //
        });
    }
}
