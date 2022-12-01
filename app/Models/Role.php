<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Kyslik\ColumnSortable\Sortable;
use App\Constants\DbConstant as cn;


class Role extends Model
{
    use HasFactory,Sortable;

    protected $table = cn::ROLES_TABLE_NAME;

    protected $fillable = [
        cn::ROLES_ROLE_NAME_COL,
        cn::ROLES_ROLE_SLUG_COL,
        cn::ROLES_PERMISSION_COL,
        cn::ROLES_STATUS_COL,
    ];

    public $sortable = [
                            cn::ROLES_ROLE_NAME_COL,
                            cn::ROLES_ROLE_SLUG_COL,
                            cn::ROLES_PERMISSION_COL,
                            cn::ROLES_STATUS_COL,
                        ];

    /**
    ** Validation Rules for role
    **/
    public static function rules($request = null, $action = '', $id = null){
        switch ($action) {
            case 'create':
                $rules = [
                    cn::ROLES_ROLE_NAME_COL => ['required']
                ];
                break;
            case 'update':
                $rules = [
                    cn::ROLES_ROLE_NAME_COL => ['required']
                ];
                break;
            default:
                break;
        }
        return $rules;
    }

     /**
    ** Additional Validation Massages for Role
    **/
    public static function rulesMessages($action = ''){
        $messages = [];
        switch ($action) {
            case 'create':
                $messages = [
                    cn::ROLES_ROLE_NAME_COL.'.required' => __('validation.please_enter_role_name'),
                ];
                break;
            case 'update':
                $messages = [
                    cn::ROLES_ROLE_NAME_COL.'.required' => __('validation.please_enter_role_name'),
                ];
                break;
        }
        return $messages;
    }

    public function users(){
        return $this->hasMany('App\Models\User');
    }
    
}
