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
     ];
 
     public $timestamps = true;

     public function grades(){
        return $this->belongsTo(Grades::Class);
     }

     public function grade(){
      return $this->hasOne(Grades::Class, cn::GRADES_ID_COL, 'grade_id');
      }

      public function school(){
         return $this->hasOne(School::Class, 'id', 'school_id');
         }
}
