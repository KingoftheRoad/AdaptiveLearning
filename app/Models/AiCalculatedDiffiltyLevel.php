<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Constants\DbConstant As cn;
use Kyslik\ColumnSortable\Sortable;
use Illuminate\Database\Eloquent\SoftDeletes;

class AiCalculatedDiffiltyLevel extends Model
{
    use  SoftDeletes,HasFactory, Sortable;
    protected $table = cn::AI_CALCULATED_DIFFICULTY_TABLE_NAME;

    public $fillable = [
        cn::AI_CALCULATED_DIFFICULTY_ID_COL,
        cn::AI_CALCULATED_DIFFICULTY_DIFFICULTY_LEVEL_COL,
        cn::AI_CALCULATED_DIFFICULTY_TITLE_COL,
        cn::AI_CALCULATED_DIFFICULTY_STATUS_COL,
    ];

    public $timestamps = true;

    // Enable sortable columns name
    public $sortable = [cn::AI_CALCULATED_DIFFICULTY_DIFFICULTY_LEVEL_COL,cn::AI_CALCULATED_DIFFICULTY_TITLE_COL,cn::AI_CALCULATED_DIFFICULTY_STATUS_COL];                

    /**
    ** Validation Rules for school
    **/
    public static function rules($request = null, $action = '', $id = null){
        switch ($action) {
            case 'create':
                $rules = [
                    'difficultyLevel' => ['required'],
                    'difficult_value' => ['required'],
                ];
                break;
            case 'update':
                $rules = [
                    'difficultyLevel' => ['required'],
                    'difficult_value' => ['required'],
                ];
                break;
            default:
                break;
        }
        return $rules;
    }

    /**
    ** Additional Validation Massages for School
    **/
    public static function rulesMessages($action = ''){
        $messages = [];
        switch ($action) {
            case 'create':
                break;
            case 'update':
                break;
        }
        return $messages;
    }
}
