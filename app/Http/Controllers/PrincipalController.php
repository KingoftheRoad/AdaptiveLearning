<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Traits\Common;
use Exception;
use Illuminate\Support\Facades\Auth;
use App\Constants\DbConstant As cn;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use App\Traits\ResponseFormat;
use App\Helpers\Helper;
use App\Models\GradeSchoolMappings;
use App\Models\PreConfigurationDiffiltyLevel;
use App\Models\GradeClassMapping;
use App\Models\StrandUnitsObjectivesMappings;
use App\Models\Exam;
use App\Models\Strands;
use App\Models\LearningsUnits;
use App\Models\LearningsObjectives;
use App\Models\TeachersClassSubjectAssign;
use App\Models\Grades;
use App\Models\Question;
use App\Http\Services\AIApiService;
use App\Models\AttemptExams;
use App\Models\MyTeachingReport;
use App\Http\Services\TeacherGradesClassService;
use DB;

class PrincipalController extends Controller
{
    use Common, ResponseFormat;
    protected $TeacherGradesClassService;
    public function __construct(){
        $this->AIApiService = new AIApiService();
        $this->TeacherGradesClassService = new TeacherGradesClassService;
    }

    public function Dashboard(){
        return view('backend.principal.principal_dashboard');
    }

    public function index(Request $request){
        try{
            if(!in_array('principal_management_read', Helper::getPermissions(Auth::user()->{cn::USERS_ID_COL}))) {
                return  redirect(Helper::redirectRoleBasedDashboard(Auth::user()->{cn::USERS_ID_COL}));
            }
            $items = $request->items ?? 10;
            $TotalFilterData ='';
            $principalData = User::where([
                                cn::USERS_ROLE_ID_COL => cn::PRINCIPAL_ROLE_ID,
                                cn::USERS_SCHOOL_ID_COL => Auth::user()->{cn::USERS_SCHOOL_ID_COL},
                            ])
                            ->sortable()
                            ->orderBy(cn::USERS_ID_COL,'DESC')
                            ->paginate($items);
            $countUsersData =   User::where([
                                    cn::USERS_ROLE_ID_COL => cn::PRINCIPAL_ROLE_ID,
                                    cn::USERS_SCHOOL_ID_COL => Auth::user()->{cn::USERS_SCHOOL_ID_COL},
                                ])->count();
            if(isset($request->filter)){
                $Query = User::select('*');
                $Query->where([cn::USERS_SCHOOL_ID_COL=>auth()->user()->school_id,cn::USERS_ROLE_ID_COL=>cn::PRINCIPAL_ROLE_ID]);
                
                //search by principal Name
                if(isset($request->principalname) && !empty($request->principalname)){
                    $Query->where(cn::USERS_NAME_EN_COL,'like','%'.$this->encrypt($request->principalname).'%');
                }
                
                //search by email
                if(isset($request->email) && !empty($request->email)){
                    $Query->where(cn::USERS_EMAIL_COL,'like','%'.$request->email.'%');
                }
                
                //search by status
                if(isset($request->status) && !empty($request->status)){
                    $Query->where(cn::SUBJECTS_STATUS_COL,$request->status);
                }
                $countUsersData = $Query->count();
                $principalData = $Query->sortable()->paginate($items);
                $this->StoreAuditLogFunction($request->all(),'User',cn::USERS_ID_COL,'','Principal Details Filter',cn::USERS_TABLE_NAME,'');
            }
            return view('backend.principal.list',compact('principalData','countUsersData','TotalFilterData','items')); 
        }catch(Exception $exception){
            return back()->withError($exception->getMessage())->withInput();
        }
    }

    public function create(){
        try{
            if(!in_array('principal_management_create', Helper::getPermissions(Auth::user()->{cn::USERS_ID_COL}))) {
                return  redirect(Helper::redirectRoleBasedDashboard(Auth::user()->{cn::USERS_ID_COL}));
            }
            return view('backend.principal.add');
        }catch(Exception $exception){
            return back()->withError($exception->getMessage())->withInput();
        }
    }

    public function store(Request $request){
        try{
            if(!in_array('principal_management_create', Helper::getPermissions(Auth::user()->{cn::USERS_ID_COL}))) {
                return  redirect(Helper::redirectRoleBasedDashboard(Auth::user()->{cn::USERS_ID_COL}));
            }
            // Check validation
            $validator = Validator::make($request->all(), User::rules($request, 'create'), User::rulesMessages('create'));
            if ($validator->fails()) {
                return back()->withErrors($validator)->withInput();
            }
            $principalData = array(
                cn::USERS_ROLE_ID_COL       => cn::PRINCIPAL_ROLE_ID,
                cn::USERS_SCHOOL_ID_COL     => Auth::user()->{cn::USERS_SCHOOL_ID_COL},
                cn::USERS_NAME_EN_COL       => $this->encrypt($request->name_en),
                cn::USERS_NAME_CH_COL       => $this->encrypt($request->name_ch),
                cn::USERS_EMAIL_COL         => $request->email,
                cn::USERS_PASSWORD_COL      => Hash::make($request->password),
                cn::USERS_MOBILENO_COL      => ($request->mobile_no) ? $this->encrypt($request->mobile_no) : null,
                cn::USERS_STATUS_COL        => $request->status
            );
            $this->StoreAuditLogFunction($principalData,'User','','','Create Principal',cn::USERS_TABLE_NAME,'');
            $Users = User::create($principalData);
            if($Users){
                return redirect('principal')->with('success_msg', __('languages.principal_added_successfully'));
            }else{
                return back()->with('error_msg', __('languages.problem_was_occur_please_try_again'));
            }
        }catch(Exception $exception){
            return back()->withError($exception->getMessage())->withInput();
        }
    }

    public function show($id){
        //
    }

    public function edit($id){
        try{
            if(!in_array('principal_management_update', Helper::getPermissions(Auth::user()->{cn::USERS_ID_COL}))) {
                return  redirect(Helper::redirectRoleBasedDashboard(Auth::user()->{cn::USERS_ID_COL}));
            }
            $user = User::find($id);
            return view('backend.principal.edit',compact('user'));
        }catch(Exception $exception){
            return back()->withError($exception->getMessage())->withInput();
        }
    }

   
    public function update(Request $request, $id){
        if(!in_array('principal_management_update', Helper::getPermissions(Auth::user()->{cn::USERS_ID_COL}))) {
            return  redirect(Helper::redirectRoleBasedDashboard(Auth::user()->{cn::USERS_ID_COL}));
        }
        $validator = Validator::make($request->all(), User::rules($request, 'update', $id), User::rulesMessages('update'));
        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }
        if(User::where(cn::USERS_ID_COL,$id)->exists()){
            $updatePrincipal = array(
                cn::USERS_NAME_EN_COL       => $this->encrypt($request->name_en),
                cn::USERS_NAME_CH_COL       => $this->encrypt($request->name_ch),
                cn::USERS_MOBILENO_COL      => ($request->mobile_no) ? $this->encrypt($request->mobile_no) : null,
                cn::USERS_EMAIL_COL         => $request->email,
                cn::USERS_STATUS_COL        => $request->status
            );
            $this->StoreAuditLogFunction($updatePrincipal,'User',cn::USERS_ID_COL,$id,'Update Principal',cn::USERS_TABLE_NAME,'');
            $update = User::where(cn::USERS_ID_COL,$id)->update($updatePrincipal);
        }
        if(!empty($update)){
            return redirect('principal')->with('success_msg', __('languages.principal_updated_successfully'));
        }else{
            return back()->with('error_msg', __('languages.problem_was_occur_please_try_again'));
        }
    }

    public function destroy($id){
        try{
            if(!in_array('principal_management_delete', Helper::getPermissions(Auth::user()->{cn::USERS_ID_COL}))) {
                return  redirect(Helper::redirectRoleBasedDashboard(Auth::user()->{cn::USERS_ID_COL}));
            }
            $this->StoreAuditLogFunction('','User','','','Delete Principal ID '.$id,cn::USERS_TABLE_NAME,'');
            $User = User::find($id);
            if($User->delete()){
                return $this->sendResponse([], __('languages.principal_deleted_successfully'));
            }else{
                return $this->sendError(__('languages.problem_was_occur_please_try_again'), 422);
            }
        }catch (\Exception $exception) {
            return $this->sendError($exception->getMessage(), 404);
        }
    }

    public function getSelfLearningTestList(Request $request){
        $userId = Auth::id();
        $items = $request->items ?? 10;
        $schoolId = Auth::user()->{cn::USERS_SCHOOL_ID_COL};
        $roleId = Auth::user()->{cn::USERS_ROLE_ID_COL};
        $grade_id = array();
        $GradeClassListData = array();
        $Query = '';
        $class_type_id = array();
        $strandsList = array();
        $LearningUnits = array();
        $LearningObjectives = array();
        // $difficultyLevels = PreConfigurationDiffiltyLevel::all();
        $difficultyLevels = PreConfigurationDiffiltyLevel::where(cn::PRE_CONFIGURE_DIFFICULTY_CURRICULUM_YEAR_ID_COL,$this->GetCurriculumYear())->get();

        $gradesList = GradeSchoolMappings::with('grades')->where([cn::GRADES_MAPPING_SCHOOL_ID_COL => $schoolId,cn::GRADES_MAPPING_CURRICULUM_YEAR_ID_COL => $this->GetCurriculumYear()])->get();
        $gradesListIdArr = GradeSchoolMappings::where([cn::GRADES_MAPPING_SCHOOL_ID_COL => $schoolId,cn::GRADES_MAPPING_CURRICULUM_YEAR_ID_COL => $this->GetCurriculumYear()])->get()->pluck(cn::GRADES_MAPPING_GRADE_ID_COL)->toArray();
        
        $AssignedClass = GradeClassMapping::where([
                                                cn::GRADES_MAPPING_SCHOOL_ID_COL=>$schoolId,
                                                cn::GRADE_CLASS_MAPPING_CURRICULUM_YEAR_ID_COL=>$this->GetCurriculumYear()
                                            ])
                                            ->pluck(cn::GRADES_MAPPING_ID_COL)
                                            ->unique()
                                            ->toArray();
        $SelfLearningTestList = MyTeachingReport::whereIn(cn::TEACHING_REPORT_GRADE_ID_COL ,$gradesListIdArr)
                                                // ->whereIn(cn::TEACHING_REPORT_CLASS_ID_COL ,$TeacherAssignedClass)
                                                ->where([
                                                    cn::TEACHING_REPORT_CURRICULUM_YEAR_ID_COL => $this->GetCurriculumYear(),
                                                    cn::TEACHING_REPORT_REPORT_TYPE_COL => 'self_learning',
                                                    cn::TEACHING_REPORT_STUDY_TYPE_COL  =>  2,
                                                    cn::TEACHING_REPORT_SCHOOL_ID_COL   =>  $schoolId,
                                                ])
                                                ->with('exams','user','attempt_exams')
                                                ->whereHas('exams',function($q){
                                                    $q->where(cn::EXAM_CURRICULUM_YEAR_ID_COL, $this->GetCurriculumYear());
                                                })
                                                ->orderBy(cn::TEACHING_REPORT_EXAM_ID_COL,'DESC')->paginate($items);        
        if(isset($request->filter) && !empty($request->filter)){
            $grade_id = $request->grade_id;//For Filtration Selection
            $class_type_id = $request->class_type_id;
            $gradeId = ($request->grade_id) ? $request->grade_id : $gradesListIdArr;
            $classTypeId = ($request->class_type_id) ? $request->class_type_id : $AssignedClass;
            $SelfLearningTestList = MyTeachingReport::with('exams')->Select('*')
                                    ->whereHas('exams',function($q){
                                        $q->where(cn::EXAM_CURRICULUM_YEAR_ID_COL, $this->GetCurriculumYear());
                                    })
                                    ->whereIn(cn::TEACHING_REPORT_GRADE_ID_COL, $gradeId)
                                    ->whereIn(cn::TEACHING_REPORT_CLASS_ID_COL,$classTypeId)
                                    ->where([
                                        cn::TEACHING_REPORT_CURRICULUM_YEAR_ID_COL => $this->GetCurriculumYear(),
                                        cn::TEACHING_REPORT_REPORT_TYPE_COL => 'self_learning',
                                        cn::TEACHING_REPORT_STUDY_TYPE_COL  =>  2,
                                        cn::TEACHING_REPORT_SCHOOL_ID_COL   =>  $schoolId,
                                    ])
                                    ->orderBy(cn::TEACHING_REPORT_EXAM_ID_COL,'DESC')->paginate($items);
                        
            //After filtration selected value selected display.
            $GradeClassListDataArr = GradeClassMapping::whereIn(cn::GRADE_CLASS_MAPPING_GRADE_ID_COL,$gradeId)
                                        ->where(cn::GRADE_CLASS_MAPPING_SCHOOL_ID_COL,$schoolId)
                                        ->where(cn::GRADE_CLASS_MAPPING_CURRICULUM_YEAR_ID_COL,$this->GetCurriculumYear())
                                        ->whereIn(cn::GRADE_CLASS_MAPPING_ID_COL,$AssignedClass)
                                        ->get()
                                        ->toArray();
            if(!empty($GradeClassListDataArr)){
                foreach($GradeClassListDataArr as $class){
                    $GradeList = Grades::find($class[cn::GRADE_CLASS_MAPPING_GRADE_ID_COL]);
                    $GradeClassListData[strtoupper($class[cn::GRADE_CLASS_MAPPING_ID_COL])]=$GradeList->{cn::GRADES_NAME_COL}.strtoupper($class[cn::GRADE_CLASS_MAPPING_NAME_COL]);
                }
            }
        }
        return view('backend/principal/self_learning_test',compact('SelfLearningTestList','difficultyLevels','items','schoolId','gradesList','grade_id','class_type_id','GradeClassListData'));
    }

    /**
     * USE : Get Listing for Self-Learning Exercise
     */
    public function getSelfLearningExerciseList(Request $request){
        $userId = Auth::id();
        $items = $request->items ?? 10;
        $schoolId = Auth::user()->{cn::USERS_SCHOOL_ID_COL};
        $roleId = Auth::user()->{cn::USERS_ROLE_ID_COL};
        $grade_id = array();
        $GradeClassListData = array();
        $Query = '';
        $class_type_id = array();
        // $difficultyLevels = PreConfigurationDiffiltyLevel::all();
        $difficultyLevels = PreConfigurationDiffiltyLevel::where(cn::PRE_CONFIGURE_DIFFICULTY_CURRICULUM_YEAR_ID_COL,$this->GetCurriculumYear())->get();
        $gradesList = GradeSchoolMappings::with('grades')->where([cn::GRADES_MAPPING_SCHOOL_ID_COL => $schoolId,cn::GRADES_MAPPING_CURRICULUM_YEAR_ID_COL => $this->GetCurriculumYear()])->get();
        $gradesListIdArr = GradeSchoolMappings::where([cn::GRADES_MAPPING_SCHOOL_ID_COL => $schoolId,cn::GRADES_MAPPING_CURRICULUM_YEAR_ID_COL => $this->GetCurriculumYear()])->get()->pluck(cn::GRADES_MAPPING_GRADE_ID_COL)->toArray();
        $AssignedClass = GradeClassMapping::where([cn::GRADES_MAPPING_SCHOOL_ID_COL=>$schoolId,cn::GRADE_CLASS_MAPPING_CURRICULUM_YEAR_ID_COL => $this->GetCurriculumYear()])->pluck(cn::GRADES_MAPPING_ID_COL)->toArray();       
        $SelfLearningTestList = MyTeachingReport::whereIn(cn::TEACHING_REPORT_GRADE_ID_COL ,$gradesListIdArr) 
                                ->whereIn(cn::TEACHING_REPORT_CLASS_ID_COL ,$AssignedClass)
                                ->where([
                                    cn::TEACHING_REPORT_CURRICULUM_YEAR_ID_COL => $this->GetCurriculumYear(),
                                    cn::TEACHING_REPORT_REPORT_TYPE_COL => 'self_learning',
                                    cn::TEACHING_REPORT_STUDY_TYPE_COL  =>  1,
                                    cn::TEACHING_REPORT_SCHOOL_ID_COL   =>  $schoolId,
                                ])
                                ->with('exams','user','attempt_exams')
                                ->whereHas('exams',function($q){
                                    $q->where(cn::EXAM_CURRICULUM_YEAR_ID_COL, $this->GetCurriculumYear());
                                })
                                ->orderBy(cn::TEACHING_REPORT_EXAM_ID_COL,'DESC')->paginate($items);
        if(isset($request->filter) && !empty($request->filter)){
            $grade_id = $request->grade_id;//For Filtration Selection
            $class_type_id = $request->class_type_id;
            $gradeId = ($request->grade_id) ? $request->grade_id : $gradesListIdArr;
            $classTypeId = ($request->class_type_id) ? $request->class_type_id : $AssignedClass;
            // Create filter query object
            $SelfLearningTestList = MyTeachingReport::with('exams')->Select('*')
                                    ->whereHas('exams',function($q){
                                        $q->where(cn::EXAM_CURRICULUM_YEAR_ID_COL, $this->GetCurriculumYear());
                                    })
                                    ->whereIn(cn::TEACHING_REPORT_GRADE_ID_COL , $gradeId)
                                    ->whereIn(cn::TEACHING_REPORT_CLASS_ID_COL ,$classTypeId)
                                    ->where([
                                        cn::TEACHING_REPORT_CURRICULUM_YEAR_ID_COL => $this->GetCurriculumYear(),
                                        cn::TEACHING_REPORT_REPORT_TYPE_COL => 'self_learning',
                                        cn::TEACHING_REPORT_STUDY_TYPE_COL  =>  1,
                                        cn::TEACHING_REPORT_SCHOOL_ID_COL   =>  $schoolId,
                                    ])
                                    ->orderBy(cn::TEACHING_REPORT_EXAM_ID_COL,'DESC')->paginate($items);
            //After filtration selected value selected display.
            $GradeClassListDataArr = GradeClassMapping::whereIn(cn::GRADE_CLASS_MAPPING_GRADE_ID_COL,$gradeId)
            ->where(cn::GRADE_CLASS_MAPPING_SCHOOL_ID_COL,$schoolId)
            ->whereIn(cn::GRADE_CLASS_MAPPING_ID_COL,$AssignedClass)->get()->toArray();
            if(!empty($GradeClassListDataArr)){
                foreach($GradeClassListDataArr as $class){
                    $GradeList = Grades::find($class[cn::GRADE_CLASS_MAPPING_GRADE_ID_COL]);
                    $GradeClassListData[strtoupper($class[cn::GRADE_CLASS_MAPPING_ID_COL])]=$GradeList->{cn::GRADES_NAME_COL}.strtoupper($class[cn::GRADE_CLASS_MAPPING_NAME_COL]);
                }
            }
        }        
        return view('backend/principal/self_learning_exercise',compact('SelfLearningTestList','difficultyLevels','items','schoolId','gradesList','grade_id','class_type_id','GradeClassListData'));
    }

    public function getAssignmentTestList(Request $request){
        $userId = Auth::id();
        $items = $request->items ?? 10;
        $schoolId = Auth::user()->{cn::USERS_SCHOOL_ID_COL};
        $roleId = Auth::user()->{cn::USERS_ROLE_ID_COL};
        $grade_id = array();
        $class_type_id = array();
        $GradeClassListData = array();
        $Query = '';
        // $difficultyLevels = PreConfigurationDiffiltyLevel::all();
        $difficultyLevels = PreConfigurationDiffiltyLevel::where(cn::PRE_CONFIGURE_DIFFICULTY_CURRICULUM_YEAR_ID_COL,$this->GetCurriculumYear());
        $gradesList = GradeSchoolMappings::with('grades')->where(cn::GRADES_MAPPING_SCHOOL_ID_COL,$schoolId)->get();
        $gradesListIdArr = GradeSchoolMappings::where([
                                                    cn::GRADES_MAPPING_SCHOOL_ID_COL => $schoolId,
                                                    cn::GRADES_MAPPING_CURRICULUM_YEAR_ID_COL => $this->GetCurriculumYear()
                                                ])
                                                ->get()
                                                ->pluck(cn::GRADES_MAPPING_GRADE_ID_COL)
                                                ->toArray();
        $AssignedClass = GradeClassMapping::where([
                                                    cn::GRADE_CLASS_MAPPING_SCHOOL_ID_COL=>$schoolId,
                                                    cn::GRADE_CLASS_MAPPING_CURRICULUM_YEAR_ID_COL => $this->GetCurriculumYear()
                                                ])->pluck(cn::GRADE_CLASS_MAPPING_ID_COL)
                                                ->unique()
                                                ->toArray();
        $classListIdArray =  GradeClassMapping::whereIn(cn::GRADE_CLASS_MAPPING_GRADE_ID_COL,$gradesListIdArr)
                            ->where([
                                        cn::GRADE_CLASS_MAPPING_SCHOOL_ID_COL => $schoolId,
                                        cn::GRADE_CLASS_MAPPING_CURRICULUM_YEAR_ID_COL => $this->GetCurriculumYear()
                                    ])
                            ->pluck(cn::GRADE_CLASS_MAPPING_ID_COL)
                            ->toArray();

        // Find Teacher Peer Group Ids
        $TeachersPeerGroupIds = [];
        $TeachersPeerGroupIds = $this-> TeacherGradesClassService->GetSchoolBasedPeerGroupIds(Auth::user()->{cn::USERS_SCHOOL_ID_COL});
        $AssignmentTestList =   MyTeachingReport::where(function($query) use($gradesListIdArr, $AssignedClass, $TeachersPeerGroupIds){
                                    $query->whereIn(cn::TEACHING_REPORT_GRADE_ID_COL ,$gradesListIdArr)
                                    ->whereIn(cn::TEACHING_REPORT_CLASS_ID_COL ,$AssignedClass)
                                    ->orWhereIn(cn::TEACHING_REPORT_PEER_GROUP_ID,$TeachersPeerGroupIds);
                                })
                                ->where([
                                    cn::TEACHING_REPORT_CURRICULUM_YEAR_ID_COL => $this->GetCurriculumYear(),
                                    cn::TEACHING_REPORT_REPORT_TYPE_COL => 'assignment_test',
                                    cn::TEACHING_REPORT_STUDY_TYPE_COL  =>  2,
                                    cn::TEACHING_REPORT_SCHOOL_ID_COL   =>  $schoolId,
                                ])
                                ->with('exams','peerGroup')
                                ->whereHas('exams',function($q){
                                    $q->where(cn::EXAM_CURRICULUM_YEAR_ID_COL, $this->GetCurriculumYear())
                                        ->where(cn::EXAM_TABLE_STATUS_COLS,'<>','inactive');
                                })
                                ->orderBy(cn::TEACHING_REPORT_EXAM_ID_COL,'DESC')->paginate($items);
        
        // For Filtration
        if(isset($request->filter) && !empty($request->filter)){
            $grade_id = $request->grade_id;//For Filtration Selection
            $class_type_id = $request->class_type_id;
            $gradeId = ($request->grade_id) ? $request->grade_id : $gradesListIdArr;
            $classTypeId = ($request->class_type_id) ? $request->class_type_id : $AssignedClass;
            $AssignmentTestList = MyTeachingReport::Select('*')
                                    ->with('exams','peerGroup')
                                    ->whereHas('exams',function($q){
                                        $q->where(cn::EXAM_CURRICULUM_YEAR_ID_COL, $this->GetCurriculumYear())
                                        ->where(cn::EXAM_TABLE_STATUS_COLS,'<>','inactive');
                                    })
                                    ->where(function($query) use($gradeId, $classTypeId, $TeachersPeerGroupIds){
                                        $query->whereIn(cn::TEACHING_REPORT_GRADE_ID_COL ,$gradeId)
                                            ->whereIn(cn::TEACHING_REPORT_CLASS_ID_COL ,$classTypeId);
                                    })
                                    ->where([
                                        cn::TEACHING_REPORT_CURRICULUM_YEAR_ID_COL => $this->GetCurriculumYear(),
                                        cn::TEACHING_REPORT_REPORT_TYPE_COL => 'assignment_test',
                                        cn::TEACHING_REPORT_STUDY_TYPE_COL  => 2,
                                        cn::TEACHING_REPORT_SCHOOL_ID_COL   => $schoolId,
                                    ])
                                    ->orderBy(cn::TEACHING_REPORT_EXAM_ID_COL,'DESC')->paginate($items);

            //After filtration selected value selected display.
            $GradeClassListDataArr = GradeClassMapping::whereIn(cn::GRADE_CLASS_MAPPING_GRADE_ID_COL,$gradeId)
                ->where([
                    cn::GRADE_CLASS_MAPPING_SCHOOL_ID_COL => $schoolId,
                    cn::GRADE_CLASS_MAPPING_CURRICULUM_YEAR_ID_COL => $this->GetCurriculumYear()
                ])
                ->whereIn(cn::GRADE_CLASS_MAPPING_ID_COL,$AssignedClass)->get()->toArray();
            if(!empty($GradeClassListDataArr)){
                foreach($GradeClassListDataArr as $class){
                    $GradeList = Grades::find($class[cn::GRADE_CLASS_MAPPING_GRADE_ID_COL]);
                    $GradeClassListData[strtoupper($class[cn::GRADE_CLASS_MAPPING_ID_COL])]=$GradeList->{cn::GRADES_NAME_COL}.strtoupper($class[cn::GRADE_CLASS_MAPPING_NAME_COL]);
                }
            }
        }
        return view('backend/principal/assignment_test',compact('AssignmentTestList','difficultyLevels','items','schoolId','gradesList','grade_id','class_type_id','GradeClassListData'));
    }

    public function getAssignmentExerciseList(Request $request){
        $userId = Auth::id();
        $items = $request->items ?? 10;
        $schoolId = Auth::user()->{cn::USERS_SCHOOL_ID_COL};
        $roleId = Auth::user()->{cn::USERS_ROLE_ID_COL};
        $grade_id = array();
        $GradeClassListData = array();
        $Query = '';
        $class_type_id = array();
        // $difficultyLevels = PreConfigurationDiffiltyLevel::all();
        $difficultyLevels = PreConfigurationDiffiltyLevel::where(cn::PRE_CONFIGURE_DIFFICULTY_CURRICULUM_YEAR_ID_COL,$this->GetCurriculumYear());

        $gradesList = GradeSchoolMappings::with('grades')->where([
                                                                    cn::GRADES_MAPPING_SCHOOL_ID_COL=> $schoolId,
                                                                    cn::GRADES_MAPPING_CURRICULUM_YEAR_ID_COL => $this->GetCurriculumYear()
                                                                ])
                                                                ->get();
        $gradesListIdArr = GradeSchoolMappings::where([
                                                        cn::GRADES_MAPPING_SCHOOL_ID_COL=> $schoolId,
                                                        cn::GRADES_MAPPING_CURRICULUM_YEAR_ID_COL => $this->GetCurriculumYear()
                                                    ])
                                                    ->get()
                                                    ->pluck(cn::GRADES_MAPPING_GRADE_ID_COL)
                                                    ->toArray();
        $AssignedClass = GradeClassMapping::where([
                                                    cn::GRADE_CLASS_MAPPING_SCHOOL_ID_COL=>$schoolId,
                                                    cn::GRADE_CLASS_MAPPING_CURRICULUM_YEAR_ID_COL => $this->GetCurriculumYear()
                                                ])->pluck(cn::GRADE_CLASS_MAPPING_ID_COL)
                                                ->unique()
                                                ->toArray();
        
        // Find Teacher Peer Group Ids
        $TeachersPeerGroupIds = [];
        $TeachersPeerGroupIds = $this-> TeacherGradesClassService->GetSchoolBasedPeerGroupIds(Auth::user()->{cn::USERS_SCHOOL_ID_COL});

        $AssignmentExerciseList = MyTeachingReport::where(function($query) use($gradesListIdArr, $AssignedClass,$TeachersPeerGroupIds){
                                                        $query->whereIn(cn::TEACHING_REPORT_GRADE_ID_COL,$gradesListIdArr)
                                                        ->whereIn(cn::TEACHING_REPORT_CLASS_ID_COL ,$AssignedClass)
                                                        ->orWhereIn(cn::TEACHING_REPORT_PEER_GROUP_ID,$TeachersPeerGroupIds);
                                                    })
                                                    ->where(cn::TEACHING_REPORT_CURRICULUM_YEAR_ID_COL,$this->GetCurriculumYear())
                                                    ->where(cn::TEACHING_REPORT_REPORT_TYPE_COL,'assignment_test')
                                                    ->where(cn::TEACHING_REPORT_STUDY_TYPE_COL,1)
                                                    ->where(cn::TEACHING_REPORT_SCHOOL_ID_COL,$schoolId)
                                                    ->with(['exams','peerGroup'])
                                                    ->whereHas('exams',function($q){
                                                        $q->where(cn::EXAM_CURRICULUM_YEAR_ID_COL, $this->GetCurriculumYear())
                                                            ->where(cn::EXAM_TABLE_STATUS_COLS,'<>','inactive');
                                                    })
                                                    ->orderBy(cn::TEACHING_REPORT_EXAM_ID_COL,'DESC')->paginate($items);
        
        // For Filtration
        if(isset($request->filter) && !empty($request->filter)){
            $grade_id = $request->grade_id;//For Filtration Selection
            $class_type_id = $request->class_type_id;
            $gradeId = ($request->grade_id) ? $request->grade_id : $gradesListIdArr;
            $classTypeId = ($request->class_type_id) ? $request->class_type_id : $AssignedClass;
            $Query = MyTeachingReport::with('exams','peerGroup')->Select('*')
                    ->where(cn::TEACHING_REPORT_CURRICULUM_YEAR_ID_COL,$this->GetCurriculumYear())
                    ->whereHas('exams',function($q){
                        $q->where(cn::EXAM_CURRICULUM_YEAR_ID_COL, $this->GetCurriculumYear())
                        ->where(cn::EXAM_TABLE_STATUS_COLS,'<>','inactive');
                    });
            $Query->whereIn(cn::TEACHING_REPORT_GRADE_ID_COL, $gradeId)
                ->whereIn(cn::TEACHING_REPORT_CLASS_ID_COL,$classTypeId)
                ->where([
                    cn::TEACHING_REPORT_REPORT_TYPE_COL => 'assignment_test',
                    cn::TEACHING_REPORT_STUDY_TYPE_COL  =>  1,
                    cn::TEACHING_REPORT_SCHOOL_ID_COL   =>  $schoolId,
                ]);
            $AssignmentExerciseList = $Query->orderBy(cn::TEACHING_REPORT_EXAM_ID_COL,'DESC')->paginate($items);

            //After filtration selected value selected display.
            $GradeClassListDataArr = GradeClassMapping::whereIn(cn::GRADE_CLASS_MAPPING_GRADE_ID_COL,$gradeId)
                                        ->where(cn::GRADE_CLASS_MAPPING_SCHOOL_ID_COL,$schoolId)
                                        ->where(cn::GRADE_CLASS_MAPPING_CURRICULUM_YEAR_ID_COL,$this->GetCurriculumYear())
                                        ->whereIn(cn::GRADE_CLASS_MAPPING_ID_COL,$AssignedClass)
                                        ->get()
                                        ->toArray();
            if(!empty($GradeClassListDataArr)){
                foreach($GradeClassListDataArr as $class){
                    $GradeList = Grades::find($class[cn::GRADE_CLASS_MAPPING_GRADE_ID_COL]);
                    $GradeClassListData[strtoupper($class[cn::GRADE_CLASS_MAPPING_ID_COL])]=$GradeList->{cn::GRADES_NAME_COL}.strtoupper($class[cn::GRADE_CLASS_MAPPING_NAME_COL]);
                }
            }
        }
        return view('backend/principal/assignment_exercise',compact('AssignmentExerciseList','difficultyLevels','items','schoolId','gradesList','grade_id','class_type_id','GradeClassListData'));
    }
}
