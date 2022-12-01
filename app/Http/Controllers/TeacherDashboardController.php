<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Controllers\CommonController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Grades;
use App\Models\User;
use App\Models\TeachersClassSubjectAssign;
use App\Models\GradeClassMapping;
use App\Constants\DbConstant As cn;
use App\Traits\Common;
use App\Traits\ResponseFormat;
use Exception;
use App\Helpers\Helper;
use App\Models\PeerGroup;
use App\Models\PeerGroupMember;
use App\Models\Exam;
use App\Models\ExamGradeClassMappingModel;
use App\Models\UserCreditPointHistory;
use App\Models\UserCreditPoints;
use App\Http\Services\TeacherGradesClassService;

class TeacherDashboardController extends Controller
{
    use Common, ResponseFormat;
    protected $TeacherGradesClassService,$CommonController;

    public function __construct(){
        $this->middleware('auth');
        $this->middleware('preventBackHistory');
        $this->TeacherGradesClassService = new TeacherGradesClassService;
        $this->CommonController = new CommonController();
    }

    public function index(){
        return view('backend.teacher_dashboard');
    }
    
    public function MyClass(Request $request){
        try{
            //  Laravel Pagination set in Cookie
            //$this->paginationCookie('TeacherMyClassList',$request);
            if(!in_array('my_classes_read', Helper::getPermissions(Auth::user()->{cn::USERS_ID_COL}))) {
                return redirect('/');
            }
            $gradeid =  array();
            $gradeClassId = array();
            // $GroupData = PeerGroup::where(cn::PEER_GROUP_CREATED_BY_USER_ID_COL,Auth::user()->{cn::USERS_ID_COL})->get();
            $GroupData = PeerGroup::where([cn::PEER_GROUP_CREATED_BY_USER_ID_COL => Auth::user()->{cn::USERS_ID_COL},
                                            cn::PEER_GROUP_CURRICULUM_YEAR_ID_COL => $this->GetCurriculumYear()
                                        ])->get();
            // $gradesList = TeachersClassSubjectAssign::where([cn::TEACHER_CLASS_SUBJECT_TEACHER_ID_COL=>Auth()->user()->{cn::USERS_ID_COL}])->with('getClass')->get()->unique(cn::TEACHER_CLASS_SUBJECT_CLASS_ID_COL);
            $gradesList = TeachersClassSubjectAssign::where([
                                                            cn::TEACHER_CLASS_SUBJECT_TEACHER_ID_COL => Auth()->user()->{cn::USERS_ID_COL},
                                                            cn::TEACHER_CLASS_SUBJECT_CURRICULUM_YEAR_ID_COL => $this->GetCurriculumYear()
                                                        ])
                                                        ->with('getClass')
                                                        ->get()
                                                        ->unique(cn::TEACHER_CLASS_SUBJECT_CLASS_ID_COL);
            // $gradeid = TeachersClassSubjectAssign::where([cn::TEACHER_CLASS_SUBJECT_TEACHER_ID_COL => Auth()->user()->{cn::USERS_ID_COL}])->pluck(cn::TEACHER_CLASS_SUBJECT_CLASS_ID_COL)->toArray();
            // $gradeClass = TeachersClassSubjectAssign::where([cn::TEACHER_CLASS_SUBJECT_TEACHER_ID_COL => Auth()->user()->{cn::USERS_ID_COL}])->pluck(cn::TEACHER_CLASS_SUBJECT_CLASS_NAME_ID_COL)->toArray();
            $gradeid = TeachersClassSubjectAssign::where([
                                                            cn::TEACHER_CLASS_SUBJECT_TEACHER_ID_COL => Auth()->user()->{cn::USERS_ID_COL},
                                                            cn::TEACHER_CLASS_SUBJECT_CURRICULUM_YEAR_ID_COL => $this->GetCurriculumYear()
                                                        ])->pluck(cn::TEACHER_CLASS_SUBJECT_CLASS_ID_COL)->toArray();
            $gradeClass = TeachersClassSubjectAssign::where([
                                                                cn::TEACHER_CLASS_SUBJECT_TEACHER_ID_COL => Auth()->user()->{cn::USERS_ID_COL},
                                                                cn::TEACHER_CLASS_SUBJECT_CURRICULUM_YEAR_ID_COL => $this->GetCurriculumYear()
                                                            ])->pluck(cn::TEACHER_CLASS_SUBJECT_CLASS_NAME_ID_COL)->toArray();
            if(isset($gradeClass) && !empty($gradeClass)){
                $gradeClass = implode(',', $gradeClass);
                $gradeClassId = explode(',',$gradeClass);
            }
            
            if(isset($request->filter) && isset($request->student_grade_id) && !empty($request->student_grade_id) && $request->student_grade_id!='all'){
                $gradeid = array($request->student_grade_id);
            }
            $classTypeOptions = '';
            $items = $request->items ?? 10;
            $grade_id = empty($request->grade) ? 0 : $request->grade;
            ($items == 0) ? $items = 10 : $request->items;//for maintain all filtration when count data value is 0
            
            $gradeData = Grades::whereIn(cn::GRADES_ID_COL,$gradeid)->get();
            // $studentList = User::whereIn(cn::USERS_GRADE_ID_COL,$gradeid)->whereIn(cn::USERS_CLASS_ID_COL,$gradeClassId)
            //                     ->where([
            //                                 cn::USERS_SCHOOL_ID_COL=>Auth::user()->{cn::USERS_SCHOOL_ID_COL},
            //                                 cn::USERS_ROLE_ID_COL=>cn::STUDENT_ROLE_ID
            //                             ])
            //                             ->where(cn::USERS_GRADE_ID_COL,'<>','')
            //                             ->sortable()
            //                             ->paginate($items);
            $studentList = User::whereIn(cn::USERS_ID_COL,$this->curriculum_year_mapping_student_ids($gradeid,$gradeClassId,Auth::user()->school_id))
                                ->where([
                                        cn::USERS_SCHOOL_ID_COL=>Auth::user()->{cn::USERS_SCHOOL_ID_COL},
                                        cn::USERS_ROLE_ID_COL=>cn::STUDENT_ROLE_ID
                                    ])
                                    ->where(cn::USERS_GRADE_ID_COL,'<>','')
                                    ->sortable()
                                    ->paginate($items);
            // $GradeClassData = GradeClassMapping::whereIn(cn::GRADE_CLASS_MAPPING_GRADE_ID_COL,$gradeid)->whereIn(cn::GRADE_CLASS_MAPPING_ID_COL,$gradeClassId)->where([cn::GRADE_CLASS_MAPPING_SCHOOL_ID_COL=>$this->isTeacherLogin()])->get();
            $GradeClassData = GradeClassMapping::whereIn(cn::GRADE_CLASS_MAPPING_GRADE_ID_COL,$gradeid)
                                                ->whereIn(cn::GRADE_CLASS_MAPPING_ID_COL,$gradeClassId)
                                                ->where([
                                                        cn::GRADE_CLASS_MAPPING_SCHOOL_ID_COL          => $this->isTeacherLogin(),
                                                        cn::GRADE_CLASS_MAPPING_CURRICULUM_YEAR_ID_COL => $this->GetCurriculumYear()
                                                    ])
                                                ->get();
            if(!empty($GradeClassData)){
                foreach($GradeClassData as $class){
                    $GradeList = Grades::find($class->grade_id);
                    $selected='';
                    if(isset($request->student_grade_id) && $request->student_grade_id == 'all' && !isset($request->class_type_id)){
                        $selected='selected="selected"';
                    }else if(!isset($request->student_grade_id) && !isset($request->class_type_id)){
                        $selected='selected="selected"';
                    }else if(isset($request->class_type_id) && !empty($request->class_type_id) && in_array($class->{cn::GRADE_CLASS_MAPPING_ID_COL},$request->class_type_id)){
                        $selected='selected="selected"';
                    }
                    $classTypeOptions .= '<option '.$selected.' value='.strtoupper($class->{cn::GRADE_CLASS_MAPPING_ID_COL}).'>'.$GradeList->{cn::GRADES_NAME_COL}.strtoupper($class->{cn::GRADE_CLASS_MAPPING_NAME_COL}).'</option>';
                }
            }
            if(isset($request->filter)){
                // $Query = User::with('getUserCreditPoints')->select('*')->where([cn::USERS_SCHOOL_ID_COL=>Auth::user()->{cn::USERS_SCHOOL_ID_COL},cn::USERS_ROLE_ID_COL=>cn::STUDENT_ROLE_ID]);
                $Query = User::with('getUserCreditPoints')->select('*')
                                                          ->where([
                                                                    cn::USERS_SCHOOL_ID_COL=>Auth::user()->{cn::USERS_SCHOOL_ID_COL},
                                                                    cn::USERS_ROLE_ID_COL=>cn::STUDENT_ROLE_ID
                                                                ]);
                // For Only search text Field in filtration
                if(isset($request->searchtext) && !empty($request->searchtext)){
                    $Query->where(function($query) use ($request){
                        $query->Where(cn::USERS_EMAIL_COL,'like','%'.$request->searchtext.'%')
                        ->orWhere(cn::USERS_NAME_EN_COL,'like','%'.$this->encrypt($request->searchtext).'%')
                        ->orWhere(cn::USERS_NAME_CH_COL,'like','%'.$this->encrypt($request->searchtext).'%');
                    });
                }
                if(isset($request->student_grade_id) && !empty($request->student_grade_id) && $request->student_grade_id!='all'){
                    // $Query->where(cn::USERS_GRADE_ID_COL,$request->student_grade_id)->whereIn(cn::USERS_CLASS_ID_COL,$gradeClassId);
                    $Query->whereIn(cn::USERS_ID_COL,$this->curriculum_year_mapping_student_ids($request->student_grade_id,$gradeClassId,Auth::user()->school_id));
                }
                if(isset($request->student_grade_id) && !empty($request->student_grade_id) && $request->student_grade_id=='all'){
                    // $Query->whereIn(cn::USERS_CLASS_ID_COL,$gradeClassId);
                    $Query->whereIn(cn::USERS_ID_COL,$this->curriculum_year_mapping_student_ids($gradeid,$gradeClassId,Auth::user()->school_id));
                }
                if(isset($request->class_type_id) && !empty($request->class_type_id)){
                    $Query->whereIn(cn::USERS_CLASS_ID_COL,$request->class_type_id);
                }
                $studentList = $Query->orderBy(cn::USERS_ID_COL,'DESC')->sortable()->paginate($items);
            }
            return view('backend.teacher.class_student_list',compact('gradesList','studentList','items','classTypeOptions','GroupData'));
        }catch(Exception $exception){
            return back()->withError($exception->getMessage())->withInput();
        }
    }

    public function studentsProfile($id){
        try{
            $studentId = $id;
            if($this->isTeacherLogin()){
                if(!in_array('my_classes_read', Helper::getPermissions(Auth::user()->{cn::USERS_ID_COL}))) {
                    return redirect('/');
                }
            }
            $profile = User::find($id);
            return view('backend.teacher.students_profile',compact('profile','studentId'));
        }catch(Exception $exception){
            return back()->withError($exception->getMessage())->withInput();
        }
    }
    
    public function MySubject(Request $request){
        try{
            if(!in_array('my_subjects_read', Helper::getPermissions(Auth::user()->{cn::USERS_ID_COL}))) {
                return redirect('/');
            }
            //  Laravel Pagination set in Cookie
            //$this->paginationCookie('TeacherMySubjectList',$request);

            $id = auth()->user()->{cn::USERS_ID_COL};
            $TotalFilterData ='';
            $items = $request->items ?? 10;
            $List = TeachersClassSubjectAssign::where([
                                                        cn::TEACHER_CLASS_SUBJECT_TEACHER_ID_COL => $id,
                                                        cn::TEACHER_CLASS_SUBJECT_CURRICULUM_YEAR_ID_COL => $this->GetCurriculumYear()
                                                    ])
                                                    ->with('getClass')
                                                    ->sortable()
                                                    ->paginate($items);
            return view('backend.teacher.subject_list',compact('List','items','TotalFilterData'));
        }catch(Exception $exception){
            return back()->withError($exception->getMessage())->withInput();
        }
    }

    public function assignStudentInGroup(Request $request){
        $studentIds = $request->studentIds;
        $peerGroupID = $request->peergroupid;
        foreach($studentIds as $memberId){
            if(PeerGroupMember::where(cn::PEER_GROUP_MEMBERS_PEER_GROUP_ID_COL,$peerGroupID)->where(cn::PEER_GROUP_MEMBERS_MEMBER_ID_COL,$memberId)->doesntExist()){
                $postData = [
                    cn::PEER_GROUP_MEMBERS_PEER_GROUP_ID_COL => $peerGroupID,
                    cn::PEER_GROUP_MEMBERS_MEMBER_ID_COL     => $memberId,
                    cn::PEER_GROUP_MEMBERS_STATUS_COL        => 1
                ];
                $createMember = PeerGroupMember::create($postData);
            }
            SELF::AssignedStudentInGroup($studentIds,$peerGroupID);
        }

        return $this->sendResponse([], __('languages.group_created_successfully')); 
    }

    public function AssignedStudentInGroup($studentIds,$peerGroupID){
        $oldStudentIds = [];
        $examData = Exam::whereRaw("find_in_set($peerGroupID,peer_group_ids)")->get();
        if(!empty($examData)){
            foreach($examData as $exams){
                if(!empty($exams->student_ids)){
                    $oldStudentIds = explode(',',$exams->student_ids);
                    $newStudentIds = implode(',',array_unique(array_merge($oldStudentIds,$studentIds)));
                    Exam::find($exams->id)->update([cn::EXAM_TABLE_STUDENT_IDS_COL=>$newStudentIds]);
                    SElF::ExamGradeClassMappingStudentDataUpdate($exams->id,Auth::user()->{cn::USERS_SCHOOL_ID_COL},$peerGroupID,$newStudentIds);
                }
            }
        }
    }

    public function ExamGradeClassMappingStudentDataUpdate($examId,$schoolID,$peerGroupID,$finalStudentIds){
        ExamGradeClassMappingModel::where([
                                            cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_EXAM_ID_COL    =>$examId,
                                            cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_SCHOOL_ID_COL  =>$schoolID,
                                            cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_PEER_GROUP_ID_COL =>$peerGroupID
                                        ])->update([cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_STUDENT_IDS_COL=>$finalStudentIds]);
    }
}
