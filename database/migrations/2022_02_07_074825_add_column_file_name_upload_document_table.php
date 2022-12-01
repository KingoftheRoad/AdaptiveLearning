<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Constants\DbConstant as cn;

class AddColumnFileNameUploadDocumentTable extends Migration
{
    public function up()
    {
        Schema::table(cn::UPLOAD_DOCUMENTS_TABLE_NAME, function (Blueprint $table) {
            $table->after(cn::UPLOAD_DOCUMENTS_STRAND_UNITS_MAPPING_ID_COL, function($table){
                $table->bigInteger(cn::UPLOAD_DOCUMENTS_FILE_NAME_COL)->nullable();
            });
        });
    }

    public function down()
    {
        //
    }
}
