<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Kyslik\ColumnSortable\Sortable;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Constants\DbConstant as cn;
use Illuminate\Validation\Rule;
use App\Models\PeerGroupMember;
use App\Models\Subjects;
use App\Models\ExamGradeClassMappingModel;
class PeerGroup extends Model
{
    use SoftDeletes, HasFactory, Sortable;
    protected $table = cn::PEER_GROUP_TABLE_NAME;

    protected $fillable = [
        cn::PEER_GROUP_SCHOOL_ID_COL,
        cn::PEER_GROUP_DREAMSCHAT_GROUP_ID,
        cn::PEER_GROUP_GROUP_NAME_COL,
        cn::PEER_GROUP_GROUP_PREFIX_COL,
        cn::PEER_GROUP_CREATED_BY_USER_ID_COL,
        cn::PEER_GROUP_SUBJECT_ID_COL,
        cn::PEER_GROUP_STATUS_COL,
        cn::PEER_GROUP_CREATED_TYPE_COL,
        cn::PEER_GROUP_AUTO_GROUP_BY_COL
    ];

    public $sortable = [
       cn::PEER_GROUP_ID_COL 
    ];

    protected $appends = ['PeerGroupName'];

    /**
     * USE : Get language based group name
     */
    public function getPeerGroupNameAttribute(){
        $PeerGroupName = null;
        // if(app()->getLocale() == 'en'){
            $PeerGroupName = $this->{cn::PEER_GROUP_GROUP_NAME_COL};
        // }
        return $PeerGroupName;
    }

    /**
     *  Relationship Starts 
     * */
    
    // Get Peer Group Members list Relationship
    public function Members(){
        return $this->hasMany(PeerGroupMember::class,cn::PEER_GROUP_MEMBERS_PEER_GROUP_ID_COL,cn::PEER_GROUP_ID_COL);
    }

    public function subject(){
        return $this->hasOne(Subjects::class,cn::SUBJECTS_ID_COL,cn::PEER_GROUP_SUBJECT_ID_COL);
    }

    public function ExamGradeClassMapping(){
        return $this->hasMany(ExamGradeClassMappingModel::class,'peer_group_id','id');
    }

    /**
     *  Relationship Ends 
     * */

    /**
     * USE : Validation rules
     */
    public static function rules($request = null, $action = '', $id = null){
        switch ($action) {
            case 'create':
                $rules = [
                    cn::PEER_GROUP_GROUP_NAME_COL => ['required']
                ];
                break;
            case 'update':
                $rules = [
                    cn::PEER_GROUP_GROUP_NAME_COL => ['required']
                ];
                break;
            default:
                break;
        }
        return $rules;
    }

    /**
    ** Additional Validation Massages for users
    **/
    public static function rulesMessages($action = ''){
        $messages = [];
        switch ($action) {
            case 'create':
                $messages = [
                    cn::PEER_GROUP_GROUP_NAME_COL.'.required' =>'Peer group name is required'
                ];
                break;
            case 'update':
                $messages = [
                    cn::PEER_GROUP_GROUP_NAME_COL.'.required' =>'Peer group name is required'
                ];
                break;
        }
        return $messages;
    }
}
