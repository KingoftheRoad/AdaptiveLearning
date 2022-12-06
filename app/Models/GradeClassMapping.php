<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Constants\DbConstant as cn;
use App\Models\AttemptExams;
use Kyslik\ColumnSortable\Sortable;
use App\Models\Grades;

use App\Models\School;

class GradeClassMapping extends Model
{
    use SoftDeletes, HasFactory,Sortable;
    
    protected $table = cn::GRADE_CLASS_MAPPING_TABLE_NAME;

    public $fillable = [
        cn::GRADE_CLASS_MAPPING_ID_COL,
        cn::GRADE_CLASS_MAPPING_SCHOOL_ID_COL,
        cn::GRADE_CLASS_MAPPING_GRADE_ID_COL,
        cn::GRADE_CLASS_MAPPING_NAME_COL,
        cn::GRADE_CLASS_MAPPING_STATUS_COL,
        cn::GRADE_CLASS_MAPPING_CURRICULUM_YEAR_ID_COL
     ];
 
     public $timestamps = true;

     public static function rules($request = null, $action = '', $id = null){
      switch ($action) {
          case 'create':
              $rules = [
                  'class_type' => ['required'],
                  'name' => ['required','regex:/^[A-Za-z0-9 ]+$/u'],
              ];
              break;
          case 'update':
              $rules = [
                  'class_type' => ['required'],
                  'name' => ['required','regex:/^[A-Za-z0-9 ]+$/u'],
              ];
              break;
          default:
              break;
      }
      return $rules;
   }

    /**
    ** Additional Validation Massages for Grade Class Mapping
    **/
    public static function rulesMessages($action = ''){
        $messages = [];
        switch ($action) {
            case 'create':
                $messages = [
                   'class_type.required' => __('validation.please_select_class'),
                   'name.required'                => __('validation.please_enter_grade_name'),
                ];
                break;
            case 'update':
                $messages = [
                    'class_type.required' => __('validation.please_select_class'),
                    'name.required'                => __('validation.please_enter_grade_name') 
                 ];
                 break;
        }
        return $messages;
    }


     public function grades(){
        return $this->belongsTo(Grades::Class);
     }

     public function grade(){
      return $this->hasOne(Grades::Class, cn::GRADES_ID_COL,cn::GRADE_CLASS_MAPPING_GRADE_ID_COL);
      }

      public function school(){
         return $this->hasOne(School::Class,cn::SCHOOL_ID_COLS,cn::GRADE_CLASS_MAPPING_SCHOOL_ID_COL);
         }
}
