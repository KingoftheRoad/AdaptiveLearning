<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Constants\DbConstant as cn;
use Kyslik\ColumnSortable\Sortable;
use Illuminate\Database\Eloquent\SoftDeletes;

class LearningsObjectives extends Model
{
    use HasFactory, SoftDeletes,Sortable;

    protected $table = cn::LEARNING_OBJECTIVES_TABLE_NAME;
    
    public $fillable = [
        cn::LEARNING_OBJECTIVES_STUDY_FOCI_COL,
        cn::LEARNING_OBJECTIVES_TITLE_COL,
        cn::LEARNING_OBJECTIVES_TITLE_EN_COL,
        cn::LEARNING_OBJECTIVES_TITLE_CH_COL,
        cn::LEARNING_OBJECTIVES_LEARNING_UNITID_COL,
        cn::LEARNING_OBJECTIVES_CODE_COL,
        cn::LEARNING_OBJECTIVES_IS_AVAILABLE_QUESTIONS_COL,
        cn::LEARNING_OBJECTIVES_STATUS_COL
    ];

    public $timestamps = true;

    // Enable sortable columns name
    public $sortable = [
        cn::LEARNING_OBJECTIVES_STUDY_FOCI_COL,
        cn::LEARNING_OBJECTIVES_TITLE_COL,
        cn::LEARNING_OBJECTIVES_TITLE_EN_COL,
        cn::LEARNING_OBJECTIVES_TITLE_CH_COL,
        cn::LEARNING_OBJECTIVES_LEARNING_UNITID_COL,
        cn::LEARNING_OBJECTIVES_CODE_COL,
        cn::LEARNING_OBJECTIVES_IS_AVAILABLE_QUESTIONS_COL,
        cn::LEARNING_OBJECTIVES_STATUS_COL
    ];    
    
    /**
    ** Validation Rules for strands
    **/
    public static function rules($request = null, $action = '', $id = null){
        switch ($action) {
            case 'create':
                $rules = [
                    cn::LEARNING_OBJECTIVES_STUDY_FOCI_COL => ['required'],
                    cn::LEARNING_OBJECTIVES_LEARNING_UNITID_COL => ['required'],
                    cn::LEARNING_OBJECTIVES_TITLE_EN_COL => ['required'],
                    cn::LEARNING_OBJECTIVES_TITLE_CH_COL => ['required'],
                    cn::LEARNING_OBJECTIVES_CODE_COL => ['required'],
                ];
                break;
            case 'update':
                $rules = [
                    cn::LEARNING_OBJECTIVES_STUDY_FOCI_COL => ['required'],
                    cn::LEARNING_OBJECTIVES_LEARNING_UNITID_COL => ['required'],
                    cn::LEARNING_OBJECTIVES_TITLE_EN_COL => ['required'],
                    cn::LEARNING_OBJECTIVES_TITLE_CH_COL => ['required'],
                    cn::LEARNING_OBJECTIVES_CODE_COL => ['required'],
                ];
                break;
            default:
                break;
        }
        return $rules;
    }

    /**
    ** Additional Validation Massages for strand
    **/
    public static function rulesMessages($action = ''){
        $messages = [];
        switch ($action) {
            case 'create':
                $messages = [
                    cn::LEARNING_OBJECTIVES_STUDY_FOCI_COL.'.required' => __('validation.please_enter_foci_number'),
                    cn::LEARNING_OBJECTIVES_LEARNING_UNITID_COL.'.required' => __('validation.please_select_learning_unit'),
                    cn::LEARNING_OBJECTIVES_TITLE_EN_COL.'.required' => __('validation.please_enter_english_name'),
                    cn::LEARNING_OBJECTIVES_TITLE_CH_COL.'.required' => __('validation.please_enter_chinese_name'),
                    cn::LEARNING_OBJECTIVES_CODE_COL.'.required' => __('validation.please_enter_code'),
                ];
                break;
            case 'update':
                $messages = [
                    cn::LEARNING_OBJECTIVES_STUDY_FOCI_COL.'.required' => __('validation.please_enter_foci_number'),
                    cn::LEARNING_OBJECTIVES_LEARNING_UNITID_COL.'.required' => __('validation.please_select_learning_unit'),
                    cn::LEARNING_OBJECTIVES_TITLE_EN_COL.'.required' => __('validation.please_enter_english_name'),
                    cn::LEARNING_OBJECTIVES_TITLE_CH_COL.'.required' => __('validation.please_enter_chinese_name'),
                    cn::LEARNING_OBJECTIVES_CODE_COL.'.required' => __('validation.please_enter_code'),
                ];
                break;
        }
        return $messages;
    }

    public function scopeIsAvailableQuestion($query){
         return $query->where(cn::LEARNING_OBJECTIVES_IS_AVAILABLE_QUESTIONS_COL,'yes');
    }
}
