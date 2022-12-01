<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Constants\DbConstant as cn;
use Kyslik\ColumnSortable\Sortable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Validation\Rule;
use App\Traits\Common;

class School extends Model
{
    use  Common, SoftDeletes,HasFactory, Sortable;
    protected $table = cn::SCHOOL_TABLE_NAME;

    public $fillable = [
        cn::SCHOOL_ID_COLS,
        cn::SCHOOL_SCHOOL_NAME_COL,
        cn::SCHOOL_SCHOOL_NAME_EN_COL,
        cn::SCHOOL_SCHOOL_NAME_CH_COL,
        cn::SCHOOL_SCHOOL_CODE_COL,
        cn::SCHOOL_SCHOOL_EMAIL_COL,
        cn::SCHOOL_SCHOOL_ADDRESS,
        cn::SCHOOL_SCHOOL_ADDRESS_EN_COL,
        cn::SCHOOL_SCHOOL_ADDRESS_CH_COL,
        cn::SCHOOL_SCHOOL_CITY,
        cn::SCHOOL_STARTTIME_COL,
        cn::SCHOOL_SCHOOL_STATUS,

    ];

    public $timestamps = true;

    // Enable sortable columns name
    public $sortable = [
        cn::SCHOOL_ID_COLS,
        cn::SCHOOL_SCHOOL_NAME_COL,
        cn::SCHOOL_SCHOOL_NAME_EN_COL,
        cn::SCHOOL_SCHOOL_NAME_CH_COL,
        cn::SCHOOL_SCHOOL_CODE_COL,
        cn::SCHOOL_SCHOOL_EMAIL_COL,
        cn::SCHOOL_SCHOOL_ADDRESS,
        cn::SCHOOL_SCHOOL_ADDRESS_EN_COL,
        cn::SCHOOL_SCHOOL_ADDRESS_CH_COL,
        cn::SCHOOL_SCHOOL_CITY,
        cn::SCHOOL_STARTTIME_COL,
        cn::SCHOOL_SCHOOL_STATUS,
    ];

    protected $appends = ['DecryptSchoolNameEn','DecryptSchoolNameCh'];

    public function getDecryptSchoolNameEnAttribute(){
        $school_name_en = null;
        if(!empty($this->school_name_en)){
            $school_name_en = $this->decrypt($this->school_name_en);
        }else{
            $school_name_en = $this->school_name;
        }
        return $school_name_en;
    }

    public function getDecryptSchoolNameChAttribute(){
        $school_name_ch = null;
        if(!empty($this->school_name_ch)){
            $school_name_ch = $this->decrypt($this->school_name_ch);
        }else{
            $school_name_ch = $this->school_name;
        }
        return $school_name_ch;
    }

    public function gradeSchoolMapping(){
        return $this->hasMany(GradeSchoolMappings::Class,cn::GRADES_MAPPING_SCHOOL_ID_COL,cn::SCHOOL_ID_COLS);
    }

    
    /**
    ** Validation Rules for school
    **/
    public static function rules($request = null, $action = '', $id = null){
        switch ($action) {
            case 'create':
                $rules = [
                    cn::SCHOOL_SCHOOL_NAME_EN_COL => ['required'],
                    cn::SCHOOL_SCHOOL_NAME_CH_COL => ['required'],
                    cn::SCHOOL_SCHOOL_CODE_COL => ['required'],
                    cn::USERS_EMAIL_COL   => ['required', Rule::unique(cn::USERS_TABLE_NAME)->whereNull(cn::USERS_DELETED_AT_COL)],
                    cn::USERS_PASSWORD_COL => ['required'],
                ];
                break;
            case 'update':
                $rules = [
                    cn::SCHOOL_SCHOOL_NAME_EN_COL => ['required'],
                    cn::SCHOOL_SCHOOL_NAME_CH_COL => ['required'],
                    cn::SCHOOL_SCHOOL_CODE_COL => ['required'],
                    cn::USERS_EMAIL_COL => [Rule::unique(cn::USERS_TABLE_NAME)->where(function ($query) use($id) {
                        return $query->whereNull(cn::USERS_DELETED_AT_COL)
                                    ->whereNotIn(cn::USERS_SCHOOL_ID_COL,[$id])
                                    ->where(cn::USERS_ROLE_ID_COL,cn::SCHOOL_ROLE_ID);
                        })
                    ],
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
                    cn::SCHOOL_SCHOOL_NAME_EN_COL.'.required' => __('validation.please_enter_english_school_name'),
                    cn::SCHOOL_SCHOOL_NAME_CH_COL.'.required' => __('validation.please_enter_chinese_school_name'),
                    cn::SCHOOL_SCHOOL_CODE_COL.'.required' => __('validation.please_enter_school_code'),
                    cn::USERS_EMAIL_COL.'.required' => __('validation.please_enter_email'),
                    cn::USERS_PASSWORD_COL.'.required' => __('validation.please_enter_password')
                ];
                break;
            case 'update':
                $messages = [
                    cn::SCHOOL_SCHOOL_NAME_EN_COL.'.required' => __('validation.please_enter_english_school_name'),
                    cn::SCHOOL_SCHOOL_NAME_CH_COL.'.required' => __('validation.please_enter_chinese_school_name'),
                    cn::SCHOOL_SCHOOL_CODE_COL.'.required' => __('validation.please_enter_school_code'),
                    cn::USERS_EMAIL_COL.'.required' => __('validation.please_enter_email'),
                ];
                break;
        }
        return $messages;
    }
}


