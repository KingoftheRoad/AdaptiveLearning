<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Constants\DbConstant as cn;

class ExamConfigurationsDetails extends Model
{
    use SoftDeletes, HasFactory;

    protected $table = cn::EXAM_CONFIGURATIONS_DETAILS_TABLE_NAME;

    public $fillable = [
       cn::EXAM_CONFIGURATIONS_DETAILS_ID_COL,
       cn::EXAM_CONFIGURATIONS_DETAILS_EXAM_ID_COL,
       cn::EXAM_CONFIGURATIONS_DETAILS_CREATED_BY_USER_ID_COL,
       cn::EXAM_CONFIGURATIONS_DETAILS_STRAND_IDS_COL,
       cn::EXAM_CONFIGURATIONS_DETAILS_LEARNING_UNIT_IDS_COL,
       cn::EXAM_CONFIGURATIONS_DETAILS_LEARNING_OBJECTIVES_IDS_COL,
       cn::EXAM_CONFIGURATIONS_DETAILS_DIFFICULTY_MODE_COL,
       cn::EXAM_CONFIGURATIONS_DETAILS_DIFFICULTY_LEVELS_COL,
       cn::EXAM_CONFIGURATIONS_DETAILS_NO_OF_QUESTIONS_COL,
       cn::EXAM_CONFIGURATIONS_DETAILS_TIME_DURATION_COL,
       cn::EXAM_CONFIGURATIONS_DETAILS_CURRICULUM_YEAR_ID_COL
    ];

    public $timestamps = true;
}
