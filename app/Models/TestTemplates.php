<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Constants\DbConstant as cn;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Validation\Rule;

class TestTemplates extends Model
{
    use HasFactory, SoftDeletes;
    
    protected $table = cn::TEST_TEMPLATE_TABLE_NAME;
    
    public $timestamps = true;
    
    protected $fillable = [
        cn::TEST_TEMPLATE_NAME_COL,
        cn::TEST_TEMPLATE_TYPE,
        cn::TEST_TEMPLATE_DIFFICULTY_LEVEL_COL,
        cn::TEST_TEMPLATE_QUESTION_IDS_COL,
        cn::TEST_TEMPLATE_CREATED_BY,
        cn::TEST_TEMPLATE_STATUS
    ];

    public static function rules($request = null, $action = '', $id = null){
        switch ($action) {
            case 'create':
                $rules = [
                    'name' => ['required','unique:test_templates,name,NULL,id,deleted_at,NULL'],
                    'difficulty_level' => ['required'],
                    'template_type' => ['required'],
                    'question_ids' => ['required']
                ];
                break;
            case 'update':
                $rules = [
                    'name' => 'required|unique:test_templates,name,'.$id.',id,deleted_at,NULL',
                    'difficulty_level' => ['required'],
                    'template_type' => ['required'],
                    'question_ids' => ['required']
                ];
                break;
            default:
                break;
        }
        return $rules;
    }
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