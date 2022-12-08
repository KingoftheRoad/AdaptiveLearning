<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Constants\DbConstant as cn;
use App\Models\Exam;

class AttemptExamStudentMapping extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = cn::ATTEMPT_EXAM_STUDENT_MAPPING_TABLE_NAME;

    public $fillable = [
        cn::ATTEMPT_EXAM_STUDENT_MAPPING_EXAM_ID_COL,
        cn::ATTEMPT_EXAM_STUDENT_MAPPING_STUDENT_ID_COL,
        cn::ATTEMPT_EXAM_STUDENT_MAPPING_STATUS_COL,
        cn::ATTEMPT_EXAM_STUDENT_MAPPING_CURRICULUM_YEAR_ID_COL
    ];

    public $timestamps = true;
}
