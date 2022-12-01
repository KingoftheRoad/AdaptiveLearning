<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Kyslik\ColumnSortable\Sortable;
use App\Constants\DbConstant as cn;
use App\Models\Exam;
use App\Models\AttemptExams;
use App\Models\PeerGroup;

class MyTeachingReport extends Model
{
    use HasFactory,Sortable;

    protected $table = cn::TEACHING_REPORT_TABLE;

    protected $fillable = [
        cn::TEACHING_REPORT_REPORT_TYPE_COL,
        cn::TEACHING_REPORT_STUDY_TYPE_COL,
        cn::TEACHING_REPORT_SCHOOL_ID_COL,
        cn::TEACHING_REPORT_EXAM_ID_COL,
        cn::TEACHING_REPORT_GRADE_ID_COL,
        cn::TEACHING_REPORT_CLASS_ID_COL,
        cn::TEACHING_REPORT_PEER_GROUP_ID,
        cn::TEACHING_REPORT_GRADE_WITH_CLASS_COL,
        cn::TEACHING_REPORT_STUDENT_IDS_COL,
        cn::TEACHING_TABLE_NO_OF_STUDENTS_COL,
        cn::TEACHING_REPORT_STUDENT_PROGRESS_COL,
        cn::TEACHING_REPORT_AVERAGE_ACCURACY_COL,
        cn::TEACHING_REPORT_STUDY_STATUS_COL,
        cn::TEACHING_REPORT_QUESTIONS_DIFFICULTIES_COL,
        cn::TEACHING_REPORT_DATE_AND_TIME_COL,
        cn::TEACHING_REPORT_CURRICULUM_YEAR_ID_COL
    ];

    public $sortable = [
            cn::TEACHING_REPORT_REPORT_TYPE_COL,
            cn::TEACHING_REPORT_STUDY_TYPE_COL,
            cn::TEACHING_REPORT_SCHOOL_ID_COL,
            cn::TEACHING_REPORT_EXAM_ID_COL,
            cn::TEACHING_REPORT_GRADE_ID_COL,
            cn::TEACHING_REPORT_CLASS_ID_COL,
            cn::TEACHING_REPORT_GRADE_WITH_CLASS_COL,
            cn::TEACHING_REPORT_STUDENT_IDS_COL,
            cn::TEACHING_TABLE_NO_OF_STUDENTS_COL,
            cn::TEACHING_REPORT_STUDENT_PROGRESS_COL,
            cn::TEACHING_REPORT_AVERAGE_ACCURACY_COL,
            cn::TEACHING_REPORT_STUDY_STATUS_COL,
            cn::TEACHING_REPORT_QUESTIONS_DIFFICULTIES_COL,
            cn::TEACHING_REPORT_DATE_AND_TIME_COL
    ];
    
    public function exams(){
        return $this->hasOne(Exam::Class,cn::EXAM_TABLE_ID_COLS, cn::TEACHING_REPORT_EXAM_ID_COL);
    }
    public function user(){
        return $this->hasOne(User::Class,cn::USERS_ID_COL, cn::TEACHING_REPORT_STUDENT_IDS_COL);
    }
    public function attempt_exams(){
        return $this->hasMany(AttemptExams::class,cn::ATTEMPT_EXAMS_EXAM_ID, cn::TEACHING_REPORT_EXAM_ID_COL);
    }
    public function peerGroup(){
        return $this->hasOne(PeerGroup::class,cn::PEER_GROUP_ID_COL,cn::TEACHING_REPORT_PEER_GROUP_ID);
    }
}
