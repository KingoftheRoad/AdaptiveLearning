<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Kyslik\ColumnSortable\Sortable;
use App\Constants\DbConstant as cn;

class StudyReport extends Model
{
    use HasFactory, SoftDeletes,Sortable;

    protected $table = cn::STUDENT_GROUP_TABLE_NAME;

    public $fillable = [
        cn::STUDY_REPORT_ID_COL,
        cn::STUDY_REPORT_REPORT_TYPE_COL,
        cn::STUDY_REPORT_STUDY_TYPE_COL,
        cn::STUDY_REPORT_SCHOOL_ID_COL,
        cn::STUDY_REPORT_EXAM_ID_COL,
        cn::STUDY_REPORT_GRADE_ID_COL,
        cn::STUDY_REPORT_CLASS_ID_COL,
        cn::STUDY_REPORT_AVERAGE_ACCURACY_COL,
        cn::STUDY_REPORT_STUDY_STATUS_COL,
        cn::STUDY_REPORT_QUESTIONS_DIFFICULTIES_COL,
        cn::STUDY_REPORT_DATE_TIME_COL,
        cn::STUDY_REPORT_STUDENT_ID_COL
    ];

    // Enable sortable columns name
    public $sortable = [
        cn::STUDY_REPORT_ID_COL,
        cn::STUDY_REPORT_REPORT_TYPE_COL,
        cn::STUDY_REPORT_STUDY_TYPE_COL,
        cn::STUDY_REPORT_SCHOOL_ID_COL,
        cn::STUDY_REPORT_EXAM_ID_COL,
        cn::STUDY_REPORT_GRADE_ID_COL,
        cn::STUDY_REPORT_CLASS_ID_COL,
        cn::STUDY_REPORT_AVERAGE_ACCURACY_COL,
        cn::STUDY_REPORT_STUDY_STATUS_COL,
        cn::STUDY_REPORT_QUESTIONS_DIFFICULTIES_COL,
        cn::STUDY_REPORT_DATE_TIME_COL
    ];
    
    public $timestamps = true;
}
