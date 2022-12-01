<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Constants\DbConstant as cn;

class AddColumnNodeIdTableNameUploadDocumentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table(cn::UPLOAD_DOCUMENTS_TABLE_NAME,function(Blueprint $table){
            $table->after(cn::UPLOAD_DOCUMENTS_DOCUMENT_TYPE_COL,function($table){
                $table->unsignedBigInteger(cn::UPLOAD_DOCUMENTS_NODE_ID)->nullable();
                $table->foreign(cn::UPLOAD_DOCUMENTS_NODE_ID)->nullable()->references(cn::NODES_NODE_ID_COL)->on(cn::NODES_TABLE_NAME)->onDelete(cn::DB_ENGINE_ONDELETE_NAME);
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
