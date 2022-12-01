<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Constants\DbConstant as cn;

class AddDescriptionEnAndDescriptionChInUploadDocumentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table(cn::UPLOAD_DOCUMENTS_TABLE_NAME, function (Blueprint $table) {
            $table->after(cn::UPLOAD_DOCUMENTS_TITLE_COL, function($table){
                $table->String(cn::UPLOAD_DOCUMENTS_DESCRIPTION_EN_COL)->nullable();
                $table->String(cn::UPLOAD_DOCUMENTS_DESCRIPTION_CH_COL)->nullable();
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
            $table->dropColumn(cn::UPLOAD_DOCUMENTS_DESCRIPTION_EN_COL);
            $table->dropColumn(cn::UPLOAD_DOCUMENTS_DESCRIPTION_CH_COL);
        });
    }
}
