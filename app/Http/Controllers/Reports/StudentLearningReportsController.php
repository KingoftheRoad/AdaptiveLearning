<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Constants\DbConstant As cn;
use App\Traits\ResponseFormat;
use App\Traits\Common;
use App\Helpers\Helper;
use App\Models\User;
use App\Models\Strands;
use Illuminate\Support\Facades\Auth;

class StudentLearningReportsController extends Controller
{
    use Common, ResponseFormat;

    public function __construct(){
        
    }

    /**
    * USE : Display student learning progress reports (My Study Panel for Student)
    **/
    public function StudentLearningReport(Request $request){
        $strandData = Strands::all();
        return view('backend.student-learning-report',compact('strandData'));
    }
}