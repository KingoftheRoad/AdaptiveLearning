<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Constants\DbConstant as cn;
use Kyslik\ColumnSortable\Sortable;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\LearningsUnits;

class Strands extends Model
{
    use HasFactory, SoftDeletes,Sortable;

    protected $table = cn::STRANDS_TABLE_NAME;
    
    public $fillable = [
        cn::STRANDS_NAME_COL,
        cn::STRANDS_CODE_COL,
        cn::STRANDS_NAME_EN_COL,
        cn::STRANDS_NAME_CH_COL,
        cn::STRANDS_STATUS_COL
    ];

     // Enable sortable columns name
     public $sortable = [
        cn::STRANDS_NAME_COL,
        cn::STRANDS_CODE_COL,
        cn::STRANDS_NAME_EN_COL,
        cn::STRANDS_NAME_CH_COL,
        cn::STRANDS_STATUS_COL
    ];                

    public $timestamps = true;

     /**
    ** Validation Rules for strands
    **/
    public static function rules($request = null, $action = '', $id = null){
        switch ($action) {
            case 'create':
                $rules = [
                    cn::STRANDS_NAME_EN_COL => ['required'],
                    cn::STRANDS_NAME_CH_COL => ['required'],
                    cn::STRANDS_CODE_COL => ['required'],
                ];
                break;
            case 'update':
                $rules = [
                    cn::STRANDS_NAME_EN_COL => ['required'],
                    cn::STRANDS_NAME_CH_COL => ['required'],
                    cn::STRANDS_CODE_COL => ['required'],
                ];
                break;
            default:
                break;
        }
        return $rules;
    }

    /**
    ** Additional Validation Massages for strand
    **/
    public static function rulesMessages($action = ''){
        $messages = [];
        switch ($action) {
            case 'create':
                $messages = [
                    cn::STRANDS_NAME_EN_COL.'.required' => __('validation.please_enter_english_name'),
                    cn::STRANDS_NAME_CH_COL.'.required' => __('validation.please_enter_chinese_name'),
                    cn::STRANDS_CODE_COL.'.required' => __('validation.please_enter_code'),
                ]; 
                break;
            case 'update':
                $messages = [
                    cn::STRANDS_NAME_EN_COL.'.required' => __('validation.please_enter_english_name'),
                    cn::STRANDS_NAME_CH_COL.'.required' => __('validation.please_enter_chinese_name'),
                    cn::STRANDS_CODE_COL.'.required' => __('validation.please_enter_code'),
                ]; 
                break;
        }
        return $messages;
    }

    function LearningUnit(){
        return $this->hasMany(LearningsUnits::class, cn::LEARNING_UNITS_STRANDID_COL, cn::STRANDS_ID_COL);
    }
}
