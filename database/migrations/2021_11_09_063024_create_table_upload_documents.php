<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Constants\DbConstant as cn;

class CreateTableUploadDocuments extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(cn::UPLOAD_DOCUMENTS_TABLE_NAME, function (Blueprint $table) {
            $table->bigIncrements(cn::UPLOAD_DOCUMENTS_ID_COL);
            $table->bigInteger(cn::UPLOAD_DOCUMENTS_STRAND_UNITS_MAPPING_ID_COL);
            $table->enum(cn::UPLOAD_DOCUMENTS_FILE_TYPE_COL,['pdf','jpg','png','jpeg','ppt','doc','docx','txt','xls','csv','mp4','mp3']); 
            $table->longText(cn::UPLOAD_DOCUMENTS_FILE_PATH_COL);
            $table->bigInteger(cn::UPLOAD_DOCUMENTS_UPLOAD_BY_COL);
            $table->enum(cn::EXAM_TABLE_STATUS_COLS,['pending','active','inactive'])->default('active'); 
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
        Schema::dropIfExists('table_upload_documents');
    }
}
