<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Constants\DbConstant as cn;

class CreateTableIntelligentTutorVideos extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(cn::INTELLIGENT_TUTOR_VIDEOS_TABLE_NAME, function (Blueprint $table) {
            $table->bigIncrements(cn::INTELLIGENT_TUTOR_VIDEOS_ID_COL);
            $table->bigInteger(cn::INTELLIGENT_TUTOR_VIDEOS_DOCUMENT_MAPPING_ID)->nullable();
            $table->Integer(cn::INTELLIGENT_TUTOR_VIDEOS_DOCUMENT_TYPE_COL)->comment('1-Self-Learning, 2-Execercise, 3-Test')->nullable();
            $table->unsignedBigInteger(cn::MAIN_UPLOAD_DOCUMENT_STRAND_UNITS_MAPPING_ID_COL)->nullable();
            $table->String(cn::INTELLIGENT_TUTOR_VIDEOS_TITLE_COL)->nullable();
            $table->longText(cn::INTELLIGENT_TUTOR_VIDEOS_DESCRIPTION_EN_COL)->nullable();
            $table->longText(cn::INTELLIGENT_TUTOR_VIDEOS_DESCRIPTION_CH_COL)->nullable();
            $table->bigInteger(cn::UPLOAD_DOCUMENTS_FILE_NAME_COL)->nullable();
            $table->enum(cn::UPLOAD_DOCUMENTS_FILE_TYPE_COL,['pdf','jpg','png','jpeg','ppt','doc','docx','txt','xls','xlsx','csv','mp4','mp3','3gp','avi','vob','flv','webm','wmv','ogg','mpeg','mov','m4p','wav','aiff','aac','pptx','url']); 
            $table->longText(cn::INTELLIGENT_TUTOR_VIDEOS_FILE_PATH_COL);
            $table->longText(cn::INTELLIGENT_TUTOR_VIDEOS_THUMBNAIL_FILE_PATH_COL)->nullable();
            $table->bigInteger(cn::INTELLIGENT_TUTOR_VIDEOS_UPLOAD_BY_COL);
            $table->unsignedBigInteger(cn::INTELLIGENT_TUTOR_VIDEOS_LANGUAGE_ID)->nullable();
            $table->enum(cn::INTELLIGENT_TUTOR_VIDEOS_STATUS_COL,['pending','active','inactive'])->default('active'); 
            $table->timestamps();
            $table->softDeletes();
            $table->foreign(cn::MAIN_UPLOAD_DOCUMENT_STRAND_UNITS_MAPPING_ID_COL)->references(cn::OBJECTIVES_MAPPINGS_ID_COL)->on(cn::OBJECTIVES_MAPPINGS_TABLE_NAME)->onDelete(cn::DB_ENGINE_ONDELETE_NAME);
            $table->foreign(cn::INTELLIGENT_TUTOR_VIDEOS_LANGUAGE_ID)->references(cn::LANGUAGES_ID_COL)->on(cn::LANGUAGES_TABLE_NAME)->onDelete(cn::DB_ENGINE_ONDELETE_NAME);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists(cn::INTELLIGENT_TUTOR_VIDEOS_TABLE_NAME);
    }
}
