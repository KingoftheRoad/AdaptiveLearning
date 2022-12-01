<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Constants\DbConstant as cn;
use Kyslik\ColumnSortable\Sortable;
use Illuminate\Database\Eloquent\SoftDeletes;

class Grades extends Model
{
    use SoftDeletes, HasFactory, Sortable;

    protected $table = cn::GRADES_TABLE_NAME;
    
    public $fillable = [
        cn::GRADES_NAME_COL,
        cn::GRADES_STATUS_COL,
        cn::GRADES_CODE_COL,
        cn::GRADES_SCHOOL_ID_COL
    ];

    public $sortable = [
        cn::GRADES_NAME_COL,
        cn::GRADES_STATUS_COL,
        cn::GRADES_CODE_COL,
        cn::GRADES_SCHOOL_ID_COL
    ];

    public $timestamps = true;

    public static function rules($request = null, $action = '', $id = null){
        switch ($action) {
            case 'create':
                $rules = [
                    'name' => ['required','regex:/^[A-Za-z0-9 ]+$/u']
                ];
                break;
            case 'update':
                $rules = [
                    'name' => ['required','regex:/^[A-Za-z0-9 ]+$/u']
                ];
                break;
            default:
                break;
        }
        return $rules;
    }

    public function classes(){
        return $this->hasMany(GradeClassMapping::Class,cn::GRADE_CLASS_MAPPING_GRADE_ID_COL,cn::GRADES_ID_COL);
    }
}
