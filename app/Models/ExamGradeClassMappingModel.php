<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Constants\DbConstant as cn;
use App\Models\PeerGroup;
use App\Models\GradeClassMapping;

class ExamGradeClassMappingModel extends Model{
    use SoftDeletes, HasFactory;

    protected $table = cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_TABLE;

    public $fillable = [
        cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_ID_COL,
        cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_SCHOOL_ID_COL,
        cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_GRADE_ID_COL,
        cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_CLASS_ID_COL,
        cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_PEER_GROUP_ID_COL,
        cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_EXAM_ID_COL,
        cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_STUDENT_IDS_COL,
        cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_START_TIME_COL,
        cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_END_TIME_COL,
        cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_START_DATE_COL,
        cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_END_DATE_COL,
        cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_STATUS_COL,
        cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_CURRICULUM_YEAR_ID_COL
     ];
 
      public $timestamps = true;

      public function PeerGroup(){
         return $this->hasOne(PeerGroup::class,cn::PEER_GROUP_ID_COL,cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_PEER_GROUP_ID_COL);
      }
      public function grade(){
         return $this->hasOne(Grades::Class, cn::GRADES_ID_COL,cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_GRADE_ID_COL);
      }
      public function grade_class_mapping(){
         return $this->hasOne(GradeClassMapping::class,cn::GRADE_CLASS_MAPPING_ID_COL,cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_CLASS_ID_COL);
      }
}
