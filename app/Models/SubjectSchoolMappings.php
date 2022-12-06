<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Constants\DbConstant as cn;
use Kyslik\ColumnSortable\Sortable;
use App\Models\Subjects;
use Illuminate\Database\Eloquent\SoftDeletes;

class SubjectSchoolMappings extends Model
{
    use SoftDeletes, HasFactory, Sortable;

    protected $table = cn::SUBJECT_MAPPING_TABLE_NAME;

    public $fillable = [
        cn::SUBJECT_MAPPING_SCHOOL_ID_COL,
        cn::SUBJECT_MAPPING_SUBJECT_ID_COL,
        cn::SUBJECT_MAPPING_STATUS_COL,
        cn::SUBJECT_MAPPING_CURRICULUM_YEAR_ID_COL
    ];

    public $timestamps = true;

    public function subjects(){
        return $this->hasOne(Subjects::Class, cn::SUBJECTS_ID_COL, cn::SUBJECT_MAPPING_SUBJECT_ID_COL);
    }

    public function getClassNameById(){
        $subkey = 'subject_id';
        if($this->$subkey != ""){
            $data = ClassSubjectMapping::where(cn::CLASS_SUBJECT_MAPPING_SCHOOL_ID_COL,Auth()->user()->school_id)->where(cn::CLASS_SUBJECT_MAPPING_SUBJECT_ID_COL,$this->$subkey)->select(cn::CLASS_SUBJECT_MAPPING_CLASS_ID_COL.' as name')->get()->toArray();
            if(isset($data) && !empty($data)){
                $data = array_column($data,'name');
                $dataTitle = Grades::whereIn(cn::GRADES_ID_COL,$data)->select(\DB::raw('GROUP_CONCAT('.cn::GRADES_NAME_COL.') as names'))->get()->toArray();
                if(isset($dataTitle) && !empty($dataTitle)){
                    return $dataTitle[0]['names'];
                }
            }
        }
        return '';
    }
}
