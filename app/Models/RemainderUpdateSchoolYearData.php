<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Constants\DbConstant as cn;
use App\Models\School;

class RemainderUpdateSchoolYearData extends Model
{
    use HasFactory;

    protected $table = cn::REMAINDER_UPDATE_SCHOOL_YEAR_DATA_TABLE;

    protected $fillable = [
        cn::REMAINDER_UPDATE_SCHOOL_YEAR_DATA_CURRICULUM_YEAR_ID_COL,
        cn::REMAINDER_UPDATE_SCHOOL_YEAR_DATA_SCHOOL_ID_COL,
        cn::REMAINDER_UPDATE_SCHOOL_YEAR_DATA_IMPORTED_DATE_COL,
        cn::REMAINDER_UPDATE_SCHOOL_YEAR_DATA_UPLOADED_BY_COL,
        cn::REMAINDER_UPDATE_SCHOOL_YEAR_DATA_STATUS_COL,
    ];

    // Get the school data by school id based on foreign-key
    public function school(){
        return $this->hasOne(School::Class, cn::SCHOOL_ID_COLS, cn::SUBJECTS_ID_COL);
    }
}
