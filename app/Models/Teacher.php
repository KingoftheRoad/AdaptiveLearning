<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Kyslik\ColumnSortable\Sortable;
use Illuminate\Notifications\Notifiable;
use App\Constants\DbConstant as cn;

class Teacher extends Model
{
    use HasFactory, Notifiable, Sortable;
    protected $table = cn::TEACHER_TABLE_NAME;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        cn::TEACHER_SCHOOL_ID_COL,
        cn::TEACHER_NAME_COL,
        cn::TEACHER_EMAIL_COL,
        cn::TEACHER_MOBILE_NO_COL,
        cn::TEACHER_ADDRESS_COL,
        cn::TEACHER_GENDER_COL,
        cn::TEACHER_DATE_OF_BIRTH_COL,
        cn::TEACHER_STATUS_COL
    ];

    // Enable sortable columns name
    public $sortable = [
        cn::TEACHER_NAME_COL, 
        cn::TEACHER_EMAIL_COL,
        cn::TEACHER_MOBILE_NO_COL,
        cn::TEACHER_DATE_OF_BIRTH_COL, 
        cn::TEACHER_GENDER_COL,
        cn::TEACHER_SCHOOL_ID_COL,
        cn::TEACHER_STATUS_COL
    ];

    /**
    ** Validation Rules for TEACHER
    **/
    public static function rules($request = null, $action = '', $id = null){
        switch ($action) {
            case 'create':
                $rules = [
                    'user_name' => ['required','regex:/^[A-Za-z0-9 ]+$/u'],
                    'email' => ['required','unique:teacher,email'],
                    'gender' => ['required'],
                    'date_of_birth' => ['required'],
                    'city' => ['required']
                ];
                break;
            case 'update':
                $rules = [
                    'user_name' => ['required','regex:/^[A-Za-z0-9 ]+$/u'],
                    'gender' => ['required'],
                    'date_of_birth' => ['required'],
                ];
                break;
            default:
                break;
        }
        return $rules;
    }

    /**
    ** Additional Validation Massages for TEACHER
    **/
    public static function rulesMessages($action = ''){
        $messages = [];
        switch ($action) {
            case 'create':
                $messages = [
                    'email.unique' => "Email already exists"
                ];
                break;
            case 'update':
                $messages = [
                    'email.unique' => "Email already exists"
                ];
                break;
        }
        return $messages;
    }

    public function schools(){
        return $this->hasOne(School::Class, cn::SCHOOL_ID_COLS, cn::TEACHER_SCHOOL_ID_COL);
    }
}
