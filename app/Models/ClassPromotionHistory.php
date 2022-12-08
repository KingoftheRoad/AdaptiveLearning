<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Constants\DbConstant as cn;

class ClassPromotionHistory extends Model
{
    use SoftDeletes, HasFactory;
    protected $table = cn::CLASS_PROMOTION_HISTORY_TABLE_NAME;

    public $fillable = [
        cn::CLASS_PROMOTION_HISTORY_SCHOOL_ID_COL,
        cn::CLASS_PROMOTION_HISTORY_STUDENT_ID_COL,
        cn::CLASS_PROMOTION_HISTORY_CURRENT_GRADE_ID_COL,
        cn::CLASS_PROMOTION_HISTORY_CURRENT_CLASS_ID_COL,
        cn::CLASS_PROMOTION_HISTORY_PROMOTED_GRADE_ID_COL,
        cn::CLASS_PROMOTION_HISTORY_PROMOTED_CLASS_ID_COL,
        cn::CLASS_PROMOTION_HISTORY_PROMOTED_BY_USER_ID_COL,
        cn::CLASS_PROMOTION_HISTORY_CURRICULUM_YEAR_ID_COL
    ];

    public $timestamps = true;
}
