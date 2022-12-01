<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Constants\DbConstant as cn;

class CreateMainUploadDocumentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(cn::MAIN_UPLOAD_DOCUMENT_TABLE_NAME, function (Blueprint $table) {
            $table->bigIncrements(cn::MAIN_UPLOAD_DOCUMENT_ID_COL);
            $table->longText(cn::MAIN_UPLOAD_DOCUMENT_NODE_ID_COL);
            $table->String(cn::UPLOAD_DOCUMENTS_FILE_NAME_COL)->nullable();
            $table->longText(cn::UPLOAD_DOCUMENTS_DESCRIPTION_EN_COL)->nullable();
            $table->longText(cn::UPLOAD_DOCUMENTS_DESCRIPTION_CH_COL)->nullable();
            $table->bigInteger(cn::MAIN_UPLOAD_DOCUMENT_UPLOAD_BY_COL)->nullable();
            $table->enum(cn::MAIN_UPLOAD_DOCUMENT_STATUS_COL,['active', 'inactive','pending'])->default('active');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists(cn::MAIN_UPLOAD_DOCUMENT_TABLE_NAME);
    }
}
