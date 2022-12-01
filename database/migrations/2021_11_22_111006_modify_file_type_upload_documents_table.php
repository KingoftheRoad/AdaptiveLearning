<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Constants\DbConstant as cn;

class ModifyFileTypeUploadDocumentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table(cn::UPLOAD_DOCUMENTS_TABLE_NAME, function(Blueprint $table)
        {
            \DB::statement("ALTER TABLE `upload_documents` CHANGE `file_type` `file_type` ENUM('doc','docx','odt','pdf','xls','xlsx','ods','ppt','pptx','txt','png','jpg','jpeg','mp3','mp4') default NULL;");
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
