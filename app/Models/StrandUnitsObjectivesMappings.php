<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Constants\DbConstant as cn;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Subjects;

class StrandUnitsObjectivesMappings extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = cn::OBJECTIVES_MAPPINGS_TABLE_NAME;
    
    public $fillable = [
        cn::OBJECTIVES_MAPPINGS_GRADE_ID_COL,
        cn::OBJECTIVES_MAPPINGS_SUBJECT_ID_COL,
        cn::OBJECTIVES_MAPPINGS_STRAND_ID_COL,
        cn::OBJECTIVES_MAPPINGS_LEARNING_UNIT_ID_COL,
        cn::OBJECTIVES_MAPPINGS_LEARNING_OBJECTIVES_ID_COL
    ];

    public $timestamps = false;

}
