<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Kyslik\ColumnSortable\Sortable;
use Illuminate\Notifications\Notifiable;
use App\Constants\DbConstant as cn;
use App\Models\Grades;
use App\Models\User;
use App\Models\GradeClassMapping;

class TeachersClassSubjectAssign extends Model
{
    use SoftDeletes, HasFactory, Notifiable, Sortable;
    
    protected $table = cn::TEACHER_CLASS_SUBJECT_TABLE_NAME;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        cn::TEACHER_CLASS_SUBJECT_SCHOOL_ID_COL,
        cn::TEACHER_CLASS_SUBJECT_TEACHER_ID_COL,
        cn::TEACHER_CLASS_SUBJECT_CLASS_ID_COL,
        cn::TEACHER_CLASS_SUBJECT_SUBJECT_ID_COL,
        cn::TEACHER_CLASS_SUBJECT_STATUS_COL,
        cn::TEACHER_CLASS_SUBJECT_CLASS_NAME_ID_COL,
        cn::TEACHER_CLASS_SUBJECT_CURRICULUM_YEAR_ID_COL
    ];

    // Enable sortable columns name
    public $sortable = [
        cn::TEACHER_CLASS_SUBJECT_SCHOOL_ID_COL,
        cn::TEACHER_CLASS_SUBJECT_TEACHER_ID_COL,
        cn::TEACHER_CLASS_SUBJECT_CLASS_ID_COL,
        cn::TEACHER_CLASS_SUBJECT_SUBJECT_ID_COL,
        cn::TEACHER_CLASS_SUBJECT_STATUS_COL,
        cn::TEACHER_CLASS_SUBJECT_CLASS_NAME_ID_COL
    ];

    public function teachers(){
        return $this->hasOne(User::Class, cn::USERS_ID_COL, cn::TEACHER_CLASS_SUBJECT_TEACHER_ID_COL);
    }

    /** USE : Validations */    
    public static function rules($request = null, $action = '', $id = null){
        switch ($action) {
            case 'create':
                $rules = [
                    'teacher_id' => ['required'],
                    'class_id' => ['required'],
                    'subject_id' => ['required'],
                    'class_type' => ['required']
                ];
                break;
            case 'update':
                $rules = [
                    'teacher_id' => ['required'],
                    'class_id' => ['required'],
                    'subject_id' => ['required'],
                    'class_type' => ['required']
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
                    'teacher_id.required' => __('validation.please_select_teacher'),
                    'class_id.required' => __('validation.please_select_grade'),
                    'subject_id.required' => __('validation.please_select_subject'),
                    'class_type.required' =>  __('validation.please_select_class_type'),
                ];
                break;
                case 'update':
                    $messages = [
                        'teacher_id.required' => __('validation.please_select_teacher'),
                        'class_id.required' => __('validation.please_select_grade'),
                        'subject_id.required' => __('validation.please_select_subject'),
                        'class_type.required' =>  __('validation.please_select_class_type'),
                    ];
                    break;
        }
        return $messages;
    }

    public function getSubjectNameById(){
        $subkey=cn::TEACHER_CLASS_SUBJECT_SUBJECT_ID_COL;
        if($this->$subkey != ""){
            $data = Subjects::whereIn('id',explode(',',$this->$subkey))->select(\DB::raw('GROUP_CONCAT('.cn::SUBJECTS_TABLE_NAME.'.'.cn::SUBJECTS_NAME_COL.') as '.cn::SUBJECTS_TABLE_NAME))->get()->toArray();
            if(isset($data) && !empty($data)){
               return $data[0][cn::SUBJECTS_TABLE_NAME];
            }
        }
        return $this->cn::TEACHER_CLASS_SUBJECT_SUBJECT_ID_COL;
    }

    public function getTeacher(){
        return $this->hasOne(User::Class, cn::USERS_ID_COL, cn::TEACHER_CLASS_SUBJECT_TEACHER_ID_COL);
    }

    public function getClass(){
        return $this->hasOne(Grades::Class, cn::GRADES_ID_COL, cn::TEACHER_CLASS_SUBJECT_CLASS_ID_COL);
    } 
}
