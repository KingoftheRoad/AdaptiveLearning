<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Constants\DbConstant as cn;
use App\Models\User; 

class UserActivities extends Model
{
    use HasFactory;
    protected $table = cn::LOGIN_ACTIVITIES_TABLE_NAME;
    
    public $fillable = [
        cn::LOGIN_ACTIVITIES_TYPE_COL,
        cn::LOGIN_ACTIVITIES_USER_ID_COL,
        cn::LOGIN_ACTIVITIES_USER_AGENT_ID_COL
    ];

    public $timestamps = true;

    public function users(){
        return $this->hasOne(User::class,cn::USERS_ID_COL,cn::LOGIN_ACTIVITIES_USER_ID_COL);
    }
}
