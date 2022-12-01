<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Kyslik\ColumnSortable\Sortable;
use App\Constants\DbConstant as cn;
use Illuminate\Database\Eloquent\SoftDeletes;

class ClassModel extends Model
{
    use SoftDeletes, HasFactory, Sortable;

    protected $table = cn::CLASS_TABLE_NAME;
    
    public $fillable = [
        cn::CLASS_ID_COL,
        cn::CLASS_CLASS_NAME_COL,
        cn::CLASS_ACTIVE_STATUS_COL,
        cn::CLASS_SCHOOL_ID_COL,
    ];

    public $sortable = [
        cn::CLASS_ID_COL,
        cn::CLASS_CLASS_NAME_COL,
        cn::CLASS_ACTIVE_STATUS_COL,
        cn::CLASS_SCHOOL_ID_COL             
    ];

    public $timestamps = true;

    public static function rules($request = null, $action = '', $id = null){
        switch ($action) {
            case 'create':
                $rules = [
                    'class_name' => ['required'],
                ];
                break;
            case 'update':
                $rules = [
                    'class_name' => ['required'],
                ];
                break;
            default:
                break;
        }
        return $rules;
    }

    public function schools(){
        return $this->hasOne(School::Class, cn::SCHOOL_ID_COLS, cn::CLASS_ID_COL);
    }
}
