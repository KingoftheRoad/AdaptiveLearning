<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Constants\DbConstant as cn;
use Kyslik\ColumnSortable\Sortable;
use Illuminate\Database\Eloquent\SoftDeletes;

class IntelligentTutorVideos extends Model
{
    use SoftDeletes,HasFactory, Sortable;
    protected $table = cn::INTELLIGENT_TUTOR_VIDEOS_TABLE_NAME;
    public $fillable = [
        cn::INTELLIGENT_TUTOR_VIDEOS_ID_COL,
        cn::INTELLIGENT_TUTOR_VIDEOS_DOCUMENT_MAPPING_ID,
        cn::INTELLIGENT_TUTOR_VIDEOS_DOCUMENT_TYPE_COL,
        cn::INTELLIGENT_TUTOR_VIDEOS_NODE_ID,
        cn::INTELLIGENT_TUTOR_VIDEOS_STRAND_UNITS_MAPPING_ID_COL,
        cn::INTELLIGENT_TUTOR_VIDEOS_TITLE_COL,
        cn::INTELLIGENT_TUTOR_VIDEOS_FILE_TYPE_COL,
        cn::INTELLIGENT_TUTOR_VIDEOS_FILE_NAME_COL,
        cn::INTELLIGENT_TUTOR_VIDEOS_FILE_PATH_COL,
        cn::INTELLIGENT_TUTOR_VIDEOS_THUMBNAIL_FILE_PATH_COL,
        cn::INTELLIGENT_TUTOR_VIDEOS_DESCRIPTION_EN_COL,
        cn::INTELLIGENT_TUTOR_VIDEOS_DESCRIPTION_CH_COL,
        cn::INTELLIGENT_TUTOR_VIDEOS_UPLOAD_BY_COL,
        cn::INTELLIGENT_TUTOR_VIDEOS_LANGUAGE_ID,
        cn::INTELLIGENT_TUTOR_VIDEOS_STATUS_COL,
        cn::INTELLIGENT_TUTOR_VIDEOS_CREATED_AT_COL,
        cn::INTELLIGENT_TUTOR_VIDEOS_UPDATED_BY_COL,
        cn::INTELLIGENT_TUTOR_VIDEOS_CURRICULUM_YEAR_ID_COL
    ];
    public $timestamps = true;

}
