<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Constants\DbConstant as cn;


class ChangeColumnStrandUnitsMappingIdUploadDocumentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table(cn::UPLOAD_DOCUMENTS_TABLE_NAME, function ($table) {
            $table->unsignedBigInteger(cn::UPLOAD_DOCUMENTS_STRAND_UNITS_MAPPING_ID_COL)->nullable()->change();
            $table->foreign(cn::UPLOAD_DOCUMENTS_STRAND_UNITS_MAPPING_ID_COL)->nullable()->references(cn::OBJECTIVES_MAPPINGS_ID_COL)->on(cn::OBJECTIVES_MAPPINGS_TABLE_NAME)->onDelete(cn::DB_ENGINE_ONDELETE_NAME);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
