<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\User;
use App\Constants\DbConstant as cn;

class UserCreditPoints extends Model{
    use SoftDeletes, HasFactory;
    
    protected $table = cn::USER_CREDIT_POINTS_TABLE;

    public $fillable = [
        cn::USER_CREDIT_POINTS_ID_COL,
        cn::USER_CREDIT_USER_ID_COL,
        cn::USER_NO_OF_CREDIT_POINTS_COL,
    ];

    public $timestamps = true;

    public function user(){
        return $this->hasOne(User::class,cn::USERS_ID_COL,cn::USER_CREDIT_USER_ID_COL);
    }
}
