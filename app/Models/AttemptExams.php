<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Constants\DbConstant as cn;
use Illuminate\Validation\Rule;
use App\Models\UserCreditPointHistory;
use App\Models\Exam;

class AttemptExams extends Model
{
    use SoftDeletes, HasFactory;
    protected $table = cn::ATTEMPT_EXAMS_TABLE_NAME;

    public $fillable = [
        cn::ATTEMPT_EXAMS_EXAM_ID,
        cn::ATTEMPT_EXAMS_STUDENT_STUDENT_ID,
        cn::ATTEMPT_EXAMS_STUDENT_GRADE_ID,
        cn::ATTEMPT_EXAMS_STUDENT_CLASS_ID,
        cn::ATTEMPT_EXAMS_LANGUAGE_COL,
        cn::ATTEMPT_EXAMS_QUESTION_ANSWER_COL,
        cn::ATTEMPT_EXAMS_ATTEMPT_FIRST_TRIAL_COL,
        cn::ATTEMPT_EXAMS_ATTEMPT_SECOND_TRIAL_COL,
        cn::ATTEMPT_EXAMS_TOTAL_CORRECT_ANSWERS,
        cn::ATTEMPT_EXAMS_TOTAL_WRONG_ANSWERS,
        cn::ATTEMPT_EXAMS_EXAM_TAKING_TIMING,
        cn::ATTEMPT_EXAMS_STUDENT_ABILITY_COL,
        cn::ATTEMPT_EXAMS_SERVER_DETAILS_COL,
        cn::ATTEMPT_EXAMS_WRONG_ANSWER_COL,
        cn::ATTEMPT_EXAMS_STATUS_COL,
        cn::ATTEMPT_EXAMS_BEFORE_EXAM_SURVEY_COL,
        cn::ATTEMPT_EXAMS_AFTER_EXAM_SURVEY_COL
    ];

    public $timestamps = true;

    /**
    ** Validation Rules for users
    **/
    public static function rules($request = null, $action = '', $examDetail = null){
        switch ($action) {
            case 'attempt_exam':
                if(!empty($examDetail)){
                    $questionIds = explode(',',$examDetail->question_ids);
                    foreach($questionIds as $key => $QuestionId){
                        $rules['ans_que_'.$QuestionId] = ['required'];
                    }
                }
                break;
            case 'change_exam_language':
                $rules = [
                    'language' => [Rule::in(['en','ch'])]
                ];
                break;
            default:
                break;
        }
        return $rules;
    }

    /**
    ** Additional Validation Massages for users
    **/
    public static function rulesMessages($action = ''){
        $messages = [];
        switch ($action) {
            case 'attempt_exam':
                break;
            case 'change_exam_language':
                $messages = [
                    'language.in' => "Selected language is invalid Allowed only ('English','Chinese')"
                ];
                break;
        }
        return $messages;
    }
    
    public function user(){
        return $this->hasOne(User::class,cn::USERS_ID_COL, cn::ATTEMPT_EXAMS_STUDENT_STUDENT_ID);
    }

    public function creditPointHistory(){
        return $this->hasMany(UserCreditPointHistory::class,'user_id','student_id');
    }
}
