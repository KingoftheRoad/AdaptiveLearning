<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Grades;
use App\Models\GradeClassMapping;
use App\Constants\DbConstant as cn;
use Kyslik\ColumnSortable\Sortable;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\Common;
use DB;

class GradeSchoolMappings extends Model
{
    use SoftDeletes, HasFactory, Sortable, Common;

    protected $table = cn::GRADES_MAPPING_TABLE_NAME;

    public $fillable = [
        cn::GRADES_MAPPING_SCHOOL_ID_COL,
        cn::GRADES_MAPPING_GRADE_ID_COL,
        cn::GRADES_MAPPING_STATUS_COL,
        cn::GRADES_MAPPING_CURRICULUM_YEAR_ID_COL,
    ];

    public $timestamps = true;

    public function grades(){
        return $this->hasOne(Grades::Class, cn::GRADES_ID_COL, cn::GRADES_MAPPING_GRADE_ID_COL);
    }

    public function getClassNames($gradeid){
        $classNames = '';
        $Result = GradeClassMapping::select(DB::raw('group_concat('.cn::GRADE_CLASS_MAPPING_NAME_COL.') as ClassNames'))->where(cn::GRADE_CLASS_MAPPING_SCHOOL_ID_COL,$this->isSchoolLogin())->where(cn::GRADE_CLASS_MAPPING_CURRICULUM_YEAR_ID_COL, $this->GetCurriculumYear())->where(cn::GRADE_CLASS_MAPPING_GRADE_ID_COL,$gradeid)->first();
        if(isset($Result) && !empty($Result)){
            $classNames = $Result->ClassNames;
        }
        return $classNames;
    }
}
