<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Kyslik\ColumnSortable\Sortable;
use App\Constants\DbConstant as cn;
use App\Models\Exam;
use App\Models\AttemptExams;
use App\Models\PeerGroup;

class MyStudyReport extends Model
{
    use HasFactory,Sortable;

    protected $table = cn::STUDY_REPORT_TABLE;

    protected $fillable = [
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

    public $sortable = [
            cn::STUDY_REPORT_ID_COL,
            cn::STUDY_REPORT_REPORT_TYPE_COL,
            cn::STUDY_REPORT_STUDY_TYPE_COL,
            cn::STUDY_REPORT_SCHOOL_ID_COL,
            cn::STUDY_REPORT_EXAM_ID_COL,
            cn::STUDY_REPORT_GRADE_ID_COL,
            cn::STUDY_REPORT_AVERAGE_ACCURACY_COL,
            cn::STUDY_REPORT_STUDY_STATUS_COL,
            cn::STUDY_REPORT_QUESTIONS_DIFFICULTIES_COL,
            cn::STUDY_REPORT_DATE_TIME_COL
    ];
    
    public function exams(){
        return $this->hasOne(Exam::Class,cn::EXAM_TABLE_ID_COLS, cn::STUDY_REPORT_SCHOOL_ID_COL);
    }
    public function user(){
        return $this->hasOne(User::Class,cn::USERS_ID_COL, cn::STUDY_REPORT_STUDY_STATUS_COL);
    }
    public function attempt_exams(){
        return $this->hasMany(AttemptExams::class,cn::ATTEMPT_EXAMS_EXAM_ID, cn::STUDY_REPORT_SCHOOL_ID_COL);
    }
}
