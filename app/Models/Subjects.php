<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Kyslik\ColumnSortable\Sortable;
use Illuminate\Notifications\Notifiable;
use App\Constants\DbConstant as cn;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Auth;

class Subjects extends Model
{
    use HasFactory, Notifiable, Sortable,SoftDeletes;

    protected $table = cn::SUBJECTS_TABLE_NAME;
    
    public $fillable = [
        cn::SUBJECTS_NAME_COL,
        cn::SUBJECTS_CODE_COL,
        cn::SUBJECTS_SCHOOL_ID_COL,
        cn::SUBJECTS_STATUS_COL
    ];

    public $timestamps = true;

    public $sortable = [
        cn::SUBJECTS_NAME_COL,
        cn::SUBJECTS_CODE_COL,
        cn::SUBJECTS_SCHOOL_ID_COL,
        cn::SUBJECTS_STATUS_COL             
    ];

    public static function rules($request = null, $action = '', $id = null){
       $school_id=auth()->user()->school_id;
        switch ($action) {
            case 'create':
                $rules = [
                    // 'name' => 'required|regex:/^[A-Za-z0-9 ]+$/u|unique:subjects,name,NULL,id,deleted_at,NULL,school_id,'.$school_id.'',
                    //cn::SUBJECTS_NAME_COL => 'required|regex:/^[A-Za-z0-9 ]+$/u|unique:subjects,name,NULL',
                    cn::SUBJECTS_NAME_COL => 'required|regex:/^[A-Za-z0-9 ]+$/u',
                    cn::SUBJECTS_CODE_COL => ['required','regex:/^[A-Za-z0-9 ]+$/u']
                ];
                break;
            case 'update':
                $rules = [
                    cn::SUBJECTS_NAME_COL => ['required','regex:/^[A-Za-z0-9 ]+$/u'],
                    cn::SUBJECTS_CODE_COL => ['required','regex:/^[A-Za-z0-9 ]+$/u']
                ];
                break;
            default:
                break;
        }
        return $rules;
    }

    public static function rulesMessages($action = ''){
        $messages = [];
        switch ($action) {
            case 'create':
                $messages = [
                    cn::SUBJECTS_NAME_COL.'.required' => __('validation.please_enter_name'),
                    cn::SUBJECTS_NAME_COL.'.unique' => __('validation.subject_name_already_exists'),
                    cn::SUBJECTS_NAME_COL.'.regex' => __('validation.please_enter_alpha_numeric_value'),
                    cn::SUBJECTS_CODE_COL.'.required' => __('validation.please_enter_code'),
                    cn::SUBJECTS_CODE_COL.'.regex' => __('validation.please_enter_alpha_numeric_value')
                ];
                break;
            case 'update':
                $messages = [
                    cn::SUBJECTS_NAME_COL.'.required' => __('validation.please_enter_name'),
                    cn::SUBJECTS_NAME_COL.'.unique' => __('validation.subject_name_already_exists'),
                    cn::SUBJECTS_NAME_COL.'.regex' => __('validation.please_enter_alpha_numeric_value'),
                    cn::SUBJECTS_CODE_COL.'.required' => __('validation.please_enter_code'),
                    cn::SUBJECTS_CODE_COL.'.regex' => __('validation.please_enter_alpha_numeric_value')
                ];
                break;
        }
        return $messages;
    }
    
    public function schools(){
        return $this->hasOne(School::Class, cn::SCHOOL_ID_COLS, cn::SUBJECTS_ID_COL);
    }
    
    public function TeachersClassSubjectAssign(){
        return $this->belongsToMany(TeachersClassSubjectAssign::class)->withPivot('is_free');
    }

    public function class(){
        return $this->belongsToMany(Subjects::class,ClassSubjectMapping::class,cn::CLASS_SUBJECT_MAPPING_SUBJECT_ID_COL,cn::CLASS_SUBJECT_MAPPING_CLASS_ID_COL)->withTimestamps();
    }
}
