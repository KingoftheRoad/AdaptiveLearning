<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Constants\DbConstant As cn;
use App\Traits\Common;
use Validator;
use Log;
class ExternalResourceDashboardController extends Controller
{
    use common;
    public function __construct(){
        $this->middleware('auth');
        $this->middleware('preventBackHistory');
    }
    public function index(){
        return view('backend.school_dashboard');
    }
}
