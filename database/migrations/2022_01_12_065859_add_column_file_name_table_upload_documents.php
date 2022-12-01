<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Constants\DbConstant as cn;

class AddColumnFileNameTableUploadDocuments extends Migration
{
    
    public function up(){
        Schema::table(cn::UPLOAD_DOCUMENTS_TABLE_NAME, function (Blueprint $table) {
            $table->after(cn::UPLOAD_DOCUMENTS_FILE_TYPE_COL, function($table){
                $table->String(cn::UPLOAD_DOCUMENTS_FILE_NAME_COL)->nullable();
            });

            $table->after(cn::UPLOAD_DOCUMENTS_THUMBNAIL_FILE_PATH_COL, function($table){
                $table->longText(cn::UPLOAD_DOCUMENTS_DESCRIPTION_EN_COL)->nullable();
                $table->longText(cn::UPLOAD_DOCUMENTS_DESCRIPTION_CH_COL)->nullable();
            });
        });
    }

    
    public function down(){
       
    }
}
