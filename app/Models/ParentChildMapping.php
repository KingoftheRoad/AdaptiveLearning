<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Constants\DbConstant as cn;

class ParentChildMapping extends Model
{
    use HasFactory;

    protected $table = cn::PARANT_CHILD_MAPPING_TABLE_NAME;
    
    public $fillable = [

        cn::PARANT_CHILD_MAPPING_PARENT_ID_COL,
        cn::PARANT_CHILD_MAPPING_STUDENT_ID_COL
    ];

}
