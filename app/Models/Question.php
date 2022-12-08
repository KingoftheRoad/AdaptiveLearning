<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Kyslik\ColumnSortable\Sortable;
use App\Constants\DbConstant as cn;
use App\Models\Answer;
use App\Models\PreConfigurationDiffiltyLevel;
use App\Models\StrandUnitsObjectivesMappings;
use Illuminate\Validation\Rule;

class Question extends Model
{
    use SoftDeletes, HasFactory,Sortable;
    protected $table = cn::QUESTION_TABLE_NAME;

    public $fillable =[
        cn::QUESTION_OBJECTIVE_MAPPING_ID_COL,
        cn::QUESTION_QUESTION_CODE_COL,
        cn::QUESTION_NAMING_STRUCTURE_CODE_COL,
        cn::QUESTION_QUESTION_UNIQUE_CODE_COL,
        cn::QUESTION_MARKS_COL,
        cn::QUESTION_BANK_UPDATED_BY_COL,
        cn::QUESTION_BANK_SCHOOL_ID_COL,
        cn::QUESTION_QUESTION_EN_COL,
        cn::QUESTION_QUESTION_CH_COL,
        cn::QUESTION_QUESTION_TYPE_COL,
        cn::QUESTION_DIFFICULTY_LEVEL_COL,
        cn::QUESTINON_STATUS_COL,
        cn::QUESTION_GENERAL_HINTS_EN,
        cn::QUESTION_GENERAL_HINTS_CH,
        cn::QUESTION_GENERAL_HINTS_VIDEO_ID_EN,
        cn::QUESTION_FULL_SOLUTION_EN,
        cn::QUESTION_FULL_SOLUTION_CH,
        cn::QUESTION_E_COL,
        cn::QUESTION_F_COL,
        cn::QUESTION_G_COL,
        cn::QUESTION_IS_APPROVED_COL,
    ];
    public $timestamps = true;

    // Enable sortable columns name
    public $sortable = [
                    cn::QUESTION_NAMING_STRUCTURE_CODE_COL, 
                    cn::QUESTION_QUESTION_EN_COL,
                    cn::QUESTION_DIFFICULTY_LEVEL_COL, 
                    cn::QUESTINON_STATUS_COL,
                    cn::QUESTION_FULL_SOLUTION_EN,
                    cn::QUESTION_FULL_SOLUTION_CH,
                    cn::QUESTION_IS_APPROVED_COL,
                ];

    /**
    ** Validation Rules for users
    **/
    public static function rules($request = null, $action = '', $id = null){
        switch ($action) {
            case 'create':
                $rules = [
                    cn::QUESTION_NAMING_STRUCTURE_CODE_COL => ['required',Rule::unique(cn::QUESTION_TABLE_NAME)->whereNull(cn::QUESTION_DELETED_AT_COL)],
                    cn::QUESTION_QUESTION_EN_COL => ['required'],
                    cn::QUESTION_QUESTION_CH_COL => ['required'],
                    cn::ANSWER_ANSWER1_EN_COL => ['required'],
                    cn::ANSWER_ANSWER2_EN_COL => ['required'],
                    cn::ANSWER_CORRECT_ANSWER_EN_COL => ['required'],
                    cn::ANSWER_CORRECT_ANSWER_CH_COL => ['required'],
                    cn::ANSWER_ANSWER1_CH_COL => ['required'],
                    cn::ANSWER_ANSWER2_CH_COL => ['required'],
                    cn::QUESTION_IS_APPROVED_COL => ['required']
                ];
                break;
            case 'update':
                $rules = [
                    cn::QUESTION_NAMING_STRUCTURE_CODE_COL => ['required',Rule::unique(cn::QUESTION_TABLE_NAME)->ignore($id)->whereNull(cn::QUESTION_DELETED_AT_COL)],
                    cn::QUESTION_QUESTION_EN_COL => ['required'],
                    cn::QUESTION_QUESTION_CH_COL => ['required'],
                    cn::ANSWER_ANSWER1_EN_COL => ['required'],
                    cn::ANSWER_ANSWER2_EN_COL => ['required'],
                    cn::ANSWER_CORRECT_ANSWER_EN_COL => ['required'],
                    cn::ANSWER_CORRECT_ANSWER_CH_COL => ['required'],
                    cn::ANSWER_ANSWER1_CH_COL => ['required'],
                    cn::ANSWER_ANSWER2_CH_COL => ['required'],
                    cn::QUESTION_IS_APPROVED_COL => ['required']
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
            case 'create':
                $messages = [
                    cn::QUESTION_NAMING_STRUCTURE_CODE_COL.'.required' => __('validation.please_enter_question_code'),
                    cn::QUESTION_NAMING_STRUCTURE_CODE_COL.'.unique' => __('languages.question_code_is_already_exists'),
                    cn::QUESTION_QUESTION_EN_COL.'.required' => __('validation.please_enter_question_of_english'),
                    cn::QUESTION_QUESTION_CH_COL.'.required' => __('validation.please_enter_question_of_chinese'),
                    cn::ANSWER_ANSWER1_EN_COL.'.required' => __('validation.please_enter_first_answer_of_english'),
                    cn::ANSWER_ANSWER2_EN_COL.'.required' => __('validation.please_enter_second_answer_of_english'),
                    cn::ANSWER_ANSWER1_CH_COL.'.required' => __('validation.please_enter_first_answer_of_chinese'),
                    cn::ANSWER_ANSWER2_CH_COL.'.required' => __('validation.please_enter_first_answer_of_chinese'),
                    cn::QUESTION_DIFFICULTY_LEVEL_COL.'.required' => __('validation.please_select_difficulty_level'),
                    cn::QUESTION_IS_APPROVED_COL.'.required' => __('validation.please_select_option')
                ];
                break;
            case 'update':
                $messages = [
                    cn::QUESTION_NAMING_STRUCTURE_CODE_COL.'.required' => __('validation.please_enter_question_code'),
                    cn::QUESTION_NAMING_STRUCTURE_CODE_COL.'.unique' => __('languages.question_code_is_already_exists'),
                    cn::QUESTION_QUESTION_EN_COL.'.required' => __('validation.please_enter_question_of_english'),
                    cn::QUESTION_QUESTION_CH_COL.'.required' => __('validation.please_enter_question_of_chinese'),
                    cn::ANSWER_ANSWER1_EN_COL.'.required' => __('validation.please_enter_first_answer_of_english'),
                    cn::ANSWER_ANSWER2_EN_COL.'.required' => __('validation.please_enter_second_answer_of_english'),
                    cn::ANSWER_ANSWER1_CH_COL.'.required' => __('validation.please_enter_first_answer_of_chinese'),
                    cn::ANSWER_ANSWER2_CH_COL.'.required' => __('validation.please_enter_first_answer_of_chinese'),
                    cn::QUESTION_DIFFICULTY_LEVEL_COL.'.required' => __('validation.please_select_difficulty_level'),
                    cn::QUESTION_IS_APPROVED_COL.'.required' => __('validation.please_select_option')
                ];
                break;
        }
        return $messages;
    }

    public function answers(){
        return $this->hasOne(Answer::class, cn::ANSWER_QUESTION_ID_COL, cn::QUESTION_TABLE_ID_COL);
    }

    public function PreConfigurationDifficultyLevel(){
        return $this->hasOne(PreConfigurationDiffiltyLevel::class, cn::PRE_CONFIGURE_DIFFICULTY_ID_COL, cn::QUESTION_DIFFICULTY_LEVEL_COL);
    }

    public function objectiveMapping(){
        $currentLanguage = 'EN';
        $STRANDS_NAME = cn::STRANDS_NAME_EN_COL;
        $LEARNING_UNITS_NAME = cn::LEARNING_UNITS_NAME_EN_COL;
        $LEARNING_OBJECTIVES_TITLE = cn::LEARNING_OBJECTIVES_TITLE_EN_COL;
        if(app()->getLocale() == 'ch'){
            $STRANDS_NAME = cn::STRANDS_NAME_CH_COL;
            $LEARNING_UNITS_NAME = cn::LEARNING_UNITS_NAME_CH_COL;
            $LEARNING_OBJECTIVES_TITLE = cn::LEARNING_OBJECTIVES_TITLE_CH_COL;
        }
        return $this->hasOne(StrandUnitsObjectivesMappings::class, cn::OBJECTIVES_MAPPINGS_ID_COL, cn::QUESTION_OBJECTIVE_MAPPING_ID_COL)
                ->join(cn::GRADES_TABLE_NAME,cn::OBJECTIVES_MAPPINGS_TABLE_NAME.'.'.cn::OBJECTIVES_MAPPINGS_GRADE_ID_COL,'=',cn::GRADES_TABLE_NAME.'.'.cn::GRADES_ID_COL)
                ->join(cn::SUBJECTS_TABLE_NAME,cn::OBJECTIVES_MAPPINGS_TABLE_NAME.'.'.cn::OBJECTIVES_MAPPINGS_SUBJECT_ID_COL,'=',cn::SUBJECTS_TABLE_NAME.'.'.cn::SUBJECTS_ID_COL)
                ->join(cn::STRANDS_TABLE_NAME,cn::OBJECTIVES_MAPPINGS_TABLE_NAME.'.'.cn::OBJECTIVES_MAPPINGS_STRAND_ID_COL,'=',cn::STRANDS_TABLE_NAME.'.'.cn::STRANDS_ID_COL)
                ->join(cn::LEARNING_UNITS_TABLE_NAME,cn::OBJECTIVES_MAPPINGS_TABLE_NAME.'.'.cn::OBJECTIVES_MAPPINGS_LEARNING_UNIT_ID_COL,'=',cn::LEARNING_UNITS_TABLE_NAME.'.'.cn::LEARNING_UNITS_ID_COL)
                ->join(cn::LEARNING_OBJECTIVES_TABLE_NAME,cn::OBJECTIVES_MAPPINGS_TABLE_NAME.'.'.cn::OBJECTIVES_MAPPINGS_LEARNING_OBJECTIVES_ID_COL,'=',cn::LEARNING_OBJECTIVES_TABLE_NAME.'.'.cn::LEARNING_OBJECTIVES_ID_COL)
                ->select(
                    cn::OBJECTIVES_MAPPINGS_TABLE_NAME.'.*',cn::GRADES_TABLE_NAME.'.'.cn::GRADES_NAME_COL.' as gradeName',
                    cn::SUBJECTS_TABLE_NAME.'.'.cn::SUBJECTS_NAME_COL.' as subjectName',
                    cn::STRANDS_TABLE_NAME.'.'.$STRANDS_NAME.' as strandName',
                    cn::LEARNING_UNITS_TABLE_NAME.'.'.$LEARNING_UNITS_NAME.' as learningUnitsName',
                    cn::LEARNING_OBJECTIVES_TABLE_NAME.'.'.cn::LEARNING_OBJECTIVES_STUDY_FOCI_COL.' as learningObjectivesNumber',
                    cn::LEARNING_OBJECTIVES_TABLE_NAME.'.'.$LEARNING_OBJECTIVES_TITLE.' as learningObjectivesTitle'
                );
    }

    public function SunjectNameFromQuestion(){
        return $this->hasOne(StrandUnitsObjectivesMappings::class, cn::OBJECTIVES_MAPPINGS_ID_COL, cn::QUESTION_OBJECTIVE_MAPPING_ID_COL)
                ->leftjoin(cn::SUBJECTS_TABLE_NAME,cn::OBJECTIVES_MAPPINGS_TABLE_NAME.'.'.cn::OBJECTIVES_MAPPINGS_SUBJECT_ID_COL,'=',cn::SUBJECTS_TABLE_NAME.'.'.cn::SUBJECTS_ID_COL)
                ->select(cn::OBJECTIVES_MAPPINGS_TABLE_NAME.'.*',cn::SUBJECTS_TABLE_NAME.'.'.cn::SUBJECTS_NAME_COL.' as subjectName',
        );
    }

    public function schools(){
        return $this->hasOne(School::Class, cn::SCHOOL_ID_COLS, cn::QUESTION_BANK_SCHOOL_ID_COL);
    }
}
