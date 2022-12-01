<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Constants\DbConstant as cn;

class DropTitleDescriptionEnAndDescriptionChColInUploadDocumentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasColumn(cn::UPLOAD_DOCUMENTS_TABLE_NAME, cn::UPLOAD_DOCUMENTS_TITLE_COL)){
            Schema::table(cn::UPLOAD_DOCUMENTS_TABLE_NAME, function($table) {
                $table->dropColumn(cn::UPLOAD_DOCUMENTS_TITLE_COL);
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
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('upload_document', function (Blueprint $table) {
            //
        });
    }
}
