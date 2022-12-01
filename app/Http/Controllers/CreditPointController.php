<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Traits\ResponseFormat;
use App\Traits\Common;
use App\Models\Grades;
use App\Models\User;
use App\Models\PeerGroup;
use App\Models\UserCreditPointHistory;
use App\Models\UserCreditPoints;
use Illuminate\Support\Facades\Auth;
use App\Constants\DbConstant As cn;
use App\Http\Services\TeacherGradesClassService;

class CreditPointController extends Controller
{
    use Common, ResponseFormat;
    protected $TeacherGradesClassService;

    public function __construct(){
        $this->middleware('auth');
        $this->middleware('preventBackHistory');
        $this->TeacherGradesClassService = new TeacherGradesClassService;
    }
    
    /**
     * USE : Assign Credit points to students
     */
    public function AssignCreditPoints(Request $request){
        if($this->isTeacherLogin()){
            $schoolId = $this->isTeacherLogin();
            // Get Teachers Grades
            $TeacherGradeClass = $this->TeacherGradesClassService->getTeacherAssignedGradesClass(Auth::user()->{cn::USERS_SCHOOL_ID_COL}, Auth::user()->{cn::USERS_ID_COL});
            $GradeClassData = Grades::with(['classes' => fn($query) => $query->whereIn(cn::GRADE_CLASS_MAPPING_ID_COL,$TeacherGradeClass['class'])
                                    ->where([cn::GRADE_CLASS_MAPPING_SCHOOL_ID_COL => $this->isTeacherLogin()])])
                                    ->whereIn(cn::GRADES_ID_COL,$TeacherGradeClass['grades'])
                                    ->get();

            // Get Peer Group List
            $PeerGroupList = PeerGroup::where([cn::PEER_GROUP_CREATED_BY_USER_ID_COL => Auth()->user()->{cn::USERS_ID_COL}, cn::PEER_GROUP_STATUS_COL => '1'])->get();

            // get student list
            $StudentList = User::whereIn(cn::USERS_GRADE_ID_COL,$TeacherGradeClass['grades'])
                            ->whereIn(cn::USERS_CLASS_ID_COL,$TeacherGradeClass['class'])
                            ->where([cn::USERS_ROLE_ID_COL => cn::STUDENT_ROLE_ID,cn::USERS_STATUS_COL => 'active'])
                            ->get();
        }

        if($this->isSchoolLogin() || $this->isPrincipalLogin()){    
            if($this->isSchoolLogin()){
                $schoolId = $this->isSchoolLogin();
            }
            if($this->isPrincipalLogin()){
                $schoolId = $this->isPrincipalLogin();
            }

            $GradeMapping = GradeSchoolMappings::with('grades')->where(cn::GRADES_MAPPING_SCHOOL_ID_COL,$schoolId)->get()->pluck(cn::GRADES_MAPPING_GRADE_ID_COL);
            $gradeClass = GradeClassMapping::where(cn::GRADE_CLASS_MAPPING_SCHOOL_ID_COL,$schoolId)->whereIn(cn::GRADE_CLASS_MAPPING_GRADE_ID_COL,$GradeMapping)->pluck(cn::GRADE_CLASS_MAPPING_ID_COL)->toArray();
            if(isset($gradeClass) && !empty($gradeClass)){
                $gradeClass = implode(',', $gradeClass);
                $gradeClassId = explode(',',$gradeClass);
            }
            $GradeClassData =   Grades::with(['classes' => fn($query) => 
                                    $query->whereIn(cn::GRADE_CLASS_MAPPING_ID_COL,$gradeClassId)
                                    ->where([cn::GRADE_CLASS_MAPPING_SCHOOL_ID_COL => $schoolId])
                                ])
                                ->whereIn(cn::GRADES_ID_COL,$GradeMapping)->get();
            
            // get student list
            $StudentList = User::where(cn::USERS_SCHOOL_ID_COL,Auth::user()->{cn::USERS_SCHOOL_ID_COL})
                            ->where(cn::USERS_ROLE_ID_COL,'=',cn::STUDENT_ROLE_ID)
                            ->with('grades')->get();

            // Get Peer Group List
            $PeerGroupList = PeerGroup::where([cn::PEER_GROUP_SCHOOL_ID_COL => $schoolId, cn::PEER_GROUP_STATUS_COL => '1'])->get();
        }

        if($request->isMethod('post')){
            if(isset($request->student_ids) && !empty($request->student_ids) && isset($request->number_of_credit_point) && !empty($request->number_of_credit_point)){
                $userCreditPointHistoryDataArray=array();
                foreach ($request->student_ids as $key => $studentIds) {
                    foreach ($studentIds as $studentId => $studentIdValue) {
                        if(!empty($userCreditPointHistoryDataArray)){
                            $dataCheck = 0;
                            foreach($userCreditPointHistoryDataArray as $key => $studentData){
                                if($studentData['user_id'] == $studentId){
                                    $number_of_credit_point = $request->number_of_credit_point;
                                    $userCreditPointHistoryDataArray[$key]=[
                                        cn::USER_CREDIT_POINT_HISTORY_USER_ID_COL => $studentId,
                                        cn::USER_CREDIT_POINT_HISTORY_NO_OF_CREDIT_POINT_COL => $number_of_credit_point
                                    ];
                                    $dataCheck++;
                                }
                            }
                            if($dataCheck == 0){
                                $userCreditPointHistoryDataArray[] = array(
                                                                        cn::USER_CREDIT_POINT_HISTORY_USER_ID_COL => $studentId,
                                                                        cn::USER_CREDIT_POINT_HISTORY_NO_OF_CREDIT_POINT_COL => $request->number_of_credit_point
                                                                    );
                            }
                        }else{
                            $userCreditPointHistoryDataArray[] = [
                                                                    cn::USER_CREDIT_POINT_HISTORY_USER_ID_COL => $studentId,
                                                                    cn::USER_CREDIT_POINT_HISTORY_NO_OF_CREDIT_POINT_COL => $request->number_of_credit_point
                                                                ];
                                                            }
                        }
                    }
                    
                    if(isset($userCreditPointHistoryDataArray) && !empty($userCreditPointHistoryDataArray)){
                    foreach ($userCreditPointHistoryDataArray as $studentsData) {
                    UserCreditPointHistory::Create([
                                                        cn::USER_CREDIT_POINT_HISTORY_EXAM_ID_COL => Null,
                                                        cn::USER_CREDIT_POINT_HISTORY_USER_ID_COL => $studentsData['user_id'],
                                                        cn::USER_CREDIT_POINT_HISTORY_TEST_TYPE_COL => '',
                                                        cn::USER_CREDIT_POINT_HISTORY_SELF_LEARNING_TYPE_COL => '',
                                                        cn::USER_CREDIT_POINT_HISTORY_CREDIT_POINT_TYPE_COL => 'manual_credit_point',
                                                        cn::USER_CREDIT_POINT_HISTORY_NO_OF_CREDIT_POINT_COL => $studentsData['no_of_credit_point']
                                                    ]);
                        $GetStudentTotalCreditPoints = $studentsData['no_of_credit_point'];
                        $studentId = $studentsData['user_id'];
                        $studentData = UserCreditPoints::where(cn::USER_CREDIT_USER_ID_COL,$studentId)->first();
                        if(isset($studentData) && !empty($studentData)){
                            $GetStudentTotalCreditPoints = ($studentData->no_of_credit_points + $request->number_of_credit_point);
                        }
                        UserCreditPoints::updateOrCreate(
                            [cn::USER_CREDIT_USER_ID_COL => $studentId],
                            [
                                cn::USER_CREDIT_USER_ID_COL => $studentId,
                                cn::USER_NO_OF_CREDIT_POINTS_COL => $GetStudentTotalCreditPoints
                            ]
                        );
                    }
                }
            }
           //return redirect('student/leaderboard')->with('success_msg','Manual Assign Credit Point Add Successfully');
           return $this->sendResponse(true);
        }
        return view('backend.credit_points.assign_credit_points',compact('GradeClassData','PeerGroupList'));
    }
}
