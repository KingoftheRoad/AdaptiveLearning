<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Kyslik\ColumnSortable\Sortable;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Constants\DbConstant as cn;
use App\Models\User;

class PeerGroupMember extends Model
{
    use SoftDeletes, HasFactory, Sortable;
    protected $table = cn::PEER_GROUP_MEMBERS_TABLE;

    protected $fillable = [
        cn::PEER_GROUP_MEMBERS_PEER_GROUP_ID_COL,
        cn::PEER_GROUP_MEMBERS_MEMBER_ID_COL,
        cn::PEER_GROUP_MEMBERS_STATUS_COL,
        cn::PEER_GROUP_MEMBERS_CURRICULUM_YEAR_ID_COL
    ];

    function Student(){
        return $this->hasOne(User::class,cn::USERS_ID_COL,cn::PEER_GROUP_MEMBERS_MEMBER_ID_COL);
    }
    
}
