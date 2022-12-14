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
            $principalData = User::where([cn::USERS_ROLE_ID_COL => cn::PRINCIPAL_ROLE_ID,cn::USERS_SCHOOL_ID_COL => Auth::user()->{cn::USERS_SCHOOL_ID_COL}])->sortable()->orderBy(cn::USERS_ID_COL,'DESC')->paginate($items);
            $countUsersData = User::all()->count();
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
        $difficultyLevels = PreConfigurationDiffiltyLevel::all();

        $gradesList = GradeSchoolMappings::with('grades')->where(cn::GRADES_MAPPING_SCHOOL_ID_COL,$schoolId)->get();
        $gradesListIdArr = GradeSchoolMappings::where(cn::GRADES_MAPPING_SCHOOL_ID_COL,$schoolId)->get()->pluck(cn::GRADES_MAPPING_GRADE_ID_COL)->toArray();
        
        $AssignedClass = GradeClassMapping::where(cn::GRADES_MAPPING_SCHOOL_ID_COL,$schoolId)->pluck(cn::GRADES_MAPPING_ID_COL)->unique()->toArray();
        $SelfLearningTestList = MyTeachingReport::whereIn(cn::TEACHING_REPORT_GRADE_ID_COL ,$gradesListIdArr)
                                                // ->whereIn(cn::TEACHING_REPORT_CLASS_ID_COL ,$TeacherAssignedClass)
                                                ->where([
                                                    cn::TEACHING_REPORT_REPORT_TYPE_COL => 'self_learning',
                                                    cn::TEACHING_REPORT_STUDY_TYPE_COL  =>  2,
                                                    cn::TEACHING_REPORT_SCHOOL_ID_COL   =>  $schoolId,
                                                ])
                                                ->with('exams','user','attempt_exams')
                                                ->orderBy(cn::TEACHING_REPORT_EXAM_ID_COL,'DESC')->paginate($items);

        // Get Current student grade id wise strand list
        $strandsList = StrandUnitsObjectivesMappings::where([cn::OBJECTIVES_MAPPINGS_SUBJECT_ID_COL => 1])->pluck(cn::OBJECTIVES_MAPPINGS_STRAND_ID_COL);
        if($strandsList->isNotEmpty()){
            $strandsIds = array_unique($strandsList->toArray());
            $strandsList = Strands::whereIn(cn::STRANDS_ID_COL, $strandsIds)->get();

            // Get The learning units based on first Strands
            $learningUnitsIds = StrandUnitsObjectivesMappings::where(cn::OBJECTIVES_MAPPINGS_GRADE_ID_COL,Auth::user()->{cn::USERS_GRADE_ID_COL})
                        ->where(cn::OBJECTIVES_MAPPINGS_SUBJECT_ID_COL,1)
                        ->where(cn::OBJECTIVES_MAPPINGS_STRAND_ID_COL,$strandsList[0]->{cn::STRANDS_ID_COL})
                        ->pluck(cn::OBJECTIVES_MAPPINGS_LEARNING_UNIT_ID_COL);
            if(!empty($learningUnitsIds)){
                $learningUnitsIds = array_unique($learningUnitsIds->toArray());
                $LearningUnits = LearningsUnits::whereIn(cn::LEARNING_UNITS_ID_COL, $learningUnitsIds)->get();

                // Get the Learning objectives based on first learning units
                $learningObjectivesIds = StrandUnitsObjectivesMappings::where(cn::OBJECTIVES_MAPPINGS_GRADE_ID_COL,Auth::user()->{cn::USERS_GRADE_ID_COL})
                        ->where(cn::OBJECTIVES_MAPPINGS_SUBJECT_ID_COL,1)
                        ->where(cn::OBJECTIVES_MAPPINGS_STRAND_ID_COL,$strandsList[0]->{cn::STRANDS_ID_COL})
                        ->whereIn(cn::OBJECTIVES_MAPPINGS_LEARNING_UNIT_ID_COL,$LearningUnits->pluck(cn::LEARNING_UNITS_ID_COL))
                        ->pluck(cn::OBJECTIVES_MAPPINGS_LEARNING_OBJECTIVES_ID_COL);
                if(!empty($learningObjectivesIds)){
                    $learningObjectivesIds = array_unique($learningObjectivesIds->toArray());
                    // $LearningObjectives = LearningsObjectives::whereIn(cn::LEARNING_OBJECTIVES_ID_COL, $learningObjectivesIds)->get();
                    $LearningObjectives = LearningsObjectives::IsAvailableQuestion()->whereIn(cn::LEARNING_OBJECTIVES_ID_COL, $learningObjectivesIds)->get();
                }
            }
        }
        

        if(isset($request->filter) && !empty($request->filter)){
            $grade_id = $request->grade_id;//For Filtration Selection
            $class_type_id = $request->class_type_id;
            $gradeId = ($request->grade_id) ? $request->grade_id : $gradesListIdArr;
            $classTypeId = ($request->class_type_id) ? $request->class_type_id : $AssignedClass;
            $SelfLearningTestList = MyTeachingReport::with('exams')->Select('*')
                        ->whereIn(cn::TEACHING_REPORT_GRADE_ID_COL, $gradeId)
                        ->whereIn(cn::TEACHING_REPORT_CLASS_ID_COL,$classTypeId)
                        ->where([
                            cn::TEACHING_REPORT_REPORT_TYPE_COL => 'self_learning',
                            cn::TEACHING_REPORT_STUDY_TYPE_COL  =>  2,
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
        return view('backend/principal/self_learning_test',compact('SelfLearningTestList','difficultyLevels','items','schoolId','gradesList','grade_id','class_type_id','GradeClassListData','strandsList','LearningUnits','LearningObjectives'));
    }

    public function getSelfLearningExerciseList(Request $request){
        $userId = Auth::id();
        $items = $request->items ?? 10;
        $schoolId = Auth::user()->{cn::USERS_SCHOOL_ID_COL};
        $roleId = Auth::user()->{cn::USERS_ROLE_ID_COL};
        $grade_id = array();
        $GradeClassListData = array();
        $Query = '';
        $class_type_id = array();
        $difficultyLevels = PreConfigurationDiffiltyLevel::all();

        $gradesList = GradeSchoolMappings::with('grades')->where(cn::GRADES_MAPPING_SCHOOL_ID_COL,$schoolId)->get();
        $gradesListIdArr = GradeSchoolMappings::where(cn::GRADES_MAPPING_SCHOOL_ID_COL,$schoolId)->get()->pluck(cn::GRADES_MAPPING_GRADE_ID_COL)->toArray();
        $AssignedClass = GradeClassMapping::where(cn::GRADES_MAPPING_SCHOOL_ID_COL,$schoolId)->pluck(cn::GRADES_MAPPING_ID_COL)->unique()->toArray();       
        $SelfLearningTestList = MyTeachingReport::whereIn(cn::TEACHING_REPORT_GRADE_ID_COL ,$gradesListIdArr)
                                                            ->whereIn(cn::TEACHING_REPORT_CLASS_ID_COL ,$AssignedClass)
                                                            ->where([
                                                                cn::TEACHING_REPORT_REPORT_TYPE_COL => 'self_learning',
                                                                cn::TEACHING_REPORT_STUDY_TYPE_COL  =>  1,
                                                                cn::TEACHING_REPORT_SCHOOL_ID_COL   =>  $schoolId,
                                                            ])
                                                            ->with('exams','user','attempt_exams')
                                                            ->orderBy(cn::TEACHING_REPORT_EXAM_ID_COL,'DESC')->paginate($items);

        // Get Current student grade id wise strand list
        $strandsList = StrandUnitsObjectivesMappings::where([cn::OBJECTIVES_MAPPINGS_SUBJECT_ID_COL => 1])->pluck(cn::OBJECTIVES_MAPPINGS_STRAND_ID_COL);
        if($strandsList->isNotEmpty()){
            $strandsIds = array_unique($strandsList->toArray());
            $strandsList = Strands::whereIn(cn::STRANDS_ID_COL, $strandsIds)->get();

            // Get The learning units based on first Strands
            $learningUnitsIds = StrandUnitsObjectivesMappings::where(cn::OBJECTIVES_MAPPINGS_GRADE_ID_COL,Auth::user()->{cn::USERS_GRADE_ID_COL})
                        ->where(cn::OBJECTIVES_MAPPINGS_SUBJECT_ID_COL,1)
                        ->where(cn::OBJECTIVES_MAPPINGS_STRAND_ID_COL,$strandsList[0]->{cn::STRANDS_ID_COL})
                        ->pluck(cn::OBJECTIVES_MAPPINGS_LEARNING_UNIT_ID_COL);
            if(!empty($learningUnitsIds)){
                $learningUnitsIds = array_unique($learningUnitsIds->toArray());
                $LearningUnits = LearningsUnits::whereIn(cn::LEARNING_UNITS_ID_COL, $learningUnitsIds)->get();

                // Get the Learning objectives based on first learning units
                $learningObjectivesIds = StrandUnitsObjectivesMappings::where(cn::OBJECTIVES_MAPPINGS_GRADE_ID_COL,Auth::user()->{cn::USERS_GRADE_ID_COL})
                        ->where(cn::OBJECTIVES_MAPPINGS_SUBJECT_ID_COL,1)
                        ->where(cn::OBJECTIVES_MAPPINGS_STRAND_ID_COL,$strandsList[0]->{cn::STRANDS_ID_COL})
                        ->whereIn(cn::OBJECTIVES_MAPPINGS_LEARNING_UNIT_ID_COL,$LearningUnits->pluck(cn::LEARNING_UNITS_ID_COL))
                        ->pluck(cn::OBJECTIVES_MAPPINGS_LEARNING_OBJECTIVES_ID_COL);
                if(!empty($learningObjectivesIds)){
                    $learningObjectivesIds = array_unique($learningObjectivesIds->toArray());
                    // $LearningObjectives = LearningsObjectives::whereIn(cn::LEARNING_OBJECTIVES_ID_COL, $learningObjectivesIds)->get();
                    $LearningObjectives = LearningsObjectives::IsAvailableQuestion()->whereIn(cn::LEARNING_OBJECTIVES_ID_COL, $learningObjectivesIds)->get();
                }
            }
        }

        if(isset($request->filter) && !empty($request->filter)){
            $grade_id = $request->grade_id;//For Filtration Selection
            $class_type_id = $request->class_type_id;
            $gradeId = ($request->grade_id) ? $request->grade_id : $gradesListIdArr;
            $classTypeId = ($request->class_type_id) ? $request->class_type_id : $AssignedClass;
            // Create filter query object
            $SelfLearningTestList = MyTeachingReport::with('exams')->Select('*')->whereIn(cn::TEACHING_REPORT_GRADE_ID_COL , $gradeId)
                                    ->whereIn(cn::TEACHING_REPORT_CLASS_ID_COL ,$classTypeId)
                                    ->where([
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
        return view('backend/principal/self_learning_exercise',compact('SelfLearningTestList','difficultyLevels','items','schoolId','gradesList','grade_id','class_type_id','GradeClassListData','strandsList','LearningUnits','LearningObjectives'));
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
        $difficultyLevels = PreConfigurationDiffiltyLevel::all();
        $gradesList = GradeSchoolMappings::with('grades')->where(cn::GRADES_MAPPING_SCHOOL_ID_COL,$schoolId)->get();
        $gradesListIdArr = GradeSchoolMappings::where(cn::GRADES_MAPPING_SCHOOL_ID_COL,$schoolId)->get()->pluck(cn::GRADES_MAPPING_GRADE_ID_COL)->toArray();
        $AssignedClass = GradeClassMapping::where(cn::GRADE_CLASS_MAPPING_SCHOOL_ID_COL,$schoolId)->pluck(cn::GRADE_CLASS_MAPPING_ID_COL)->unique()->toArray();
        $classListIdArray =  GradeClassMapping::whereIn(cn::GRADE_CLASS_MAPPING_GRADE_ID_COL,$gradesListIdArr)
                            ->where(cn::GRADE_CLASS_MAPPING_SCHOOL_ID_COL,$schoolId)
                            ->pluck(cn::GRADE_CLASS_MAPPING_ID_COL)
                            ->toArray();

        // Find Teacher Peer Group Ids
        $TeachersPeerGroupIds = [];
        $TeachersPeerGroupIds = $this-> TeacherGradesClassService->GetSchoolBasedPeerGroupIds(Auth::user()->{cn::USERS_SCHOOL_ID_COL});
        $AssignmentTestList = MyTeachingReport::where(function($query) use($gradesListIdArr, $AssignedClass, $TeachersPeerGroupIds){
                                        $query->whereIn(cn::TEACHING_REPORT_GRADE_ID_COL ,$gradesListIdArr)
                                            ->whereIn(cn::TEACHING_REPORT_CLASS_ID_COL ,$AssignedClass)
                                            ->orWhereIn(cn::TEACHING_REPORT_PEER_GROUP_ID,$TeachersPeerGroupIds);
                                    })
                                    ->where([
                                        cn::TEACHING_REPORT_REPORT_TYPE_COL => 'assignment_test',
                                        cn::TEACHING_REPORT_STUDY_TYPE_COL  =>  2,
                                        cn::TEACHING_REPORT_SCHOOL_ID_COL   =>  $schoolId,
                                    ])
                                    ->with('exams','peerGroup')
                                    ->orderBy(cn::TEACHING_REPORT_EXAM_ID_COL,'DESC')->paginate($items);
        
        // For Filtration
        if(isset($request->filter) && !empty($request->filter)){
            $grade_id = $request->grade_id;//For Filtration Selection
            $class_type_id = $request->class_type_id;
            $gradeId = ($request->grade_id) ? $request->grade_id : $gradesListIdArr;
            $classTypeId = ($request->class_type_id) ? $request->class_type_id : $AssignedClass;
            $AssignmentTestList = MyTeachingReport::Select('*')
                                    ->with('exams','peerGroup')
                                    ->where(function($query) use($gradeId, $classTypeId, $TeachersPeerGroupIds){
                                        $query->whereIn(cn::TEACHING_REPORT_GRADE_ID_COL ,$gradeId)
                                            ->whereIn(cn::TEACHING_REPORT_CLASS_ID_COL ,$classTypeId);
                                    })
                                    ->where([
                                        cn::TEACHING_REPORT_REPORT_TYPE_COL => 'assignment_test',
                                        cn::TEACHING_REPORT_STUDY_TYPE_COL  => 2,
                                        cn::TEACHING_REPORT_SCHOOL_ID_COL   => $schoolId,
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
        $difficultyLevels = PreConfigurationDiffiltyLevel::all();

        $gradesList = GradeSchoolMappings::with('grades')->where(cn::GRADES_MAPPING_SCHOOL_ID_COL,$schoolId)->get();
        $gradesListIdArr = GradeSchoolMappings::where(cn::GRADES_MAPPING_SCHOOL_ID_COL,$schoolId)->get()->pluck(cn::GRADES_MAPPING_GRADE_ID_COL)->toArray();
        $AssignedClass = GradeClassMapping::where(cn::GRADE_CLASS_MAPPING_SCHOOL_ID_COL,$schoolId)->pluck(cn::GRADE_CLASS_MAPPING_ID_COL)->unique()->toArray();
        
        // Find Teacher Peer Group Ids
        $TeachersPeerGroupIds = [];
        $TeachersPeerGroupIds = $this-> TeacherGradesClassService->GetSchoolBasedPeerGroupIds(Auth::user()->{cn::USERS_SCHOOL_ID_COL});

        $AssignmentExerciseList = MyTeachingReport::where(function($query) use($gradesListIdArr, $AssignedClass,$TeachersPeerGroupIds){
                                                        $query->whereIn(cn::TEACHING_REPORT_GRADE_ID_COL,$gradesListIdArr)
                                                        ->whereIn(cn::TEACHING_REPORT_CLASS_ID_COL ,$AssignedClass)
                                                        ->orWhereIn(cn::TEACHING_REPORT_PEER_GROUP_ID,$TeachersPeerGroupIds);
                                                    })
                                                    ->where(cn::TEACHING_REPORT_REPORT_TYPE_COL,'assignment_test')
                                                    ->where(cn::TEACHING_REPORT_STUDY_TYPE_COL,1)
                                                    ->where(cn::TEACHING_REPORT_SCHOOL_ID_COL,$schoolId)
                                                    ->with(['exams','peerGroup'])
                                                    ->orderBy(cn::TEACHING_REPORT_EXAM_ID_COL,'DESC')->paginate($items);
        
        // For Filtration
        if(isset($request->filter) && !empty($request->filter)){
            $grade_id = $request->grade_id;//For Filtration Selection
            $class_type_id = $request->class_type_id;
            $gradeId = ($request->grade_id) ? $request->grade_id : $gradesListIdArr;
            $classTypeId = ($request->class_type_id) ? $request->class_type_id : $AssignedClass;
            $Query = MyTeachingReport::with('exams','peerGroup')->Select('*');
            $Query->whereIn(cn::TEACHING_REPORT_GRADE_ID_COL , $gradeId)
                ->whereIn(cn::TEACHING_REPORT_CLASS_ID_COL ,$classTypeId)
                ->where([
                    cn::TEACHING_REPORT_REPORT_TYPE_COL => 'assignment_test',
                    cn::TEACHING_REPORT_STUDY_TYPE_COL  =>  1,
                    cn::TEACHING_REPORT_SCHOOL_ID_COL   =>  $schoolId,
                ]);
            $AssignmentExerciseList = $Query->orderBy(cn::TEACHING_REPORT_EXAM_ID_COL,'DESC')->paginate($items);

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
        return view('backend/principal/assignment_exercise',compact('AssignmentExerciseList','difficultyLevels','items','schoolId','gradesList','grade_id','class_type_id','GradeClassListData'));
    }
}
