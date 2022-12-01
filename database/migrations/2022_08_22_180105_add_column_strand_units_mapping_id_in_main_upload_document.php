<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Constants\DbConstant as cn;

class AddColumnStrandUnitsMappingIdInMainUploadDocument extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table(cn::MAIN_UPLOAD_DOCUMENT_TABLE_NAME, function (Blueprint $table) {
            $table->after(cn::MAIN_UPLOAD_DOCUMENT_ID_COL, function($table){
                $table->unsignedBigInteger(cn::MAIN_UPLOAD_DOCUMENT_STRAND_UNITS_MAPPING_ID_COL)->nullable();
                $table->foreign(cn::MAIN_UPLOAD_DOCUMENT_STRAND_UNITS_MAPPING_ID_COL)->references(cn::OBJECTIVES_MAPPINGS_ID_COL)->on(cn::OBJECTIVES_MAPPINGS_TABLE_NAME)->onDelete(cn::DB_ENGINE_ONDELETE_NAME);
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
        Schema::table(cn::MAIN_UPLOAD_DOCUMENT_TABLE_NAME, function (Blueprint $table) {
            $table->dropForeign([cn::MAIN_UPLOAD_DOCUMENT_STRAND_UNITS_MAPPING_ID_COL.'_foreign']);
            $table->dropColumn(cn::MAIN_UPLOAD_DOCUMENT_STRAND_UNITS_MAPPING_ID_COL);
        });
    }
}
