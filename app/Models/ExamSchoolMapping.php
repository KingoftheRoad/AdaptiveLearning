<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Constants\DbConstant as cn;

class ExamSchoolMapping extends Model{
    use SoftDeletes, HasFactory;

    protected $table = cn::EXAM_SCHOOL_MAPPING_TABLE;

    public $fillable = [
        cn::EXAM_SCHOOL_MAPPING_ID_COL,
        cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_SCHOOL_ID_COL,
        cn::EXAM_SCHOOL_MAPPING_SCHOOL_ID_COL,
        cn::EXAM_SCHOOL_MAPPING_EXAM_ID_COL,
        cn::EXAM_SCHOOL_MAPPING_STATUS_COL,
        cn::EXAM_SCHOOL_MAPPING_CURRICULUM_YEAR_ID_COL
     ];
 
     public $timestamps = true;
}
