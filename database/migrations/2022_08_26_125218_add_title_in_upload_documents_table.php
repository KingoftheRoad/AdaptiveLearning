<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Constants\DbConstant as cn;

class AddTitleInUploadDocumentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table(cn::UPLOAD_DOCUMENTS_TABLE_NAME, function (Blueprint $table) {
            $table->after(cn::UPLOAD_DOCUMENTS_STRAND_UNITS_MAPPING_ID_COL, function($table){
                $table->String(cn::UPLOAD_DOCUMENTS_TITLE_COL)->nullable();
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
        Schema::table(cn::UPLOAD_DOCUMENTS_TABLE_NAME, function (Blueprint $table) {
                $table->dropColumn(cn::UPLOAD_DOCUMENTS_TITLE_COL);
            });
    }
}
