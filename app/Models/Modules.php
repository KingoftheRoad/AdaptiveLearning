<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Kyslik\ColumnSortable\Sortable;
use App\Constants\DbConstant as cn;

class Modules extends Model
{
    use HasFactory,SoftDeletes,Sortable;

    protected $table = cn::MODULES_TABLE_NAME;

    protected $fillable = [
        cn::MODULES_MODULE_NAME_COL,
        cn::MODULES_MODULE_SLUG_COL,
        cn::MODULES_STATUS_COL,
    ];

    public $sortable = [
        cn::MODULES_MODULE_NAME_COL,
        cn::MODULES_MODULE_SLUG_COL,
        cn::MODULES_STATUS_COL
    ];

        /**
    ** Validation Rules for module
    **/
    public static function rules($request = null, $action = '', $id = null){
        switch ($action) {
            case 'create':
                $rules = [
                    cn::MODULES_MODULE_NAME_COL => ['required']
                ];
                break;
            case 'update':
                $rules = [
                    cn::MODULES_MODULE_NAME_COL => ['required']
                ];
                break;
            default:
                break;
        }
        return $rules;
    }

     /**
    ** Additional Validation Massages for module
    **/
    public static function rulesMessages($action = ''){
        $messages = [];
        switch ($action) {
            case 'create':
                $messages = [
                    cn::MODULES_MODULE_NAME_COL.'.required' => __('validation.please_enter_module_name'),
                ];
                break;
            case 'update':
                $messages = [
                    cn::MODULES_MODULE_NAME_COL.'.required' => __('validation.please_enter_module_name'),
                ];
                break;
        }
        return $messages;
    }
}
