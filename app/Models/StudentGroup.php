<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Kyslik\ColumnSortable\Sortable;
use App\Constants\DbConstant as cn;


class StudentGroup extends Model
{
    use HasFactory, SoftDeletes,Sortable;

    protected $table = cn::STUDENT_GROUP_TABLE_NAME;
    
    public $fillable = [
        cn::STUDENT_GROUP_NAME_COL,
        cn::STUDENT_GROUP_GRADE_ID_COL,
        cn::STUDENT_GROUP_STUDENT_ID_COL,
        cn::STUDENT_GROUP_EXAM_GROP_IDS_COL,
        cn::STUDENT_GROUP_STATUS_COL,
        cn::STUDENT_GROUP_SCHOOL_ID_COL
    ];

    // Enable sortable columns name
    public $sortable = [cn::STUDENT_GROUP_ID_COL,cn::STUDENT_GROUP_NAME_COL];
    public $timestamps = true;
}
