<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Languages;
use Kyslik\ColumnSortable\Sortable;
use App\Models\UploadDocuments;
use App\Constants\DbConstant as cn;

class MainUploadDocument extends Model
{
    use HasFactory,SoftDeletes,Sortable;

    protected $table = cn::MAIN_UPLOAD_DOCUMENT_TABLE_NAME;

    protected $fillable = [
        cn::MAIN_UPLOAD_DOCUMENT_ID_COL,
        cn::MAIN_UPLOAD_DOCUMENT_NODE_ID_COL,
        cn::MAIN_UPLOAD_DOCUMENT_FILE_NAME_COL,
        cn::MAIN_UPLOAD_DOCUMENT_DESCRIPTION_EN_COL,
        cn::MAIN_UPLOAD_DOCUMENT_DESCRIPTION_CH_COL,
        cn::MAIN_UPLOAD_DOCUMENT_UPLOAD_BY_COL,
        cn::MAIN_UPLOAD_DOCUMENT_LANGUAGE_ID,
        cn::MAIN_UPLOAD_DOCUMENT_STATUS_COL

    ];

    public $sortable = [
        cn::MAIN_UPLOAD_DOCUMENT_ID_COL,
        cn::MAIN_UPLOAD_DOCUMENT_NODE_ID_COL,
        cn::MAIN_UPLOAD_DOCUMENT_FILE_NAME_COL,
        cn::MAIN_UPLOAD_DOCUMENT_DESCRIPTION_EN_COL,
        cn::MAIN_UPLOAD_DOCUMENT_DESCRIPTION_CH_COL,
        cn::MAIN_UPLOAD_DOCUMENT_UPLOAD_BY_COL,
        cn::MAIN_UPLOAD_DOCUMENT_LANGUAGE_ID,
        cn::MAIN_UPLOAD_DOCUMENT_STATUS_COL
    ];
    public function document(){
        return $this->hasMany(UploadDocuments::class, cn::UPLOAD_DOCUMENTS_DOCUMENT_MAPPING_ID, cn::MAIN_UPLOAD_DOCUMENT_ID_COL);
    }

    public function language(){
        return $this->belongsTo(Languages::class,cn::MAIN_UPLOAD_DOCUMENT_LANGUAGE_ID,cn::LANGUAGES_ID_COL);
    }
}
