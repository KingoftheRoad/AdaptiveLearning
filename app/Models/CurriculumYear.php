<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Constants\DbConstant as cn;
use Illuminate\Database\Eloquent\SoftDeletes;

class CurriculumYear extends Model
{
    use SoftDeletes, HasFactory;

    protected $table = cn::CURRICULUM_YEAR_TABLE_NAME;

    public $fillable = [
        cn::CURRICULUM_YEAR_YEAR_COL,
        cn::CURRICULUM_YEAR_STATUS_COL
    ];

    public $timestamps = true;

    protected $dates = [cn::DELETED_AT_COL];

    public function scopeIsActiveYear($query){
        return $query->where(cn::CURRICULUM_YEAR_STATUS_COL,'active');
    }
}