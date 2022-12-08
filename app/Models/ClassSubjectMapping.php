<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Constants\DbConstant as cn;

class ClassSubjectMapping extends Model
{
    use SoftDeletes, HasFactory;

    protected $table = cn::CLASS_SUBJECT_MAPPING_TABLE_NAME;

    public $fillable = [
        cn::CLASS_SUBJECT_MAPPING_SUBJECT_ID_COL,
        cn::CLASS_SUBJECT_MAPPING_CLASS_ID_COL,
        cn::CLASS_SUBJECT_MAPPING_SCHOOL_ID_COL,
        cn::CLASS_SUBJECT_MAPPING_STATUS_COL,
        cn::CLASS_SUBJECT_MAPPING_CURRICULUM_YEAR_ID_COL
    ];

    public $timestamps = true;
    
    protected $dates = [cn::DELETED_AT_COL];
}
