<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Constants\DbConstant as cn;

class ExamCreditPointRulesMapping extends Model
{
    use SoftDeletes, HasFactory;

    protected $table = cn::EXAM_CREDIT_POINT_RULES_MAPPING_TABLE;

    public $fillable = [
       cn::EXAM_CREDIT_POINT_RULES_MAPPING_ID_COL,
       cn::EXAM_CREDIT_POINT_RULES_MAPPING_EXAM_ID_COL,
       cn::EXAM_CREDIT_POINT_RULES_MAPPING_SCHOOL_ID_COL,
       cn::EXAM_CREDIT_POINT_RULES_MAPPING_CREDIT_POINT_RULES_COL,
       cn::EXAM_CREDIT_POINT_RULES_MAPPING_RULES_VALUE_COL,
       cn::EXAM_CREDIT_POINT_RULES_MAPPING_STATUS_COL,
       cn::EXAM_CREDIT_POINT_RULES_MAPPING_CURRICULUM_YEAR_ID_COL
    ];

    public $timestamps = true;
}
