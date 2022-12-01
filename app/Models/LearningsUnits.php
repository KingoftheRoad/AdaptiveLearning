<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Constants\DbConstant as cn;
use Illuminate\Database\Eloquent\SoftDeletes;

class LearningsUnits extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = cn::LEARNING_UNITS_TABLE_NAME;
    
    public $fillable = [
        cn::LEARNING_UNITS_NAME_COL,
        cn::LEARNING_UNITS_NAME_EN_COL,
        cn::LEARNING_UNITS_NAME_CH_COL,
        cn::LEARNING_UNITS_STRANDID_COL,
        cn::LEARNING_UNITS_CODE_COL,
        cn::LEARNING_UNITS_STATUS_COL
    ];

    public $timestamps = true; 

    /**
    ** Validation Rules for school
    **/

    public static function rules($request = null, $action = '', $id = null){
        switch ($action) {
            case 'create':
                $rules = [
                    // 'name' => ['required'],
                    cn::LEARNING_UNITS_NAME_EN_COL => ['required'],
                    cn::LEARNING_UNITS_NAME_CH_COL => ['required'],
                    cn::LEARNING_UNITS_STRANDID_COL => ['required'],
                    cn::LEARNING_UNITS_CODE_COL => ['required'],
                ];
                break;
            case 'update':
                $rules = [
                    // 'name' => ['required'],
                    cn::LEARNING_UNITS_NAME_EN_COL => ['required'],
                    cn::LEARNING_UNITS_NAME_CH_COL => ['required'],
                    cn::LEARNING_UNITS_STRANDID_COL => ['required'],
                    cn::LEARNING_UNITS_CODE_COL => ['required'],
                ];
                break;
            default:
                break;
        }
        return $rules;
    }

    /**
    ** Additional Validation Massages for School
    **/
    public static function rulesMessages($action = ''){
        $messages = [];
        switch ($action) {
            case 'create':
                $messages = [
                    cn::LEARNING_UNITS_NAME_EN_COL.'.required' => __('validation.please_enter_english_name'),
                    cn::LEARNING_UNITS_NAME_CH_COL.'.required' => __('validation.please_enter_chinese_name'),
                    cn::LEARNING_UNITS_STRANDID_COL.'.required' => __('validation.please_select_strand'),
                    cn::LEARNING_UNITS_CODE_COL.'.required' => __('validation.please_enter_code')
                ];
                break;
            case 'update':
                $messages = [
                    cn::LEARNING_UNITS_NAME_EN_COL.'.required' => __('validation.please_enter_english_name'),
                    cn::LEARNING_UNITS_NAME_CH_COL.'.required' => __('validation.please_enter_chinese_name'),
                    cn::LEARNING_UNITS_STRANDID_COL.'.required' => __('validation.please_select_strand'),
                    cn::LEARNING_UNITS_CODE_COL.'.required' => __('validation.please_enter_code')
                ];
                break;
        }
        return $messages;
    }

    public function Strands(){
        return $this->hasOne(Strands::Class,cn::STRANDS_ID_COL, cn::LEARNING_UNITS_STRANDID_COL);
    }
}
