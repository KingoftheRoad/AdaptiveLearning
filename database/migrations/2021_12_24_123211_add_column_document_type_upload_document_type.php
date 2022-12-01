<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Constants\DbConstant as cn;

class AddColumnDocumentTypeUploadDocumentType extends Migration
{
  
    public function up()
    {
        Schema::table(cn::UPLOAD_DOCUMENTS_TABLE_NAME,function(Blueprint $table){
            $table->after(cn::UPLOAD_DOCUMENTS_ID_COL,function($table){
                $table->Integer(cn::UPLOAD_DOCUMENTS_DOCUMENT_TYPE_COL)->comment('1-Self-Learning, 2-Execercise, 3-Test')->nullable();
            });
        });
    }

    
    public function down()
    {
        //
    }
}
