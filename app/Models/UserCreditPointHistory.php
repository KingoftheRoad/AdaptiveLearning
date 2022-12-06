<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Kyslik\ColumnSortable\Sortable;
use App\Constants\DbConstant as cn;

class UserCreditPointHistory extends Model{
    use SoftDeletes, HasFactory, Sortable;
    
    protected $table = cn::USER_CREDIT_POINT_HISTORY;

    public $fillable = [
        cn::USER_CREDIT_POINT_HISTORY_ID_COL,
        cn::USER_CREDIT_POINT_HISTORY_EXAM_ID_COL,
        cn::USER_CREDIT_POINT_HISTORY_USER_ID_COL,
        cn::USER_CREDIT_POINT_HISTORY_TEST_TYPE_COL,
        cn::USER_CREDIT_POINT_HISTORY_SELF_LEARNING_TYPE_COL,
        cn::USER_CREDIT_POINT_HISTORY_CREDIT_POINT_TYPE_COL,
        cn::USER_CREDIT_POINT_HISTORY_NO_OF_CREDIT_POINT_COL,
        cn::USER_CREDIT_POINT_HISTORY_CREDIT_POINT_HISTORY_COL,
        cn::USER_CREDIT_POINT_HISTORY_CURRICULUM_YEAR_ID_COL
    ];

    public $sortable = [
        cn::USER_CREDIT_POINT_HISTORY_ID_COL,
        cn::USER_CREDIT_POINT_HISTORY_EXAM_ID_COL,
        cn::USER_CREDIT_POINT_HISTORY_USER_ID_COL,
        cn::USER_CREDIT_POINT_HISTORY_TEST_TYPE_COL,
        cn::USER_CREDIT_POINT_HISTORY_SELF_LEARNING_TYPE_COL,
        cn::USER_CREDIT_POINT_HISTORY_CREDIT_POINT_TYPE_COL,
        cn::USER_CREDIT_POINT_HISTORY_NO_OF_CREDIT_POINT_COL,
        cn::USER_CREDIT_POINT_HISTORY_CREDIT_POINT_HISTORY_COL,
        cn::USER_CREDIT_POINT_HISTORY_CURRICULUM_YEAR_ID_COL
    ];

    public $timestamps = true;

    public function getExam(){
        return $this->hasOne(Exam::class,cn::EXAM_TABLE_ID_COLS,cn::USER_CREDIT_POINT_HISTORY_EXAM_ID_COL);
    }
}

