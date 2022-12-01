<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Constants\DbConstant as cn;
use App\Models\AttemptExams;
use App\Models\ExamSchoolMapping;
use App\Models\CurriculumYear;
use App\Models\User;
use Kyslik\ColumnSortable\Sortable;

class Exam extends Model
{
    use SoftDeletes, HasFactory,Sortable;
    protected $table = cn::EXAM_TABLE_NAME;

    public $fillable = [
        cn::EXAM_CURRICULUM_YEAR_ID_COL,
       cn::EXAM_TABLE_PARENT_EXAM_ID_COLS,
       cn::EXAM_TABLE_USE_OF_MODE_COLS,
       cn::EXAM_TYPE_COLS,
       cn::EXAM_REFERENCE_NO_COL,
       cn::EXAM_TABLE_TITLE_COLS,
       cn::EXAM_TABLE_SCHOOL_COLS,
       cn::EXAM_TABLE_FROM_DATE_COLS,
       cn::EXAM_TABLE_TO_DATE_COLS,
       cn::EXAM_TABLE_START_TIME_COL,
       cn::EXAM_TABLE_END_TIME_COL,
       cn::EXAM_TABLE_REPORT_TYPE_COLS,
       cn::EXAM_TABLE_RESULT_DATE_COLS,
       cn::EXAM_TABLE_PUBLISH_DATE_COL,
       cn::EXAM_TABLE_TIME_DURATIONS_COLS,
       cn::EXAM_TABLE_DESCRIPTION_COLS,
       cn::EXAM_TABLE_QUESTION_IDS_COL,
       cn::EXAM_TABLE_STUDENT_IDS_COL,
       cn::EXAM_TABLE_PEER_GROUP_IDS_COL,
       cn::EXAM_TABLE_GROUP_IDS_COL,
       cn::EXAM_TABLE_IS_GROUP_TEST_COL,
       cn::EXAM_TABLE_RESULT_DECLARE_COL,
       cn::EXAM_TABLE_TEMPLATE_ID,
       cn::EXAM_TABLE_SELF_LEARNING_TEST_TYPE_COL,
       cn::EXAM_TABLE_NO_OF_TRIALS_PER_QUESTIONS_COL,
       cn::EXAM_TABLE_DIFFICULTY_MODE_COL,
       cn::EXAM_TABLE_DIFFICULTY_LEVELS_COL,
       cn::EXAM_TABLE_IS_DISPLAY_HINTS_COL,
       cn::EXAM_TABLE_IS_DISPLAY_FULL_SOLUTIONS_COL,
       cn::EXAM_TABLE_IS_DISPLAY_PER_ANSWER_HINTS_COL,
       cn::EXAM_TABLE_IS_RANDOMIZED_ANSWERS_COL,
       cn::EXAM_TABLE_IS_RANDOMIZED_ORDER_COL,
       cn::EXAM_TABLE_LEARNING_OBJECTIVES_CONFIGURATIONS_COL,
       cn::EXAM_TABLE_CREATED_BY_COL,
       cn::EXAM_TABLE_CREATED_BY_USER_COL,
       cn::EXAM_TABLE_STATUS_COLS,
       cn::EXAM_TABLE_IS_UNLIMITED,
       cn::EXAM_TABLE_IS_TEACHING_REPORT_SYNC,
       cn::EXAM_TABLE_ASSIGN_SCHOOL_STATUS
    ];

    public $timestamps = true;

    // Enable sortable columns name
    public $sortable = [
        cn::EXAM_CURRICULUM_YEAR_ID_COL,
        cn::EXAM_TYPE_COLS,
        cn::EXAM_REFERENCE_NO_COL,
        cn::EXAM_TABLE_TITLE_COLS,
        cn::EXAM_TABLE_FROM_DATE_COLS,
        cn::EXAM_TABLE_TO_DATE_COLS,
        cn::EXAM_TABLE_RESULT_DATE_COLS,
        cn::EXAM_TABLE_TIME_DURATIONS_COLS,
        cn::EXAM_TABLE_STATUS_COLS,
        cn::EXAM_TABLE_SELF_LEARNING_TEST_TYPE_COL
    ];

    public static function rules($request = null, $action = '', $id = null){
        switch ($action) {
            case 'create':
                $rules = [
                    cn::EXAM_TYPE_COLS => ['required'],
                    cn::EXAM_TABLE_TITLE_COLS => ['required'],
                    cn::EXAM_TABLE_FROM_DATE_COLS => ['required'],
                    cn::EXAM_TABLE_TO_DATE_COLS => ['required'],
                    cn::EXAM_TABLE_RESULT_DATE_COLS => ['required'],
                ];
                // If not selected unlimited time then required time duration field
                if($request->unlimited_time != 1){
                    $rules = [
                        'time_duration' => ['required']
                    ];
                }
                break;
            case 'update':
                $rules = [
                    cn::EXAM_TYPE_COLS => ['required'],
                    cn::EXAM_TABLE_TITLE_COLS => ['required'],
                    cn::EXAM_TABLE_FROM_DATE_COLS => ['required'],
                    cn::EXAM_TABLE_TO_DATE_COLS => ['required'],
                    cn::EXAM_TABLE_RESULT_DATE_COLS => ['required'],
                ];
                // If not selected unlimited time then required time duration field
                if($request->unlimited_time != 1){
                    $rules = [
                        'time_duration' => ['required']
                    ];
                }
                break;
            case 'addQuestionToExams':
                $rules = [
                    cn::EXAM_TABLE_QUESTION_IDS_COL => ['required'],
                ];
                break;
            case 'addQuestionToStudents':
                $rules = [
                    cn::EXAM_TABLE_QUESTION_IDS_COL => ['required'],
                ];
                break;
            default:
                break;
        }
        return $rules;
    }

    public static function rulesMessages($request = null,$action = ''){
        $messages = [];
        switch ($action) {
            case 'create':
                $messages = [
                    cn::EXAM_TYPE_COLS.'.required' => __('validation.please_select_test_type'),
                    cn::EXAM_TABLE_TITLE_COLS.'.required' => __('validation.please_enter_title'),
                    cn::EXAM_TABLE_FROM_DATE_COLS.'.required' => __('validation.please_enter_from_date'),
                    cn::EXAM_TABLE_TO_DATE_COLS.'.required' => __('validation.please_enter_to_date'),
                    cn::EXAM_TABLE_RESULT_DATE_COLS.'.required' => __('validation.please_enter_result_date'),
                ];
                if($request->unlimited_time != 1){
                    $messages = array_merge($messages, [
                        'time_duration.required' => "Please Enter Duration",
                    ]);
                }
                break;
            case 'update':
                $messages = [
                    cn::EXAM_TYPE_COLS.'.required' => __('validation.please_select_exam_type'),
                    cn::EXAM_TABLE_TITLE_COLS.'.required' => __('validation.please_enter_title'),
                    cn::EXAM_TABLE_FROM_DATE_COLS.'.required' => __('validation.please_enter_from_date'),
                    cn::EXAM_TABLE_TO_DATE_COLS.'.required' => __('validation.please_enter_to_date'),
                    cn::EXAM_TABLE_RESULT_DATE_COLS.'.required' => __('validation.please_enter_result_date'),
                ];
                break;
            case 'addQuestionToExams':
                $messages = [
                    cn::EXAM_TABLE_QUESTION_IDS_COL.'.required' => __('validation.select_atleast_1_question_to_add_this_exam'),
                ];
                break;
            case 'addQuestionToStudents':
                $messages = [
                    cn::EXAM_TABLE_QUESTION_IDS_COL.'.required' => __('validation.select_atleast_1_student_to_assign_this_exam'),
                ];
                break;
        }
        return $messages;
    }
    
    //Get Current Curriculum Year name
    public function getCurrentCurriculumYear($CurriculumYear = null){
        $CurrentCurriculumYear = '';
        if(!empty($CurriculumYear)){
            $CurriculumData = CurriculumYear::find($CurriculumYear);
            if(isset($CurriculumData) && !empty($CurriculumData)){
                $CurrentCurriculumYear = $CurriculumData->year;
            }
        }
        return $CurrentCurriculumYear;
    }

    public function attempt_exams(){
        return $this->hasMany(AttemptExams::class,cn::ATTEMPT_EXAMS_EXAM_ID, cn::EXAM_TABLE_ID_COLS);
    }

    public function user(){
        return $this->hasOne(User::class,cn::USERS_ID_COL,cn::EXAM_TABLE_STUDENT_IDS_COL);
    }

    public function examSchoolGradeClass(){
        return $this->hasMany(ExamGradeClassMappingModel::class,cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_EXAM_ID_COL,cn::EXAM_TABLE_ID_COLS);
    }

    public function ExamSchoolMapping(){
        return $this->hasMany(ExamSchoolMapping::class,cn::EXAM_SCHOOL_MAPPING_EXAM_ID_COL,cn::EXAM_SCHOOL_MAPPING_ID_COL);
    }

    public function ExamGradeClassConfigurations(){
        return $this->hasOne(ExamGradeClassMappingModel::class,cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_EXAM_ID_COL,cn::EXAM_TABLE_ID_COLS);
    }

    public function examCreditPointRules(){
        return $this->hasMany(ExamCreditPointRulesMapping::class,cn::EXAM_CREDIT_POINT_RULES_MAPPING_EXAM_ID_COL,cn::EXAM_TABLE_ID_COLS);
    }
}
