<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Constants\DbConstant as cn;

class DropColumnsUploadDocumentTable extends Migration
{
    public function up(){
        if (Schema::hasColumn(cn::UPLOAD_DOCUMENTS_TABLE_NAME, cn::UPLOAD_DOCUMENTS_FILE_NAME_COL)){
            Schema::table(cn::UPLOAD_DOCUMENTS_TABLE_NAME, function($table) {
                $table->dropColumn(cn::UPLOAD_DOCUMENTS_FILE_NAME_COL);
            });
        }

        if (Schema::hasColumn(cn::UPLOAD_DOCUMENTS_TABLE_NAME, cn::UPLOAD_DOCUMENTS_DESCRIPTION_EN_COL)){
            Schema::table(cn::UPLOAD_DOCUMENTS_TABLE_NAME, function($table) {
                $table->dropColumn(cn::UPLOAD_DOCUMENTS_DESCRIPTION_EN_COL);
            });
        }

        if (Schema::hasColumn(cn::UPLOAD_DOCUMENTS_TABLE_NAME, cn::UPLOAD_DOCUMENTS_DESCRIPTION_CH_COL)){
            Schema::table(cn::UPLOAD_DOCUMENTS_TABLE_NAME, function($table) {
                $table->dropColumn(cn::UPLOAD_DOCUMENTS_DESCRIPTION_CH_COL);
            });
        }

        if (Schema::hasColumn(cn::UPLOAD_DOCUMENTS_TABLE_NAME, cn::UPLOAD_DOCUMENTS_UPLOAD_BY_COL)){
            Schema::table(cn::UPLOAD_DOCUMENTS_TABLE_NAME, function($table) {
                $table->dropColumn(cn::UPLOAD_DOCUMENTS_UPLOAD_BY_COL);
            });
        }

        if (Schema::hasColumn(cn::UPLOAD_DOCUMENTS_TABLE_NAME, cn::UPLOAD_DOCUMENTS_STATUS_COL)){
            Schema::table(cn::UPLOAD_DOCUMENTS_TABLE_NAME, function($table) {
                $table->dropColumn(cn::UPLOAD_DOCUMENTS_STATUS_COL);
            });
        }

        if (Schema::hasColumn(cn::UPLOAD_DOCUMENTS_TABLE_NAME, cn::UPLOAD_DOCUMENTS_NODE_ID)){
            Schema::table(cn::UPLOAD_DOCUMENTS_TABLE_NAME, function($table) {
            	$table->dropForeign('upload_documents_node_id_foreign');
                $table->dropColumn(cn::UPLOAD_DOCUMENTS_NODE_ID);
            });
        }
    }
   
    public function down(){

    }
}
