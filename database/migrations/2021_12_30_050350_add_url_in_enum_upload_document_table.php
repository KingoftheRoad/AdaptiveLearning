<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Constants\DbConstant as cn;

class AddUrlInEnumUploadDocumentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table(cn::UPLOAD_DOCUMENTS_TABLE_NAME, function (Blueprint $table) {    
            DB::statement("ALTER TABLE ".cn::UPLOAD_DOCUMENTS_TABLE_NAME ." CHANGE COLUMN ".cn::UPLOAD_DOCUMENTS_FILE_TYPE_COL ." ". cn::UPLOAD_DOCUMENTS_FILE_TYPE_COL ." ENUM('doc','docx','odt','csv','pdf','xls','xlsx','ods','ppt','pptx','txt','png','pptx','txt','png','jpg','jpeg','mp3','mp4','url') ");
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
