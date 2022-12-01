<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Constants\DbConstant as cn;
use Kyslik\ColumnSortable\Sortable;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\CurriculumYear;

class CurriculumYearStudentMappings extends Model
{
    use SoftDeletes, HasFactory, Sortable;

    protected $table = cn::CURRICULUM_YEAR_STUDENT_MAPPING_TABLE;

    public $fillable = [
        cn::CURRICULUM_YEAR_STUDENT_MAPPING_USER_ID_COL,
        cn::CURRICULUM_YEAR_STUDENT_MAPPING_CURRICULUM_YEAR_ID_COL,
        cn::CURRICULUM_YEAR_STUDENT_MAPPING_SCHOOL_ID_COL,
        cn::CURRICULUM_YEAR_STUDENT_MAPPING_GRADE_ID_COL,
        cn::CURRICULUM_YEAR_STUDENT_MAPPING_CLASS_ID_COL,
        cn::CURRICULUM_YEAR_STUDENT_MAPPING_STATUS_COL,
        cn::CURRICULUM_YEAR_STUDENT_NUMBER_WITHIN_CLASS_COL,
        cn::CURRICULUM_YEAR_STUDENT_CLASS,
        cn::CURRICULUM_YEAR_CLASS_STUDENT_NUMBER
    ];

    public $timestamps = true;

    public function CurriculumYear(){
        return $this->hasOne(CurriculumYear::class,cn::CURRICULUM_YEAR_ID_COL,cn::CURRICULUM_YEAR_STUDENT_MAPPING_CURRICULUM_YEAR_ID_COL);
    }
}
