<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Nodes;
use App\Models\MainUploadDocument;
use App\Constants\DbConstant as cn;
use Kyslik\ColumnSortable\Sortable;
use Illuminate\Database\Eloquent\SoftDeletes;

class UploadDocuments extends Model
{
    use SoftDeletes,HasFactory, Sortable;
    protected $table = cn::UPLOAD_DOCUMENTS_TABLE_NAME;
    public $fillable = [
        cn::UPLOAD_DOCUMENTS_ID_COL,
        cn::UPLOAD_DOCUMENTS_DOCUMENT_MAPPING_ID,
        cn::UPLOAD_DOCUMENTS_DOCUMENT_TYPE_COL,
        cn::UPLOAD_DOCUMENTS_NODE_ID,
        cn::UPLOAD_DOCUMENTS_FILE_NAME_COL,
        cn::UPLOAD_DOCUMENTS_TITLE_COL,
        cn::UPLOAD_DOCUMENTS_STRAND_UNITS_MAPPING_ID_COL,
        cn::UPLOAD_DOCUMENTS_FILE_TYPE_COL,
        cn::UPLOAD_DOCUMENTS_FILE_PATH_COL,
        cn::UPLOAD_DOCUMENTS_THUMBNAIL_FILE_PATH_COL,
        cn::UPLOAD_DOCUMENTS_DESCRIPTION_EN_COL,
        cn::UPLOAD_DOCUMENTS_DESCRIPTION_CH_COL,
        cn::UPLOAD_DOCUMENTS_LANGUAGE_ID,
        cn::UPLOAD_DOCUMENTS_UPLOAD_BY_COL,
        cn::UPLOAD_DOCUMENTS_STATUS_COL,
        cn::UPLOAD_DOCUMENTS_CREATED_AT_COL,
        cn::UPLOAD_DOCUMENTS_UPDATED_BY_COL,
        cn::UPLOAD_DOCUMENTS_CURRICULUM_YEAR_ID_COL
    ];
    public $timestamps = true;

    public function nodes(){
        return $this->hasOne(Nodes::class, cn::NODES_NODE_ID_COL, cn::UPLOAD_DOCUMENTS_NODE_ID);
    }

    public function mainDoucment(){
        return $this->belongsTo(MainUploadDocument::class,cn::UPLOAD_DOCUMENTS_DOCUMENT_MAPPING_ID,cn::MAIN_UPLOAD_DOCUMENT_ID_COL);
    }

    public function documentData(){
        return $this->hasOne(MainUploadDocument::class,cn::MAIN_UPLOAD_DOCUMENT_ID_COL,cn::UPLOAD_DOCUMENTS_DOCUMENT_MAPPING_ID);
    }
}
