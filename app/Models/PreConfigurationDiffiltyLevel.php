<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Constants\DbConstant As cn;
use Kyslik\ColumnSortable\Sortable;
use Illuminate\Database\Eloquent\SoftDeletes;

class PreConfigurationDiffiltyLevel extends Model
{
    use  SoftDeletes,HasFactory, Sortable;
    protected $table = cn::PRE_CONFIGURE_DIFFICULTY_TABLE_NAME;

    public $fillable = [
        cn::PRE_CONFIGURE_DIFFICULTY_ID_COL,
        cn::PRE_CONFIGURE_DIFFICULTY_DIFFICULTY_LEVEL_COL,
        cn::PRE_CONFIGURE_DIFFICULTY_DIFFICULTY_LEVEL_NAME_EN_COL,
        cn::PRE_CONFIGURE_DIFFICULTY_DIFFICULTY_LEVEL_NAME_CH_COL,
        cn::PRE_CONFIGURE_DIFFICULTY_DIFFICULTY_LEVEL_COLOR_COL,
        cn::PRE_CONFIGURE_DIFFICULTY_TITLE_COL,
        cn::PRE_CONFIGURE_DIFFICULTY_STATUS_COL,
    ];

    public $timestamps = true;

    // Enable sortable columns name
    public $sortable = [cn::PRE_CONFIGURE_DIFFICULTY_DIFFICULTY_LEVEL_COL,cn::PRE_CONFIGURE_DIFFICULTY_DIFFICULTY_LEVEL_NAME_COL,cn::PRE_CONFIGURE_DIFFICULTY_DIFFICULTY_LEVEL_COLOR_COL,cn::PRE_CONFIGURE_DIFFICULTY_TITLE_COL,cn::PRE_CONFIGURE_DIFFICULTY_STATUS_COL];                

    /**
    ** Validation Rules for school
    **/
    public static function rules($request = null, $action = '', $id = null){
        switch ($action) {
            case 'create':
                $rules = [
                    cn::PRE_CONFIGURE_DIFFICULTY_DIFFICULTY_LEVEL_COL => ['required'],
                    cn::PRE_CONFIGURE_DIFFICULTY_TITLE_COL => ['required'],
                    cn::PRE_CONFIGURE_DIFFICULTY_DIFFICULTY_LEVEL_NAME_EN_COL => ['required'],
                    cn::PRE_CONFIGURE_DIFFICULTY_DIFFICULTY_LEVEL_NAME_CH_COL => ['required']
                ];
                break;
            case 'update':
                $rules = [
                    cn::PRE_CONFIGURE_DIFFICULTY_DIFFICULTY_LEVEL_COL => ['required'],
                    cn::PRE_CONFIGURE_DIFFICULTY_TITLE_COL => ['required'],
                    cn::PRE_CONFIGURE_DIFFICULTY_DIFFICULTY_LEVEL_NAME_EN_COL => ['required'],
                    cn::PRE_CONFIGURE_DIFFICULTY_DIFFICULTY_LEVEL_NAME_CH_COL => ['required']
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
                    cn::PRE_CONFIGURE_DIFFICULTY_DIFFICULTY_LEVEL_COL.'.required' => __('validation.please_select_difficulty_level'),
                    cn::PRE_CONFIGURE_DIFFICULTY_TITLE_COL.'.required' => __('validation.please_enter_title'),
                    cn::PRE_CONFIGURE_DIFFICULTY_DIFFICULTY_LEVEL_NAME_EN_COL.'.required' => __('validation.please_enter_english_name'),
                    cn::PRE_CONFIGURE_DIFFICULTY_DIFFICULTY_LEVEL_NAME_CH_COL.'.required' => __('validation.please_enter_chinese_name')
                ];
                break;
            case 'update':
                $messages = [
                    cn::PRE_CONFIGURE_DIFFICULTY_DIFFICULTY_LEVEL_COL.'.required' => __('validation.please_select_difficulty_level'),
                    cn::PRE_CONFIGURE_DIFFICULTY_TITLE_COL.'.required' => __('validation.please_enter_title'),
                    cn::PRE_CONFIGURE_DIFFICULTY_DIFFICULTY_LEVEL_NAME_EN_COL.'.required' => __('validation.please_enter_english_name'),
                    cn::PRE_CONFIGURE_DIFFICULTY_DIFFICULTY_LEVEL_NAME_CH_COL.'.required' => __('validation.please_enter_chinese_name')
                ];
                break;
        }
        return $messages;
    }
}
