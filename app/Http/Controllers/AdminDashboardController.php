<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;

class AdminDashboardController extends Controller
{
    public function __construct(){
        $this->middleware('auth');
        $this->middleware('preventBackHistory');
    }

    public function index(){
        return view('backend.admin_dashboard');
    }
}
