<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Constants\DbConstant as cn;

class AddDocumentMappingColInDocumentTable extends Migration
{
    public function up(){
        Schema::table(cn::UPLOAD_DOCUMENTS_TABLE_NAME, function (Blueprint $table) {
            $table->after(cn::UPLOAD_DOCUMENTS_ID_COL, function($table){
                $table->unsignedBigInteger(cn::UPLOAD_DOCUMENTS_DOCUMENT_MAPPING_ID)->nullable();
            });

            $table->foreign(cn::UPLOAD_DOCUMENTS_DOCUMENT_MAPPING_ID)->references(cn::MAIN_UPLOAD_DOCUMENT_ID_COL)->on(cn::MAIN_UPLOAD_DOCUMENT_TABLE_NAME)->onDelete(cn::DB_ENGINE_ONDELETE_NAME);
        });
    }

    public function down()
    {
        
    }
}
