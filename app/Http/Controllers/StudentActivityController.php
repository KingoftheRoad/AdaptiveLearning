<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Constants\DbConstant As cn;
use App\Traits\Common;
use App\Traits\ResponseFormat;
use App\Models\Exam;
use Exception;
use Illuminate\Support\Facades\Auth;
class StudentActivityController extends Controller
{
    use Common, ResponseFormat;

    public function index(Request $request){
        try{
            $learningTypes = array(
                ['id' => 1,"name" => 'Self Study'],
                ['id' => 2,"name" => 'Assignment/Exercise'],
                ['id' => 3,"name" => 'Test']
            );
            switch($request->learning_type){
                case 3: 
                    $userId = Auth::id();
                    $examList = Exam::with('attempt_exams')->whereRaw("find_in_set($userId,student_ids)")->where(cn::EXAM_TABLE_IS_GROUP_TEST_COL,0)->sortable()->orderBy(cn::EXAM_TABLE_ID_COLS,'DESC')->get();
                    return view('backend/student/mylearning_list',compact('learningTypes','examList'));
                    break;
                default:
                    return view('backend/student/mylearning_list',compact('learningTypes'));
            }
        }catch(Exception $exception){
            return redirect('my-desk')->withError($exception->getMessage());  
        }
    }
}
