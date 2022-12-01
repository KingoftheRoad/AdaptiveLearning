<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Constants\DbConstant as cn;

class DropColumnFileNameFromMainDocumentTabl extends Migration
{
    public function up()
    {
        if (Schema::hasColumn(cn::MAIN_UPLOAD_DOCUMENT_TABLE_NAME, cn::MAIN_UPLOAD_DOCUMENT_FILE_NAME_COL)){
            Schema::table(cn::MAIN_UPLOAD_DOCUMENT_TABLE_NAME, function($table) {
                $table->dropColumn(cn::MAIN_UPLOAD_DOCUMENT_FILE_NAME_COL);
            });
        }
    }

    public function down()
    {
       //
    }
}
