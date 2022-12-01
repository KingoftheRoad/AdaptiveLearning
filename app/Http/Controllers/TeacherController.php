<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Grades;
use App\Constants\DbConstant As cn;
use App\Traits\Common;
use App\Traits\ResponseFormat;
use App\Http\Repositories\UsersRepository;
use Exception;
use App\Models\PreConfigurationDiffiltyLevel;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Models\GradeClassMapping;
use App\Models\TeachersClassSubjectAssign;
use App\Models\StrandUnitsObjectivesMappings;
use App\Models\Question;
use Illuminate\Support\Facades\DB;
use App\Models\Exam;
use App\Models\Strands;
use App\Models\LearningsUnits;
use App\Models\LearningsObjectives;
use App\Models\AttemptExams;
use App\Http\Services\AIApiService;
use App\Helpers\Helper;

class TeacherController extends Controller
{
    use Common, ResponseFormat;
    protected $UsersRepository;

    public function __construct(){
        $this->UsersRepository = new UsersRepository();
        $this->AIApiService = new AIApiService();
    }

    public function index(Request $request){
        try{
            //  Laravel Pagination set in Cookie
            //$this->paginationCookie('SchoolTeacherList',$request);
            if(!in_array('teacher_management_read', Helper::getPermissions(Auth::user()->{cn::USERS_ID_COL}))) {
                return  redirect(Helper::redirectRoleBasedDashboard(Auth::user()->{cn::USERS_ID_COL}));
            }
            $items = $request->items ?? 10;
            $TotalFilterData ='';
            $countUsersData = User::all()->count();
            $gradeList = Grades::all();
            $genderList = array(
                ['id' => 'male',"name" => 'Male'],
                ['id' => 'female',"name" => 'Female'],
                ['id' => 'other',"name" => 'Other']
            );
            $UsersList = User::where(cn::USERS_SCHOOL_ID_COL,'=',auth()->user()->school_id)->where(cn::USERS_ROLE_ID_COL,'=',2)->sortable()->orderBy(cn::USERS_ID_COL,'DESC')->paginate($items);
            if(isset($request->filter)){
                $Query = User::select('*');
                $Query->where([cn::USERS_SCHOOL_ID_COL=>auth()->user()->school_id,cn::USERS_ROLE_ID_COL=>2]);
                
                //search by teachername
                if(isset($request->teachername) && !empty($request->teachername)){
                    $Query->where(cn::USERS_NAME_COL,'like','%'.$request->teachername.'%');
                }
                //search by email
                if(isset($request->email) && !empty($request->email)){
                    $Query->where(cn::USERS_EMAIL_COL,'like','%'.$request->email.'%');
                }
               
                //search by status
                if(isset($request->status) && !empty($request->status)){
                    $Query->where(cn::SUBJECTS_STATUS_COL,$request->status);
                }
                $TotalFilterData = $Query->count();
                $UsersList = $Query->sortable()->paginate($items);
                $this->StoreAuditLogFunction($request->all(),'User',cn::USERS_ID_COL,'','Teacher Details Filter',cn::USERS_TABLE_NAME,'');
            }
            return view('backend.teacher.list',compact('UsersList','genderList','gradeList','items','countUsersData','TotalFilterData')); 
        } catch (\Exception $exception) {
            return back()->withError($exception->getMessage())->withInput();
        }
    }

    public function create(){
        try {
            if(!in_array('teacher_management_create', Helper::getPermissions(Auth::user()->{cn::USERS_ID_COL}))) {
                return  redirect(Helper::redirectRoleBasedDashboard(Auth::user()->{cn::USERS_ID_COL}));
            }
            $Grades = Grades::all();
            return view('backend.teacher.add',compact('Grades'));
        } catch (\Exception $exception) {
            return back()->withError($exception->getMessage())->withInput();
        }
    }

    public function store(Request $request){
        try {
            if(!in_array('teacher_management_create', Helper::getPermissions(Auth::user()->{cn::USERS_ID_COL}))) {
                return  redirect(Helper::redirectRoleBasedDashboard(Auth::user()->{cn::USERS_ID_COL}));
            }
            // Check validation
            $validator = Validator::make($request->all(), User::rules($request, 'create'), User::rulesMessages('create'));
            if ($validator->fails()) {
                return back()->withErrors($validator)->withInput();
            }
            $school_add_in_user_table = array(
                cn::USERS_ROLE_ID_COL       => cn::TEACHER_ROLE_ID,
                cn::USERS_SCHOOL_ID_COL     => $request->school,
                cn::USERS_NAME_EN_COL       => $this->encrypt($request->name_en),
                cn::USERS_NAME_CH_COL       => $this->encrypt($request->name_ch),
                cn::USERS_EMAIL_COL         => $request->email,
                cn::USERS_ADDRESS_COL       => ($request->address) ? $this->encrypt($request->address) : null,
                cn::USERS_MOBILENO_COL      => ($request->mobile_no) ? $this->encrypt($request->mobile_no) : null,
                cn::USERS_CITY_COL          => ($request->city) ? $this->encrypt($request->city) : null,
                cn::USERS_GENDER_COL        => $request->gender ?? null,
                cn::USERS_DATE_OF_BIRTH_COL   => (!empty($request->date_of_birth) ? $this->DateConvertToYMD($request->date_of_birth) : NULL),
                cn::USERS_PASSWORD_COL      => Hash::make($request->password),
                cn::USERS_STATUS_COL        => $request->status
            );
            $this->StoreAuditLogFunction($school_add_in_user_table,'User','','','Create Teacher',cn::USERS_TABLE_NAME,'');
            $Users = User::create($school_add_in_user_table);
            if($Users){
                return redirect('teacher')->with('success_msg', __('languages.teacher_added_successfully'));
            }else{
                return back()->with('error_msg', __('languages.problem_was_occur_please_try_again'));
            }
        }catch(\Exception $exception) {
            return back()->withError($exception->getMessage())->withInput();
        }
    }

    public function edit($id){
        try{
            if(!in_array('teacher_management_update', Helper::getPermissions(Auth::user()->{cn::USERS_ID_COL}))) {
                return  redirect(Helper::redirectRoleBasedDashboard(Auth::user()->{cn::USERS_ID_COL}));
            }
            $user = User::find($id);
            return view('backend.teacher.edit',compact('user'));
        } catch (\Exception $exception) {
            return back()->withError($exception->getMessage())->withInput();
        }
    }

    public function update(Request $request, $id){
        try{
            if(!in_array('teacher_management_update', Helper::getPermissions(Auth::user()->{cn::USERS_ID_COL}))) {
                return  redirect(Helper::redirectRoleBasedDashboard(Auth::user()->{cn::USERS_ID_COL}));
            }
            $validator = Validator::make($request->all(), User::rules($request, 'update', $id), User::rulesMessages('update'));
            if ($validator->fails()) {
                return back()->withErrors($validator)->withInput();
            }
            if(User::where(cn::USERS_ID_COL,$id)->exists()){
                $school_add_in_user_table = array(
                    cn::USERS_NAME_EN_COL       => $this->encrypt($request->name_en),
                    cn::USERS_NAME_CH_COL       => $this->encrypt($request->name_ch),
                    cn::USERS_ADDRESS_COL       => ($request->address) ? $this->encrypt($request->address) : null,
                    cn::USERS_MOBILENO_COL      => ($request->mobile_no) ? $this->encrypt($request->mobile_no) : null,
                    cn::USERS_DATE_OF_BIRTH_COL   => (!empty($request->date_of_birth) ? $this->DateConvertToYMD($request->date_of_birth) : NULL),
                    cn::USERS_EMAIL_COL         => $request->email,
                    cn::USERS_GENDER_COL        => $request->gender ?? null,
                    cn::USERS_CITY_COL          => ($request->city) ? $this->encrypt($request->city) : null,
                    cn::USERS_STATUS_COL        => $request->status
                );
                $this->StoreAuditLogFunction($school_add_in_user_table,'User',cn::USERS_ID_COL,$id,'Update Teacher',cn::USERS_TABLE_NAME,'');
                $update = User::where(cn::USERS_ID_COL,$id)->update($school_add_in_user_table);
            }
            if(!empty($update)){
                return redirect('teacher')->with('success_msg', __('languages.teacher_updated_successfully'));
            }else{
                return back()->with('error_msg', __('languages.problem_was_occur_please_try_again'));
            }
        }catch(Exception $exception){
            return $this->sendError($exception->getMessage(), 404);
        }
    }

    public function destroy($id){
        try{
            if(!in_array('teacher_management_delete', Helper::getPermissions(Auth::user()->{cn::USERS_ID_COL}))) {
                return  redirect(Helper::redirectRoleBasedDashboard(Auth::user()->{cn::USERS_ID_COL}));
            }
            $this->StoreAuditLogFunction('','User','','','Delete Teacher ID '.$id,cn::USERS_TABLE_NAME,'');
            $User = User::find($id);
            if($User->delete()){
                return $this->sendResponse([], __('languages.teacher_deleted_successfully'));
            }else{
                return $this->sendError(__('languages.problem_was_occur_please_try_again'), 422);
            }
        }catch (\Exception $exception) {
            return $this->sendError($exception->getMessage(), 404);
        }
    }

    /**
     * USE : Teacher can view students assignment & test list
     */
    public function getAssignmentTestList(Request $request){
        try{
            $userId = Auth::id();
            $schoolId = Auth::user()->{cn::USERS_SCHOOL_ID_COL};
            $roleId = Auth::user()->{cn::USERS_ROLE_ID_COL};
            $ExamList = array();
            $strands_id = array();
            $learning_units_id = array();
            $learning_objectives_id = array();
            $ExamStrandList = array();
            $isFilter = 0;
            $active_tab = "";
            $grade_id = array();
            $class_type_id = array();
            $GradeClassListData = array();
            $stdata = array();
            $student_id = '';
            $gradesList = TeachersClassSubjectAssign::where([cn::TEACHER_CLASS_SUBJECT_TEACHER_ID_COL => Auth()->user()->{cn::USERS_ID_COL}])->with('getClass')->get()->unique(cn::TEACHER_CLASS_SUBJECT_CLASS_ID_COL);
            $gradesListId = TeachersClassSubjectAssign::where([cn::TEACHER_CLASS_SUBJECT_TEACHER_ID_COL => Auth()->user()->{cn::USERS_ID_COL}])
                            ->pluck(cn::TEACHER_CLASS_SUBJECT_CLASS_NAME_ID_COL,cn::TEACHER_CLASS_SUBJECT_CLASS_ID_COL)
                            ->toArray();
            $gradesListIdArr = TeachersClassSubjectAssign::where([cn::TEACHER_CLASS_SUBJECT_TEACHER_ID_COL => Auth()->user()->{cn::USERS_ID_COL}])->pluck(cn::TEACHER_CLASS_SUBJECT_CLASS_ID_COL)->toArray();
            $difficultyLevels = PreConfigurationDiffiltyLevel::all();

            $studentidlist = User::whereIn(cn::USERS_GRADE_ID_COL,$gradesListIdArr)->where(cn::USERS_SCHOOL_ID_COL,$schoolId)->where(cn::USERS_ROLE_ID_COL,cn::STUDENT_ROLE_ID)->pluck(cn::USERS_ID_COL)->toArray();
            foreach($gradesListId as $grades_key => $grades_value) {
                $GradeClassData = GradeClassMapping::where(cn::GRADE_CLASS_MAPPING_GRADE_ID_COL,$grades_key)
                                    ->where(cn::GRADE_CLASS_MAPPING_SCHOOL_ID_COL,$schoolId)
                                    ->whereIn(cn::GRADE_CLASS_MAPPING_ID_COL,explode(',', $grades_value))
                                    ->pluck(cn::GRADE_CLASS_MAPPING_NAME_COL,cn::GRADE_CLASS_MAPPING_ID_COL)
                                    ->toArray();
                foreach ($GradeClassData as $class_key => $class_value) {
                    $sexam = User::where(cn::USERS_GRADE_ID_COL,$grades_key)
                                ->where(cn::USERS_CLASS_ID_COL,$class_key)
                                ->where(cn::USERS_SCHOOL_ID_COL,$schoolId)
                                ->where(cn::USERS_ROLE_ID_COL,cn::STUDENT_ROLE_ID)
                                ->pluck(cn::USERS_ID_COL)
                                ->toArray();
                    if(isset($sexam) && !empty($sexam)){
                       $grades_class_value = $grades_key.'-'.$class_value;
                       $ExamStrandList[$grades_class_value] = $sexam;
                    }
                }
            }

            if(isset($request->active_tab) && !empty($request->active_tab)){
                $active_tab = $request->active_tab;
            }
            if(isset($request->student_id) && !empty($request->student_id)){
                $userId = $request->student_id;
                $student_id = $request->student_id;
            }
            if(isset($request->grade_id) && !empty($request->grade_id)){
                $grade_id = $request->grade_id;
                $ExamStrandList = array();
                foreach ($grade_id as $grades_key => $grades_value) {
                    $classListId = TeachersClassSubjectAssign::where([
                                cn::TEACHER_CLASS_SUBJECT_TEACHER_ID_COL => Auth()->user()->{cn::USERS_ID_COL}
                            ])
                            ->where(cn::TEACHER_CLASS_SUBJECT_CLASS_ID_COL,$grades_value)
                            ->pluck(cn::TEACHER_CLASS_SUBJECT_CLASS_NAME_ID_COL)
                            ->toArray();
                    $GradeClassListDataArr = GradeClassMapping::where(cn::GRADE_CLASS_MAPPING_GRADE_ID_COL,$grades_value)
                        ->where(cn::GRADE_CLASS_MAPPING_SCHOOL_ID_COL,$schoolId)
                        ->whereIn(cn::GRADE_CLASS_MAPPING_ID_COL,explode(',', $classListId[0]))
                        ->get()->toArray();
                    if(!empty($GradeClassListDataArr)){
                        foreach($GradeClassListDataArr as $class){
                            $sexam = User::where(cn::USERS_GRADE_ID_COL,$grades_value)->where(cn::USERS_CLASS_ID_COL,$class[cn::GRADE_CLASS_MAPPING_ID_COL])->where(cn::USERS_SCHOOL_ID_COL,$schoolId)->where(cn::USERS_ROLE_ID_COL,cn::STUDENT_ROLE_ID)->pluck(cn::USERS_ID_COL)->toArray();
                            if(isset($sexam) && !empty($sexam)){
                                $grades_class_value = $grades_value.'-'.$class[cn::GRADE_CLASS_MAPPING_NAME_COL];
                                $ExamStrandList[$grades_class_value] = $sexam;
                            }
                            $GradeList = Grades::find($class[cn::GRADE_CLASS_MAPPING_GRADE_ID_COL]);
                            $GradeClassListData[strtoupper($class[cn::GRADE_CLASS_MAPPING_ID_COL])]=$GradeList->name.strtoupper($class[cn::GRADE_CLASS_MAPPING_NAME_COL]);
                        }
                    }

                }
            }
            if(isset($request->grade_id) && !empty($request->grade_id) && isset($request->class_type_id) && !empty($request->class_type_id)){
                $ExamStrandList = array();
                foreach ($request->grade_id as $grades_key => $grades_value) {
                    $class_type_id = $request->class_type_id;
                    foreach ($class_type_id as $class_key => $class_value) {
                        $GradeClassData = GradeClassMapping::where(cn::GRADE_CLASS_MAPPING_GRADE_ID_COL,$grades_value)->where(cn::GRADE_CLASS_MAPPING_SCHOOL_ID_COL,$schoolId)->where(cn::GRADE_CLASS_MAPPING_ID_COL,$class_value)->pluck(cn::GRADE_CLASS_MAPPING_NAME_COL,cn::GRADE_CLASS_MAPPING_ID_COL)->toArray();
                        $sexam = User::where(cn::USERS_GRADE_ID_COL,$grades_value)->where(cn::USERS_CLASS_ID_COL,$class_value)->where(cn::USERS_SCHOOL_ID_COL,$schoolId)->where(cn::USERS_ROLE_ID_COL,cn::STUDENT_ROLE_ID)->pluck(cn::USERS_ID_COL)->toArray();
                        if(isset($sexam) && !empty($sexam)){
                            if(isset($GradeClassData[$class_value])){
                                $grades_class_value = $grades_value.'-'.$GradeClassData[$class_value];
                                $ExamStrandList[$grades_class_value] = $sexam;
                            }
                        }
                        
                    }
                }

                $classListId = TeachersClassSubjectAssign::where([
                                cn::TEACHER_CLASS_SUBJECT_TEACHER_ID_COL => Auth()->user()->{cn::USERS_ID_COL}
                            ])
                            ->where(cn::TEACHER_CLASS_SUBJECT_CLASS_ID_COL,$grades_value)
                            ->pluck(cn::TEACHER_CLASS_SUBJECT_CLASS_NAME_ID_COL)
                            ->toArray();
                $GradeClassListDataArr = GradeClassMapping::whereIn(cn::GRADE_CLASS_MAPPING_GRADE_ID_COL,$request->grade_id)
                    ->where(cn::GRADE_CLASS_MAPPING_SCHOOL_ID_COL,$schoolId)
                    ->whereIn(cn::GRADE_CLASS_MAPPING_ID_COL,explode(',', $classListId[0]))->get()->toArray();
                if(!empty($GradeClassListDataArr)){
                    foreach($GradeClassListDataArr as $class){
                        $GradeList = Grades::find($class[cn::GRADE_CLASS_MAPPING_GRADE_ID_COL]);
                        $GradeClassListData[strtoupper($class[cn::GRADE_CLASS_MAPPING_ID_COL])]=$GradeList->name.strtoupper($class[cn::GRADE_CLASS_MAPPING_NAME_COL]);
                    }
                }
            }

            $data = array();
            // Store students array            
            $gradeClassAvailableStudents = (!empty($ExamStrandList)) ? $ExamStrandList : [];
            foreach($ExamStrandList as $ExamStrandKey => $ExamStrandValue){
                $student_id = implode('|',$ExamStrandValue);
                $results = DB::select(DB::raw('select '.cn::EXAM_TABLE_ID_COLS.' from '.cn::EXAM_TABLE_NAME.' where CONCAT(",",'.cn::EXAM_TABLE_STUDENT_IDS_COL.',",") REGEXP ",('.$student_id.')," AND '.cn::EXAM_TABLE_IS_GROUP_TEST_COL.' = 0'));
                $exam_list = array_column($results,cn::EXAM_TABLE_ID_COLS);
                
                // Get Exercise Exams List
                $data['exerciseExam'][$ExamStrandKey] = Exam::with(['attempt_exams' => fn($query) => $query->whereIn('student_id', $ExamStrandValue)])->whereIn(cn::EXAM_TABLE_ID_COLS,$exam_list)->where(cn::EXAM_TYPE_COLS,2)->where(cn::EXAM_TABLE_IS_GROUP_TEST_COL,0)
                                                            ->where(cn::EXAM_TABLE_STATUS_COLS,'publish')
                                                            ->orderBy(cn::EXAM_TABLE_CREATED_AT,'DESC')
                                                            ->get();
                    
                // Get Test Exams List
                $data['testExam'][$ExamStrandKey] = Exam::with(['attempt_exams' => fn($query) => $query->whereIn('student_id', $ExamStrandValue)])->whereIn(cn::EXAM_TABLE_ID_COLS,$exam_list)->where(cn::EXAM_TYPE_COLS,3)->where(cn::EXAM_TABLE_IS_GROUP_TEST_COL,0)
                                                        ->where(cn::EXAM_TABLE_STATUS_COLS,'publish')
                                                        ->orderBy(cn::EXAM_TABLE_CREATED_AT,'DESC')
                                                        ->get();
            }
            return view('backend/teacher/assignment_tests_list',compact('gradeClassAvailableStudents','difficultyLevels','data','active_tab','gradesList','schoolId','grade_id','stdata','student_id','userId','roleId','GradeClassListData','class_type_id'));
        }catch(Exception $exception){
            return back()->withError($exception->getMessage());
        }
    }

    /**
     * USE : Teacher can view students self-learning test list
     */
    public function getSelfLearningList(Request $request){
        try{
            $userId = Auth::id();
            $schoolId = Auth::user()->{cn::USERS_SCHOOL_ID_COL};
            $roleId = Auth::user()->{cn::USERS_ROLE_ID_COL};
            $ExamList = array();
            $strands_id = array();
            $learning_units_id = array();
            $learning_objectives_id = array();
            $ExamStrandList = array();
            $isFilter = 0;
            $active_tab = "";
            $grade_id = array();
            $class_type_id = array();
            $GradeClassListData = array();
            $stdata = array();
            $student_id = '';
            $strandsList = array();
            $LearningUnits = array();
            $LearningObjectives = array();
            $gradesList = TeachersClassSubjectAssign::with('getTeacher')->with('getClass')->where([cn::TEACHER_CLASS_SUBJECT_TEACHER_ID_COL => Auth()->user()->{cn::USERS_ID_COL}])->get()->unique(cn::TEACHER_CLASS_SUBJECT_CLASS_ID_COL);
            $gradesListId = TeachersClassSubjectAssign::where([cn::TEACHER_CLASS_SUBJECT_TEACHER_ID_COL => Auth()->user()->{cn::USERS_ID_COL}])->pluck(cn::TEACHER_CLASS_SUBJECT_CLASS_NAME_ID_COL,cn::TEACHER_CLASS_SUBJECT_CLASS_ID_COL)->toArray();
            $gradesListIdArr = TeachersClassSubjectAssign::where([cn::TEACHER_CLASS_SUBJECT_TEACHER_ID_COL => Auth()->user()->{cn::USERS_ID_COL}])->pluck(cn::TEACHER_CLASS_SUBJECT_CLASS_ID_COL)->toArray();
            $difficultyLevels = PreConfigurationDiffiltyLevel::all();

            $studentidlist = User::whereIn(cn::USERS_GRADE_ID_COL,$gradesListIdArr)->where(cn::USERS_SCHOOL_ID_COL,$schoolId)->where(cn::USERS_ROLE_ID_COL,cn::STUDENT_ROLE_ID)->pluck(cn::USERS_ID_COL)->toArray();
            foreach ($gradesListId as $grades_key => $grades_value) {
                $GradeClassData = GradeClassMapping::where(cn::GRADE_CLASS_MAPPING_GRADE_ID_COL,$grades_key)->where(cn::GRADE_CLASS_MAPPING_SCHOOL_ID_COL,$schoolId)->whereIn(cn::GRADE_CLASS_MAPPING_ID_COL,explode(',', $grades_value))->pluck(cn::GRADE_CLASS_MAPPING_NAME_COL,cn::GRADE_CLASS_MAPPING_ID_COL)->toArray();
                foreach ($GradeClassData as $class_key => $class_value) {
                    $sexam = User::where(cn::USERS_GRADE_ID_COL,$grades_key)->where(cn::USERS_CLASS_ID_COL,$class_key)->where(cn::USERS_SCHOOL_ID_COL,$schoolId)->where(cn::USERS_ROLE_ID_COL,cn::STUDENT_ROLE_ID)->pluck(cn::USERS_ID_COL)->toArray();
                    if(isset($sexam) && !empty($sexam)){
                       $grades_class_value = $grades_key.'-'.$class_value;
                       $ExamStrandList[$grades_class_value] = $sexam;
                    }
                }
            }
            if(isset($request->active_tab) && !empty($request->active_tab)){
                $active_tab = $request->active_tab;
            }
            if(isset($request->student_id) && !empty($request->student_id)){
                $userId = $request->student_id;
                $student_id = $request->student_id;
            }
            if(isset($request->grade_id) && !empty($request->grade_id)){
                $grade_id = $request->grade_id;
                $ExamStrandList = array();
                foreach ($grade_id as $grades_key => $grades_value) {
                    $classListId = TeachersClassSubjectAssign::where([
                                cn::TEACHER_CLASS_SUBJECT_TEACHER_ID_COL => Auth()->user()->{cn::USERS_ID_COL}
                            ])
                            ->where(cn::TEACHER_CLASS_SUBJECT_CLASS_ID_COL,$grades_value)
                            ->pluck(cn::TEACHER_CLASS_SUBJECT_CLASS_NAME_ID_COL)
                            ->toArray();
                    $GradeClassListDataArr = GradeClassMapping::where(cn::GRADE_CLASS_MAPPING_GRADE_ID_COL,$grades_value)
                        ->where(cn::GRADE_CLASS_MAPPING_SCHOOL_ID_COL,$schoolId)
                        ->whereIn(cn::GRADE_CLASS_MAPPING_ID_COL,explode(',', $classListId[0]))
                        ->get()->toArray();
                    if(!empty($GradeClassListDataArr)){
                        foreach($GradeClassListDataArr as $class){
                            $sexam = User::where(cn::USERS_GRADE_ID_COL,$grades_value)->where(cn::USERS_CLASS_ID_COL,$class[cn::GRADE_CLASS_MAPPING_ID_COL])->where(cn::USERS_SCHOOL_ID_COL,$schoolId)->where(cn::USERS_ROLE_ID_COL,cn::STUDENT_ROLE_ID)->pluck(cn::USERS_ID_COL)->toArray();
                            if(isset($sexam) && !empty($sexam)){
                                $grades_class_value = $grades_value.'-'.$class[cn::GRADE_CLASS_MAPPING_NAME_COL];
                                $ExamStrandList[$grades_class_value] = $sexam;
                            }
                            $GradeList = Grades::find($class[cn::GRADE_CLASS_MAPPING_GRADE_ID_COL]);
                            $GradeClassListData[strtoupper($class[cn::GRADE_CLASS_MAPPING_ID_COL])]=$GradeList->name.strtoupper($class[cn::GRADE_CLASS_MAPPING_NAME_COL]);
                        }
                    }
                }
            }
            if(isset($request->grade_id) && !empty($request->grade_id) && isset($request->class_type_id) && !empty($request->class_type_id)){
                $ExamStrandList = array();
                foreach ($request->grade_id as $grades_key => $grades_value) {
                    $class_type_id = $request->class_type_id;
                    foreach ($class_type_id as $class_key => $class_value) {
                        $GradeClassData = GradeClassMapping::where(cn::GRADE_CLASS_MAPPING_GRADE_ID_COL,$grades_value)->where(cn::GRADE_CLASS_MAPPING_SCHOOL_ID_COL,$schoolId)->where(cn::GRADE_CLASS_MAPPING_ID_COL,$class_value)->pluck(cn::GRADE_CLASS_MAPPING_NAME_COL,cn::GRADE_CLASS_MAPPING_ID_COL)->toArray();
                        $sexam = User::where(cn::USERS_GRADE_ID_COL,$grades_value)->where(cn::USERS_CLASS_ID_COL,$class_value)->where(cn::USERS_SCHOOL_ID_COL,$schoolId)->where(cn::USERS_ROLE_ID_COL,cn::STUDENT_ROLE_ID)->pluck(cn::USERS_ID_COL)->toArray();
                        if(isset($sexam) && !empty($sexam)){
                            if(isset($GradeClassData[$class_value])){
                                $grades_class_value = $grades_value.'-'.$GradeClassData[$class_value];
                                $ExamStrandList[$grades_class_value] = $sexam;
                            }
                        }
                    }
                }
                $classListId = TeachersClassSubjectAssign::where([
                            cn::TEACHER_CLASS_SUBJECT_TEACHER_ID_COL => Auth()->user()->{cn::USERS_ID_COL}
                        ])
                        ->where(cn::TEACHER_CLASS_SUBJECT_CLASS_ID_COL,$grades_value)
                        ->pluck(cn::TEACHER_CLASS_SUBJECT_CLASS_NAME_ID_COL)
                        ->toArray();
                $GradeClassListDataArr = GradeClassMapping::whereIn(cn::GRADE_CLASS_MAPPING_GRADE_ID_COL,$request->grade_id)
                    ->where(cn::GRADE_CLASS_MAPPING_SCHOOL_ID_COL,$schoolId)
                    ->whereIn(cn::GRADE_CLASS_MAPPING_ID_COL,explode(',', $classListId[0]))->get()->toArray();
                if(!empty($GradeClassListDataArr)){
                    foreach($GradeClassListDataArr as $class){
                        $GradeList = Grades::find($class[cn::GRADE_CLASS_MAPPING_GRADE_ID_COL]);
                        $GradeClassListData[strtoupper($class[cn::GRADE_CLASS_MAPPING_ID_COL])]=$GradeList->name.strtoupper($class[cn::GRADE_CLASS_MAPPING_NAME_COL]);
                    }
                }
            }
            // Filter using Strands options
            // if(isset($request->strands) && !empty($request->strands)){
            //     if(!is_array($request->strands)){
            //         $strands_id = json_decode($request->strands);
            //     }else{
            //         $strands_id = $request->strands;
            //     }
            //     $isFilter = 1;
            // }
            // Filter using Learning Units options
            // if(isset($request->learning_units) && !empty($request->learning_units)){
            //     if(!is_array($request->learning_units)){
            //         $learning_units_id = json_decode($request->learning_units);
            //     }else{
            //         $learning_units_id = $request->learning_units;
            //     }
            //     $isFilter = 1;
            // }

            // Filetr using Learning Objectives Focus
            // if(isset($request->learning_objectives_id) && !empty($request->learning_objectives_id)){
            //     if(!is_array($request->learning_objectives_id)){
            //         $learning_objectives_id = json_decode($request->learning_objectives_id);
            //     }else{
            //         $learning_objectives_id = $request->learning_objectives_id;
            //     }
            //     $isFilter = 1;
                
            // }

            // Searching Using StrandsLearningUnitsLearningObjectives mapping Idsfor selected filter options
            // $StrandUnitsObjectivesMappings = StrandUnitsObjectivesMappings::where(function ($query) use ($strands_id,$learning_units_id,$learning_objectives_id) {
            //     if(!empty($strands_id)){
            //         $query->whereIn(cn::OBJECTIVES_MAPPINGS_STRAND_ID_COL,$strands_id);
            //     }
            //     if(!empty($learning_units_id)){
            //         $query->whereIn(cn::OBJECTIVES_MAPPINGS_LEARNING_UNIT_ID_COL,$learning_units_id);
            //     }
            //     if(!empty($learning_objectives_id)){
            //         $query->whereIn(cn::OBJECTIVES_MAPPINGS_LEARNING_OBJECTIVES_ID_COL,$learning_objectives_id);
            //     }
            // })->get()->toArray();

            // if(isset($StrandUnitsObjectivesMappings) && !empty($StrandUnitsObjectivesMappings) && $isFilter == 1){
            //     $StrandUnitsObjectivesMappingsId = array_column($StrandUnitsObjectivesMappings,cn::OBJECTIVES_MAPPINGS_ID_COL);
            //     $QuestionsList = Question::with('answers')->whereIn(cn::QUESTION_OBJECTIVE_MAPPING_ID_COL,$StrandUnitsObjectivesMappingsId)->orderBy(cn::QUESTION_TABLE_ID_COL)->get()->toArray();
            //     if(isset($QuestionsList) && !empty($QuestionsList)){
            //         $QuestionsDataList = array_column($QuestionsList,cn::QUESTION_TABLE_ID_COL);
            //         $ExamList = Exam::with('attempt_exams')->whereIn(cn::EXAM_TABLE_QUESTION_IDS_COL,$QuestionsDataList)->get()->toArray();
            //         if(isset($ExamList) && !empty($ExamList)){
            //             $ExamList = array_column($ExamList,cn::EXAM_TABLE_ID_COLS);
            //         }
            //     }
            // }
            $ExamsData = array();
            $GroupTestData = [];

            foreach($ExamStrandList as $ExamStrandKey => $ExamStrandValue){
                $student_id = implode('|',$ExamStrandValue);
                $results = DB::select(DB::raw('select '.cn::EXAM_TABLE_ID_COLS.' from '.cn::EXAM_TABLE_NAME.' where CONCAT(",",'.cn::EXAM_TABLE_STUDENT_IDS_COL.',",") REGEXP ",('.$student_id.')," AND '.cn::EXAM_TABLE_IS_GROUP_TEST_COL.' = 0'));
                $exam_list = array_column($results,cn::EXAM_TABLE_ID_COLS);
                // Get Self Learning excercise List
                $ExamsData['excercise_list'][$ExamStrandKey] = Exam::with('attempt_exams','user')
                                                                ->whereIn(cn::EXAM_TABLE_ID_COLS,$exam_list)
                                                                ->where(cn::EXAM_TYPE_COLS,1)
                                                                ->where(cn::EXAM_TABLE_IS_GROUP_TEST_COL,0)
                                                                ->where('self_learning_test_type',1)
                                                                // ->where(function ($query) use ($learning_objectives_id,$ExamList){
                                                                //     if(!empty($learning_objectives_id)){
                                                                //         $query->whereIn(cn::EXAM_TABLE_ID_COLS,$ExamList);
                                                                //     }
                                                                // })
                                                                ->where(cn::EXAM_TABLE_STATUS_COLS,'publish')
                                                                ->orderBy(cn::EXAM_TABLE_CREATED_AT,'DESC')
                                                                ->get();
                // Get Self Learning excercise List
                $ExamsData['test_list'][$ExamStrandKey] = Exam::with('attempt_exams','user')
                                                                ->whereIn(cn::EXAM_TABLE_ID_COLS,$exam_list)
                                                                ->where(cn::EXAM_TYPE_COLS,1)
                                                                ->where(cn::EXAM_TABLE_IS_GROUP_TEST_COL,0)
                                                                ->where('self_learning_test_type',2)
                                                                // ->where(function ($query) use ($learning_objectives_id,$ExamList){
                                                                //     if(!empty($learning_objectives_id)){
                                                                //         $query->whereIn(cn::EXAM_TABLE_ID_COLS,$ExamList);
                                                                //     }
                                                                // })
                                                                ->where(cn::EXAM_TABLE_STATUS_COLS,'publish')
                                                                ->orderBy(cn::EXAM_TABLE_CREATED_AT,'DESC')
                                                                ->get();
            }
            //$studyFocusTreeOption = $this->getSubjectMapping($strands_id,$learning_units_id,$learning_objectives_id);
            // Get Current student grade id wise strand list
            $strandsList = StrandUnitsObjectivesMappings::where([cn::OBJECTIVES_MAPPINGS_SUBJECT_ID_COL => 1])->pluck(cn::OBJECTIVES_MAPPINGS_STRAND_ID_COL);
            if($strandsList->isNotEmpty()){
                $strandsIds = array_unique($strandsList->toArray());
                $strandsList = Strands::whereIn(cn::STRANDS_ID_COL, $strandsIds)->get();

                // Get The learning units based on first Strands
                $learningUnitsIds = StrandUnitsObjectivesMappings::where(cn::OBJECTIVES_MAPPINGS_GRADE_ID_COL,Auth::user()->{cn::USERS_GRADE_ID_COL})
                            ->where(cn::OBJECTIVES_MAPPINGS_SUBJECT_ID_COL,1)
                            ->where(cn::OBJECTIVES_MAPPINGS_STRAND_ID_COL,$strandsList[0]->id)
                            ->pluck(cn::OBJECTIVES_MAPPINGS_LEARNING_UNIT_ID_COL);
                if(!empty($learningUnitsIds)){
                    $learningUnitsIds = array_unique($learningUnitsIds->toArray());
                    $LearningUnits = LearningsUnits::whereIn(cn::LEARNING_UNITS_ID_COL, $learningUnitsIds)->get();

                    // Get the Learning objectives based on first learning units
                    $learningObjectivesIds = StrandUnitsObjectivesMappings::where(cn::OBJECTIVES_MAPPINGS_GRADE_ID_COL,Auth::user()->{cn::USERS_GRADE_ID_COL})
                            ->where(cn::OBJECTIVES_MAPPINGS_SUBJECT_ID_COL,1)
                            ->where(cn::OBJECTIVES_MAPPINGS_STRAND_ID_COL,$strandsList[0]->id)
                            ->whereIn(cn::OBJECTIVES_MAPPINGS_LEARNING_UNIT_ID_COL,$LearningUnits->pluck('id'))
                            ->pluck(cn::OBJECTIVES_MAPPINGS_LEARNING_OBJECTIVES_ID_COL);
                    if(!empty($learningObjectivesIds)){
                        $learningObjectivesIds = array_unique($learningObjectivesIds->toArray());
                        // $LearningObjectives = LearningsObjectives::whereIn(cn::LEARNING_OBJECTIVES_ID_COL, $learningObjectivesIds)->get();
                        $LearningObjectives = LearningsObjectives::IsAvailableQuestion()->whereIn(cn::LEARNING_OBJECTIVES_ID_COL, $learningObjectivesIds)->get();
                    }
                }
            }
            //return view('backend/teacher/myteaching_selflearning_list',compact('difficultyLevels','ExamsData','studyFocusTreeOption','strands_id','learning_units_id','learning_objectives_id','active_tab','gradesList','schoolId','grade_id','stdata','student_id','userId','roleId','GradeClassListData','class_type_id','studentidlist','strandsList','LearningUnits','LearningObjectives'));
            return view('backend/teacher/myteaching_selflearning_list',compact('difficultyLevels','ExamsData','strands_id','learning_units_id','learning_objectives_id','active_tab','gradesList','schoolId','grade_id','stdata','student_id','userId','roleId','GradeClassListData','class_type_id','studentidlist','strandsList','LearningUnits','LearningObjectives'));
        }catch(Exception $exception){
            return back()->withError($exception->getMessage());
        }
    }

    /**
     * USE : Display Student attempt exam or not
     */
    public function StudentProgressReport(Request $request){
        try{
            $dataTable = '';
            $schoolId = Auth::user()->{cn::USERS_SCHOOL_ID_COL};
            if(isset($request->examid) && !empty($request->examid) && isset($request->studentIds) && !empty($request->studentIds)){
                $studentIds = explode(',',$request->studentIds);
                foreach($studentIds as $studentId){
                    // Get correct answer detail
                    $User = User::find($studentId);
                    $AttemptExamData = AttemptExams::where('student_id',$studentId)->where('exam_id',$request->examid)->first();
                    if(isset($User) && !empty($User)){
                        $u_name = $this->decrypt($User->name_en);
                        $classStudentNumber = ($User->class_student_number) ?? 'N/A';
                        if(app()->getLocale() == 'ch'){
                            $u_name = mb_convert_encoding($this->decrypt($User->name_ch), 'UTF-8', 'UTF-8');
                        }
                        if(isset($AttemptExamData) && !empty($AttemptExamData)){
                            $dataTable.='<tr><td>'.$u_name.'</td><td>'.$classStudentNumber.'</td><td>'.$User->email.'</td><td><span class="badge badge-success">Complete</span></td></tr>';
                        }else{
                            $dataTable.='<tr><td>'.$u_name.'</td><td>'.$classStudentNumber.'</td><td>'.$User->email.'</td><td><span class="badge badge-warning">Pending</span></td></tr>';
                        }
                    }
                }
                return $this->sendResponse($dataTable);
            }
        }catch(Exception $exception){
            return $this->sendError($exception->getMessage(), 404);
        }
    }
}
