<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\ParentChildMapping;
use App\Models\TeachersClassSubjectAssign;
use App\Models\Subjects;
use App\Constants\DbConstant As cn;
use Illuminate\Support\Facades\Auth;

class ParentDashboardController extends Controller {
    public function __construct(){
        $this->middleware('auth');
        $this->middleware('preventBackHistory');
    }

    public function index(){
        return view('backend.parent_dashboard');
    }

    public function GetTeacherList($id){
        $user = User::where(cn::USERS_ID_COL,$id)->get()->toArray();
        $schoolId = $user[0][cn::USERS_SCHOOL_ID_COL];
        $ClassId = $user[0][cn::USERS_GRADE_ID_COL];
        $TeachersList = TeachersClassSubjectAssign::with('teachers')->where([cn::TEACHER_CLASS_SUBJECT_SCHOOL_ID_COL => $schoolId, cn::TEACHER_CLASS_SUBJECT_CLASS_ID_COL => $ClassId])->get();
        return view('backend.parent.child_teacher',compact('TeachersList'));
    }

    public function GetSubjectList($id){
        $user = User::where(cn::USERS_ID_COL,$id)->get()->toArray();
        $schoolId = $user[0][cn::USERS_SCHOOL_ID_COL];
        $studentCurrectClassId = $user[0][cn::USERS_GRADE_ID_COL];
        $subjectList = [];
        $SubjectIdsArray = TeachersClassSubjectAssign::where([
            cn::TEACHER_CLASS_SUBJECT_SCHOOL_ID_COL => $schoolId,
            cn::TEACHER_CLASS_SUBJECT_CLASS_ID_COL  => $studentCurrectClassId
            ])->get()->pluck(cn::TEACHER_CLASS_SUBJECT_SUBJECT_ID_COL);
        if(isset($SubjectIdsArray) && !empty($SubjectIdsArray)){
            $subjectid = [];
            foreach($SubjectIdsArray as $subjectIds){
                $subjectid[] = explode(',',$subjectIds);
            }
            $subjectid = call_user_func_array('array_merge', $subjectid);
            if(isset($subjectid) && !empty($subjectid)){
                $subjectList = Subjects::whereIn(cn::SUBJECTS_ID_COL,$subjectid)->get();
            }
        }
        return view('backend.parent.child_subject',compact('subjectList'));
    }

    public function ChildList(){
        try{
            $ParentChildMapping = ParentChildMapping::where(cn::PARANT_CHILD_MAPPING_PARENT_ID_COL, Auth::user()->{cn::USERS_ID_COL})->get(cn::PARANT_CHILD_MAPPING_STUDENT_ID_COL)->toArray();
            $List = User::whereIn(cn::USERS_ID_COL,$ParentChildMapping)->get();
            return view('backend.parent.child_list',compact('List')); 
        } catch (\Exception $exception) {
            return redirect('users')->withError($exception->getMessage())->withInput();
        }
    }
}
