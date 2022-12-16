<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Constants\DbConstant As cn;
use App\Traits\ResponseFormat;
use Illuminate\Support\Facades\Auth;

class StudentController extends Controller
{
    public $Auth;

    public function __construct(){
        $this->middleware('guest');
        $this->Auth = Auth::user();
    }

    public function GetStudentCreditPoints(Request $request){
        try {
            
        } catch (\Exception $ex) {
            return $this->sendError($ex);
        }
    }
}
