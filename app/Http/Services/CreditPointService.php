<?php

namespace App\Http\Services;

use App\Traits\Common;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use App\Models\UserCreditPoints;
use App\Constants\DbConstant as cn;

class CreditPointService 
{
    use Common;

    public function __construct(){
        
    }

    /**
     * USE : Get student credit points
     * Params : $StudentId
     */
    public function GetStudentCreditPoints($StudentId = null,$request = null){
        if(isset($StudentId) && !empty($StudentId)){
            return UserCreditPoints::where(cn::USER_CREDIT_USER_ID_COL,$StudentId)->sum(cn::USER_NO_OF_CREDIT_POINTS_COL);
        }
    }
}