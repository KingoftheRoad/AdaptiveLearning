<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;   
use App\Traits\Common;
use App\Models\GradeSchoolMappings;
use App\Models\GradeClassMapping;
use App\Models\ClassPromotionHistory;
use App\Constants\DbConstant As cn;
use Illuminate\Support\Facades\Hash;
use Exception;
use Illuminate\Support\Facades\Validator;
use App\Models\Grades;
use App\Models\Exam;
use App\Models\PreConfigurationDiffiltyLevel;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Traits\ResponseFormat;
use App\Models\StrandUnitsObjectivesMappings;
use App\Models\Question;
use App\Models\TeachersClassSubjectAssign;
use App\Models\Strands;
use App\Models\LearningsUnits;
use App\Models\LearningsObjectives;
use App\Models\ExamConfigurationsDetails;
use App\Models\CurriculumYearStudentMappings;
use App\Http\Services\AIApiService;
use App\Helpers\Helper;
use DB;
use App\Models\ExamSchoolMapping;

class StudentController extends Controller
{
    use common, ResponseFormat;
    protected $currentUserSchoolId;
    protected $DefaultStudentOverAllAbility;
    protected $CurrentCurriculumYearId;
    
    public function __construct(){
        $this->AIApiService = new AIApiService();
        $this->CurrentCurriculumYearId = $this->getGlobalConfiguration('current_curriculum_year');

        // Store global variable into current user schhol id
        $this->currentUserSchoolId = null;
        $this->DefaultStudentOverAllAbility = 0.1;
        $this->middleware(function ($request, $next) {
            $this->currentUserSchoolId = Auth::user()->{cn::USERS_SCHOOL_ID_COL};
            return $next($request);
        });
    }

    public function index(Request $request){
         try{
            //  Laravel Pagination set in Cookie
            //$this->paginationCookie('SchoolStudentList',$request);
            if(!in_array('student_management_read', Helper::getPermissions(Auth::user()->{cn::USERS_ID_COL}))) {
                return  redirect(Helper::redirectRoleBasedDashboard(Auth::user()->{cn::USERS_ID_COL}));
            }
            $classTypeOptions = '';
            $items = $request->items ?? 10;
            $TotalFilterData = '';
            $gradeList = GradeSchoolMappings::with('grades')->where([
                            cn::GRADES_MAPPING_SCHOOL_ID_COL => Auth::user()->school_id,
                            cn::GRADES_MAPPING_CURRICULUM_YEAR_ID_COL => $this->GetCurriculumYear()
                        ])
                        ->get();
            $countUserData = User::where([
                                cn::USERS_SCHOOL_ID_COL => auth()->user()->school_id,
                                cn::USERS_ROLE_ID_COL => cn::STUDENT_ROLE_ID,
                            ])->count();
            $UsersList = User::where([
                            cn::USERS_SCHOOL_ID_COL => auth()->user()->school_id,
                            cn::USERS_ROLE_ID_COL => cn::STUDENT_ROLE_ID,
                        ])
                        ->whereIn('id',$this->curriculum_year_mapping_student_ids('','',auth()->user()->school_id))
                        ->sortable()
                        ->orderBy(cn::USERS_ID_COL,'DESC')
                        ->paginate($items);

            $GradeClassMapping = GradeClassMapping::where([
                                    cn::GRADE_CLASS_MAPPING_GRADE_ID_COL => $request->student_grade_id,
                                    cn::GRADE_CLASS_MAPPING_SCHOOL_ID_COL => $this->isSchoolLogin()
                                ])->get();
            
            $Query = User::select('*');
            if(isset($request->filter_data)){
                if(isset($request->search) && !empty($request->search)){
                    $Query->where(function($q) use ($Query, $request){
                        $q->Where(cn::USERS_NAME_EN_COL,'Like','%'.$this->encrypt($request->search).'%')
                        ->orWhere(cn::USERS_NAME_CH_COL,'Like','%'.$this->encrypt($request->search).'%')
                        ->orWhere(cn::USERS_EMAIL_COL,'Like','%'.$request->search.'%');
                    });
                }
                if(isset($request->student_grade_id) && !empty($request->student_grade_id) && isset($request->class_type_id) && !empty($request->class_type_id)){
                    $Query->where(cn::USERS_GRADE_ID_COL,$request->student_grade_id)
                    ->whereIn(cn::USERS_CLASS_ID_COL,$request->class_type_id)
                    ->whereIn('id',$this->curriculum_year_mapping_student_ids($request->student_grade_id,$request->class_type_id,auth()->user()->school_id));
                }
                if(isset($request->student_grade_id) && !empty($request->student_grade_id)){
                    $Query->where(cn::USERS_GRADE_ID_COL,$request->student_grade_id)
                    ->whereIn('id',$this->curriculum_year_mapping_student_ids($request->student_grade_id,'',auth()->user()->school_id));
                }
                if(isset($request->classStudentNumber) && !empty($request->classStudentNumber)){
                    $Query->where(cn::USERS_CLASS_CLASS_STUDENT_NUMBER,$request->classStudentNumber);
                } 
                if(isset($request->status)){
                    $Query->where(cn::USERS_STATUS_COL,$request->status);
                }
                if(!empty($GradeClassMapping)){
                    foreach($GradeClassMapping as $class){
                        if(!empty($request->class_type_id) && in_array($class->id, $request->class_type_id)){
                                $classTypeOptions .= '<option value='.strtoupper($class->id).' selected>'.strtoupper($class->name).'</option>';
                        }else{
                            $classTypeOptions .= '<option value='.strtoupper($class->id).'>'.strtoupper($class->name).'</option>';
                        }                   
                    }
                }
                $TotalFilterData = $Query->where([cn::USERS_SCHOOL_ID_COL => auth()->user()->school_id,cn::USERS_ROLE_ID_COL => cn::STUDENT_ROLE_ID])->count();
                $UsersList = $Query->where([cn::USERS_SCHOOL_ID_COL => auth()->user()->school_id,cn::USERS_ROLE_ID_COL => cn::STUDENT_ROLE_ID])
                            ->whereIn('id',$this->curriculum_year_mapping_student_ids('','',auth()->user()->school_id))
                            ->sortable()->paginate($items);
                $this->StoreAuditLogFunction($request->all(),'User',cn::USERS_ID_COL,'','Student Details Filter',cn::USERS_TABLE_NAME,'');
            }
            //echo '<pre>';print_r($UsersList->toArray());die;
            return view('backend.studentmanagement.list',compact('UsersList','items','countUserData','gradeList','TotalFilterData','classTypeOptions'));
        }catch(Exception $exception){
            return redirect('Student')->withError($exception->getMessage())->withInput();
        }        
    }

    public function create(){
        try{
            if(!in_array('student_management_create', Helper::getPermissions(Auth::user()->{cn::USERS_ID_COL}))) {
                return  redirect(Helper::redirectRoleBasedDashboard(Auth::user()->{cn::USERS_ID_COL}));
            }
            $gradeData = GradeSchoolMappings::where([cn::GRADES_MAPPING_SCHOOL_ID_COL=>Auth::user()->{cn::USERS_SCHOOL_ID_COL}])->pluck(cn::GRADES_MAPPING_GRADE_ID_COL)->toArray();
            $grades = Grades::whereIn(cn::GRADES_ID_COL,$gradeData)->get();
            return view('backend.studentmanagement.add',compact('grades')); 
        } catch (\Exception $exception) {
            return redirect('Student')->withError($exception->getMessage())->withInput();
        }
    }
    
    public function store(Request $request){
        try {
            if(!in_array('student_management_create', Helper::getPermissions(Auth::user()->{cn::USERS_ID_COL}))) {
                return  redirect(Helper::redirectRoleBasedDashboard(Auth::user()->{cn::USERS_ID_COL}));
            }
            $validator = Validator::make($request->all(), User::rules($request, 'create'), User::rulesMessages('create'));
            if ($validator->fails()) {
                return back()->withErrors($validator)->withInput();
            }

            $Grades = Grades::where(cn::GRADES_NAME_COL,$request->grade_id)->first();
            if(empty($Grades->id)){
                return back()->withInput()->with('error_msg', __('languages.grade_not_available'));
            }

            // Get class type list
            $classData = GradeClassMapping::where([
                            cn::GRADE_CLASS_MAPPING_CURRICULUM_YEAR_ID_COL => $this->GetCurriculumYear(),
                            cn::GRADE_CLASS_MAPPING_SCHOOL_ID_COL => $this->currentUserSchoolId,
                            cn::GRADE_CLASS_MAPPING_GRADE_ID_COL => $Grades->id,
                            cn::GRADE_CLASS_MAPPING_ID_COL => $request->class_id
                        ])->first();
            if(empty($classData->id)){
                return back()->withInput()->with('error_msg', __('languages.class_not_available'));
            }
            if(User::where([cn::USERS_SCHOOL_ID_COL => Auth::user()->{cn::USERS_SCHOOL_ID_COL},cn::USERS_PERMANENT_REFERENCE_NUMBER => $request->permanent_refrence_number,cn::USERS_ROLE_ID_COL => 3])->exists()){
                return back()->withInput()->with('error_msg', __('languages.permanent_reference_already_exists'));
            }
            // Store user detail
            $PostData = array(
                cn::USERS_CURRICULUM_YEAR_ID_COL    => $this->GetCurriculumYear(),
                cn::USERS_ROLE_ID_COL               => cn::STUDENT_ROLE_ID,
                cn::USERS_GRADE_ID_COL              => $Grades->id,
                cn::USERS_SCHOOL_ID_COL             => auth()->user()->school_id,
                cn::STUDENT_NUMBER_WITHIN_CLASS     => $request->student_number,
                cn::USERS_PERMANENT_REFERENCE_NUMBER => $request->permanent_refrence_number,
                cn::USERS_CLASS                     => $Grades->name.$classData->name,
                cn::USERS_CLASS_ID_COL                 => $classData->id,
                cn::USERS_NAME_EN_COL               =>  $this->encrypt($request->name_en),
                cn::USERS_NAME_CH_COL               => $this->encrypt($request->name_ch),
                cn::USERS_EMAIL_COL                 => $request->email,
                cn::USERS_MOBILENO_COL              => ($request->mobile_no) ? $this->encrypt($request->mobile_no) : null,
                cn::USERS_ADDRESS_COL               => ($request->address) ? $this->encrypt($request->address) : null,
                cn::USERS_GENDER_COL                => $request->gender ?? null,
                cn::USERS_CITY_COL                  => ($request->city) ? $this->encrypt($request->city) : null,
                cn::USERS_DATE_OF_BIRTH_COL         => ($request->date_of_birth) ? $this->DateConvertToYMD($request->date_of_birth) : null,                
                cn::USERS_PASSWORD_COL              => Hash::make($request->password),
                cn::USERS_STATUS_COL                => $request->status ?? 'active',
                cn::USERS_CREATED_BY_COL            => auth()->user()->id,
            );
            if(User::where([cn::USERS_CLASS_STUDENT_NUMBER => $Grades->name.$classData->name.$request->student_number,cn::USERS_ROLE_ID_COL => cn::STUDENT_ROLE_ID,cn::USERS_SCHOOL_ID_COL => Auth::user()->{cn::USERS_SCHOOL_ID_COL}])->doesntExist()){
                $PostData += ([cn::USERS_CLASS_STUDENT_NUMBER      =>  $Grades->name.$classData->name.$request->student_number,]);
            }else{
                return back()->with('error_msg', __('languages.duplicate_class_student_number'));
            }
            $Users = User::create($PostData);
            if($Users){
            $this->StoreAuditLogFunction($PostData,'User',cn::USERS_ID_COL,'','Create Student',cn::USERS_TABLE_NAME,'');
            //in student curriculum year table in side user not exist then create record.
                if(!(CurriculumYearStudentMappings::where(cn::CURRICULUM_YEAR_STUDENT_MAPPING_USER_ID_COL,$Users->id)->exists())){
                    $curriculumStudentMapping = CurriculumYearStudentMappings::create([
                        cn::CURRICULUM_YEAR_STUDENT_MAPPING_CURRICULUM_YEAR_ID_COL => $this->GetCurriculumYear(),
                        cn::CURRICULUM_YEAR_STUDENT_MAPPING_USER_ID_COL => $Users->id,
                        cn::CURRICULUM_YEAR_STUDENT_MAPPING_SCHOOL_ID_COL => Auth::user()->school_id,
                        cn::CURRICULUM_YEAR_STUDENT_MAPPING_GRADE_ID_COL => $Grades->id ?? null,
                        cn::CURRICULUM_YEAR_STUDENT_MAPPING_CLASS_ID_COL => $classData->id ?? null,
                        cn::CURRICULUM_YEAR_STUDENT_NUMBER_WITHIN_CLASS_COL => $Users->student_number_within_class ?? null,
                        cn::CURRICULUM_YEAR_STUDENT_CLASS => $Grades->name.$classData->name ?? null,
                        cn::CURRICULUM_YEAR_CLASS_STUDENT_NUMBER => $Users->class_student_number ?? null,
                        cn::CURRICULUM_YEAR_STUDENT_MAPPING_STATUS_COL => $Users->status 
                    ]);
                }
                return redirect('Student')->with('success_msg', __('languages.student_added_successfully'));
            }else{
                return back()->with('error_msg', __('languages.problem_was_occur_please_try_again'));
            }
        } catch (\Exception $exception) {
            return back()->withError($exception->getMessage())->withInput();
        }
    }

    public function edit($id){
        try{
            if(!in_array('student_management_update', Helper::getPermissions(Auth::user()->{cn::USERS_ID_COL}))) {
                return  redirect(Helper::redirectRoleBasedDashboard(Auth::user()->{cn::USERS_ID_COL}));
            }
            $gradeData = GradeSchoolMappings::where([
                            cn::GRADES_MAPPING_CURRICULUM_YEAR_ID_COL => $this->GetCurriculumYear(),
                            cn::GRADES_MAPPING_SCHOOL_ID_COL => Auth::user()->{cn::USERS_SCHOOL_ID_COL}
                        ])
                        ->pluck(cn::GRADES_MAPPING_GRADE_ID_COL)->toArray();
            $grades = Grades::whereIn(cn::GRADES_ID_COL,$gradeData)->get();
            $user = User::find($id);
            return view('backend.studentmanagement.edit',compact('user','grades'));
        }catch(\Exception $exception){
            return back()->withError($exception->getMessage())->withInput();
        }
    }

    public function update(Request $request, $id){
        try{
            $classData = array();
            if(!in_array('student_management_update', Helper::getPermissions(Auth::user()->{cn::USERS_ID_COL}))) {
                return  redirect(Helper::redirectRoleBasedDashboard(Auth::user()->{cn::USERS_ID_COL}));
            }
            // Check validation
            $validator = Validator::make($request->all(), User::rules($request, 'update', $id), User::rulesMessages('update'));
            if ($validator->fails()) {
                return back()->withErrors($validator)->withInput();
            }
            // $classNumber = explode('+',$request->class_number);
            $userData = User::find($id);

            //$gradeid = ($userData->grade_id != '') ? $userData->grade_id : 4;
            $gradeid = ($userData->CurriculumYearGradeId != '') ? $userData->CurriculumYearGradeId : 4;
            $Grades = Grades::where(cn::GRADES_NAME_COL,$gradeid)->first();
            
            // Get Class List
            if($this->currentUserSchoolId){
                $classData = GradeClassMapping::where([
                                cn::GRADE_CLASS_MAPPING_CURRICULUM_YEAR_ID_COL  => $this->GetCurriculumYear(),
                                cn::GRADE_CLASS_MAPPING_SCHOOL_ID_COL           => $this->currentUserSchoolId,
                                cn::GRADE_CLASS_MAPPING_GRADE_ID_COL            => $gradeid,
                                cn::GRADE_CLASS_MAPPING_ID_COL                  => $request->class_id
                            ])->first();
            }
            if(empty($classData->id)){
                return back()->with('error_msg', __('languages.class_not_available'));
            }
            if(User::where([
                cn::USERS_SCHOOL_ID_COL                 => Auth::user()->{cn::USERS_SCHOOL_ID_COL},
                cn::USERS_PERMANENT_REFERENCE_NUMBER    => $request->permanent_refrence_number,
                cn::USERS_ROLE_ID_COL                   => cn::STUDENT_ROLE_ID
            ])
            ->where(cn::USERS_ID_COL,'!=' ,$userData->id)
            ->exists()){
                return back()->withInput()->with('error_msg', __('languages.permanent_reference_already_exists'));
            }

            // Update user detail
            $PostData = array(
                cn::USERS_ROLE_ID_COL               => cn::STUDENT_ROLE_ID,
                cn::USERS_GRADE_ID_COL              => (!empty($request->grade_id)) ?  $request->grade_id : '',
                cn::USERS_SCHOOL_ID_COL             => auth()->user()->school_id,
                cn::STUDENT_NUMBER_WITHIN_CLASS     => $request->student_number,
                cn::USERS_CLASS                     => $Grades->name.$classData->name,
                cn::USERS_CLASS_ID_COL              => $classData->id,
                cn::USERS_PERMANENT_REFERENCE_NUMBER=> ($request->permanent_refrence_number) ? $request->permanent_refrence_number : null,
                cn::USERS_NAME_EN_COL               =>  $this->encrypt($request->name_en),
                cn::USERS_NAME_CH_COL               => $this->encrypt($request->name_ch),
                cn::USERS_EMAIL_COL                 => $request->email,
                cn::USERS_MOBILENO_COL              => ($request->mobile_no) ? $this->encrypt($request->mobile_no) : null,
                cn::USERS_ADDRESS_COL               => ($request->address) ? $this->encrypt($request->address) : null,
                cn::USERS_GENDER_COL                => $request->gender ?? null,
                cn::USERS_CITY_COL                  => ($request->city) ? $this->encrypt($request->city) : null,
                cn::USERS_DATE_OF_BIRTH_COL         => ($request->date_of_birth) ? $this->DateConvertToYMD($request->date_of_birth) : null,                
                cn::USERS_PASSWORD_COL              => Hash::make($request->password),
                cn::USERS_STATUS_COL                => $request->status ?? 'active',
                cn::USERS_CREATED_BY_COL            => auth()->user()->id
            );
            $this->StoreAuditLogFunction($PostData,'User',cn::USERS_ID_COL,$id,'Update Student',cn::USERS_TABLE_NAME,'');
            if(User::where([
                cn::USERS_SCHOOL_ID_COL => Auth::user()->{cn::USERS_SCHOOL_ID_COL},
                cn::USERS_CLASS_STUDENT_NUMBER => $Grades->name.$classData->name.$request->student_number,
                cn::USERS_ROLE_ID_COL => cn::STUDENT_ROLE_ID
            ])->where(cn::USERS_ID_COL,'!=',$id)->doesntExist()){
                $PostData += ([cn::USERS_CLASS_STUDENT_NUMBER      =>  $Grades->name.$classData->name.$request->student_number,]);
            }else{
                return back()->with('error_msg', __('languages.duplicate_class_student_number'));
            }
            $Update = User::where(cn::USERS_ID_COL,$id)->Update($PostData);
            if($Update){
                return redirect('Student')->with('success_msg', __('languages.student_updated_successfully'));
            }else{
                return back()->with('error_msg', __('languages.problem_was_occur_please_try_again'));
            }
        }catch(\Exception $exception){
            return back()->withError($exception->getMessage())->withInput();
        }
    }

    /**
     * USE : Delete User from user list
     */
    public function destroy($id){
        try{
            if(!in_array('student_management_delete', Helper::getPermissions(Auth::user()->{cn::USERS_ID_COL}))) {
                return  redirect(Helper::redirectRoleBasedDashboard(Auth::user()->{cn::USERS_ID_COL}));
            }
            $this->StoreAuditLogFunction('','User','','','Delete Student ID '.$id,cn::USERS_TABLE_NAME,'');
            $User = User::find($id);
            if($User->delete()){
                return $this->sendResponse([], __('languages.student_deleted_successfully'));
            }else{
                return $this->sendError(__('languages.problem_was_occur_please_try_again'), 422);
            }
        }catch (\Exception $exception) {
            return $this->sendError($exception->getMessage(), 404);
        }
    }

    /**
     * USE : Add new grade
     */
    public function AddGrade(Request $request){
        try{
            $Update = User::where(cn::USERS_ID_COL,$request->id)->Update([cn::USERS_GRADE_ID_COL => $request->class_id]);
            if($Update){
                return $this->sendResponse([], __('languages.class_promotion_successfully'));
            }else{
                return $this->sendError(__('languages.problem_was_occur_please_try_again'), 422);
            }
        }catch (\Exception $exception) {
            return $this->sendError($exception->getMessage(), 404);
        }
    }

    public function classpromotion(Request $request){
        $flag= 1;
        if(!empty($request->studentIds) && !empty($request->class_type) && !empty($request->grade_id) ){
            foreach($request->studentIds as $student){
                $Grades = Grades::find($request->grade_id);
                $ClassData = GradeClassMapping::where([
                                cn::GRADE_CLASS_MAPPING_CURRICULUM_YEAR_ID_COL => $this->GetCurriculumYear(),
                                cn::GRADE_CLASS_MAPPING_SCHOOL_ID_COL => $this->isSchoolLogin(),
                                cn::GRADE_CLASS_MAPPING_ID_COL => $request->class_type
                            ])->first();
                $UserData = User::find($student);
                $user_class_student_number = '';
                if(!empty($Grades->{cn::GRADES_ID_COL}) && !empty($ClassData->{cn::GRADE_CLASS_MAPPING_NAME_COL})){
                    $user_class_student_number .= $Grades->{cn::GRADES_ID_COL}.''.$ClassData->{cn::GRADE_CLASS_MAPPING_NAME_COL};
                }
                if(!empty($UserData->{cn::STUDENT_NUMBER_WITHIN_CLASS})){
                    $user_class_student_number .= $UserData->{cn::STUDENT_NUMBER_WITHIN_CLASS};
                }else{
                    $user_class_student_number .= '00';
                }
                $postData = array(
                    cn::USERS_GRADE_ID_COL => $request->grade_id,
                    cn::USERS_CLASS_ID_COL => $request->class_type,
                    cn::USERS_CLASS => $Grades->{cn::GRADES_ID_COL}.''.$ClassData->{cn::GRADE_CLASS_MAPPING_NAME_COL},
                    cn::USERS_CLASS_STUDENT_NUMBER => $user_class_student_number
                );
                $getUserDetail = User::where(cn::USERS_ID_COL,$student)->first();
                if(User::where(cn::USERS_ID_COL,$student)->update($postData)){
                    $classpromotionCreateData = array(
                        cn::CLASS_PROMOTION_HISTORY_SCHOOL_ID_COL =>  $getUserDetail->school_id ?? '',
                        cn::CLASS_PROMOTION_HISTORY_STUDENT_ID_COL => $getUserDetail->id,
                        cn::CLASS_PROMOTION_HISTORY_CURRENT_GRADE_ID_COL => $request->grade_id,
                        cn::CLASS_PROMOTION_HISTORY_CURRENT_CLASS_ID_COL => $request->class_type,
                        cn::CLASS_PROMOTION_HISTORY_PROMOTED_GRADE_ID_COL => $getUserDetail->grade_id ?? '',
                        cn::CLASS_PROMOTION_HISTORY_PROMOTED_CLASS_ID_COL => $getUserDetail->class_id ?? '',
                        cn::CLASS_PROMOTION_HISTORY_PROMOTED_BY_USER_ID_COL => Auth::user()->{cn::USERS_ID_COL}
                    );
                    ClassPromotionHistory::create($classpromotionCreateData);
                }else{
                    $flag= 0;break;
                }
            }
            if($flag==1){
                $this->StoreAuditLogFunction($request->studentIds,'User','','','Class Promotion in '.$request->grade_id.' GRADE and Class '.$request->class_type,cn::USERS_TABLE_NAME,'');
                return $this->sendResponse([], __('languages.class_promotion_successfully'));
            }else{
                return $this->sendError(__('languages.problem_was_occur_please_try_again'), 422);
            }
        }else{
            return $this->sendError(__('validation.please_user_select_first'), 422);
        }
    }
    
    public function myCalendar(Request $request){
        try{
            $userId = Auth::id();
            $examList = Exam::with(cn::ATTEMPT_EXAMS_TABLE_NAME)->whereRaw("find_in_set($userId,".cn::STUDENT_GROUP_STUDENT_ID_COL.")")->whereMonth(cn::EXAM_TABLE_PUBLISH_DATE_COL, date('m'))->whereYear(cn::EXAM_TABLE_PUBLISH_DATE_COL, date('Y'))->where(cn::EXAM_TABLE_STATUS_COLS,'publish')->where(cn::EXAM_TABLE_IS_GROUP_TEST_COL,'0')->get();
            return view('backend.student.my_calendar',compact('examList'));
        }catch(\Exception $exception){
            return redirect('exams')->withError($exception->getMessage());
        }
    }

    public function selectMonthData(Request $request){
        if (isset($request->year) && !empty($request->year) && isset($request->month) && !empty($request->month)) {
            $userId = Auth::id();
            $month = $request->month;
            $year = $request->year;
            $examList = Exam::whereRaw("find_in_set($userId,".cn::STUDENT_GROUP_STUDENT_ID_COL.")")->whereMonth(cn::EXAM_TABLE_PUBLISH_DATE_COL, $month)->whereYear(cn::EXAM_TABLE_PUBLISH_DATE_COL, $year)->where(cn::EXAM_TABLE_STATUS_COLS,'publish')->select(cn::EXAM_TABLE_TITLE_COLS,cn::EXAM_TABLE_PUBLISH_DATE_COL,cn::EXAM_TABLE_IS_GROUP_TEST_COL,cn::EXAM_TABLE_GROUP_IDS_COL)->where(cn::EXAM_TABLE_IS_GROUP_TEST_COL,'0')->get()->toArray();
            $AllData = array('examList' => $examList);
            return $AllData;
        }
        return '';
    }

    /**
     * USE : School can view student calss promotion history
     */
    public function ClassPromotionHistory(Request $request,$id){
        $items = $request->items ?? 10;
        $promotionHistory = User::with('promotionhistory')->where(cn::USERS_ID_COL,$id)->first();
        $countHistory =  $promotionHistory->promotionhistory()->count();
        $arrayPromotionHistory = $promotionHistory->promotionhistory()->paginate($items);
       return view('backend.studentmanagement.class_promotion',compact('promotionHistory','arrayPromotionHistory','items','countHistory'));
    }
    
    /**
     * USE : Student Self create Exam 
     */
    public function selfExamCreate($request){
        $response = [];
        $timeduration = null;
        if($request['self_learning_test_type'] == 2){
            $TotalTime = 0;
            $QuestionPerSeconds = $this->getGlobalConfiguration('default_second_per_question');
            if(isset($QuestionPerSeconds) && !empty($QuestionPerSeconds) && !empty($request['questionIds'])){
                $totalSeconds = (count(explode(",",$request['questionIds'])) * $QuestionPerSeconds);
                $TotalTime = gmdate("H:i:s", $totalSeconds);
                $timeduration = ($TotalTime) ? $this->timeToSecond($TotalTime): null;
            }
        }
        $examData = [
            cn::EXAM_CURRICULUM_YEAR_ID_COL     => $this->CurrentCurriculumYearId, // "CurrentCurriculumYearId" Get value from Global Configuration
            cn::EXAM_TYPE_COLS                  => 1,
            cn::EXAM_REFERENCE_NO_COL           => $this->GetMaxReferenceNumberExam(1,$request['self_learning_test_type']),
            cn::EXAM_TABLE_TITLE_COLS           => $this->createTestTitle(),
            cn::EXAM_TABLE_FROM_DATE_COLS       => Carbon::now(),
            cn::EXAM_TABLE_TO_DATE_COLS         => Carbon::now(),
            cn::EXAM_TABLE_RESULT_DATE_COLS     => Carbon::now(),
            cn::EXAM_TABLE_PUBLISH_DATE_COL     => Carbon::now(),
            cn::EXAM_TABLE_TIME_DURATIONS_COLS  => $timeduration,
            cn::EXAM_TABLE_QUESTION_IDS_COL     => ($request['questionIds']) ?  $request['questionIds'] : null,
            cn::EXAM_TABLE_STUDENT_IDS_COL      => $this->LoggedUserId(),
            cn::EXAM_TABLE_SCHOOL_COLS          => Auth::user()->{cn::USERS_SCHOOL_ID_COL},
            cn::EXAM_TABLE_IS_UNLIMITED         => ($request['self_learning_test_type'] == 1) ? 1 : 0,
            cn::EXAM_TABLE_SELF_LEARNING_TEST_TYPE_COL => $request['self_learning_test_type'],
            cn::EXAM_TABLE_CREATED_BY_COL       => $this->LoggedUserId(),
            cn::EXAM_TABLE_CREATED_BY_USER_COL  => 'student',
            cn::EXAM_TABLE_STATUS_COLS          => 'publish'
        ];
        $this->StoreAuditLogFunction($examData,'Exam',cn::EXAM_TABLE_ID_COLS,'','Create Exam',cn::EXAM_TABLE_NAME,'');
        $exams = Exam::create($examData);
        if($exams){
            // Create exam school mapping
            ExamSchoolMapping::create(['school_id' => Auth::user()->{cn::USERS_SCHOOL_ID_COL},'exam_id' => $exams->id, 'status' => 'publish']);
            $strand_id = '';
            $learning_unit_id = '';
            $learning_objectives_id = '';
            $difficulty_lvl = '';
            $difficulty_mode = '';
            $test_time_duration = '';
            if(isset($request['strand_id']) && !empty($request['strand_id'])){
                $strand_id = implode(',', $request['strand_id']);
            }
            if(isset($request['learning_unit_id']) && !empty($request['learning_unit_id'])){
                $learning_unit_id = implode(',', $request['learning_unit_id']);
            }
            if(isset($request['learning_objectives_id']) && !empty($request['learning_objectives_id'])){
                $learning_objectives_id = implode(',', $request['learning_objectives_id']);
            }
            if(isset($request['difficulty_lvl']) && !empty($request['difficulty_lvl'])){
                $difficulty_lvl = implode(',', $request['difficulty_lvl']);
            }
            if(isset($request['difficulty_mode']) && !empty($request['difficulty_mode'])){
                $difficulty_mode = $request['difficulty_mode'];
            }
            if(isset($request['test_time_duration']) && !empty($request['test_time_duration'])){
                $test_time_duration = $request['test_time_duration'];
            }
            $examData = [
                cn::EXAM_TYPE_COLS => 1,
                cn::EXAM_CONFIGURATIONS_DETAILS_EXAM_ID_COL => $exams->id,
                cn::EXAM_CONFIGURATIONS_DETAILS_CREATED_BY_USER_ID_COL => $this->LoggedUserId(),
                cn::EXAM_CONFIGURATIONS_DETAILS_STRAND_IDS_COL => $strand_id,
                cn::EXAM_CONFIGURATIONS_DETAILS_LEARNING_UNIT_IDS_COL => $learning_unit_id,
                cn::EXAM_CONFIGURATIONS_DETAILS_LEARNING_OBJECTIVES_IDS_COL => $learning_objectives_id,
                cn::EXAM_CONFIGURATIONS_DETAILS_DIFFICULTY_MODE_COL => $difficulty_lvl,
                cn::EXAM_CONFIGURATIONS_DETAILS_DIFFICULTY_LEVELS_COL => $difficulty_mode,
                cn::EXAM_CONFIGURATIONS_DETAILS_NO_OF_QUESTIONS_COL => ($request['questionIds']) ?  count(explode(",",$request['questionIds'])) : null,
                //cn::EXAM_CONFIGURATIONS_DETAILS_TIME_DURATION_COL => $test_time_duration
            ];
            ExamConfigurationsDetails::create($examData);
            $response['redirectUrl'] = 'student/exam/'.$exams->id;
            $response['self_learning_type'] = $request['self_learning_test_type'];
        }
        return $response;
    }

    /**
     * USE : Get the self learning test list
     */
    public function getSelfLearningExerciseList(Request $request){
        try{
            $userId = Auth::id();
            $schoolId = Auth::user()->{cn::USERS_SCHOOL_ID_COL};
            $roleId = Auth::user()->{cn::USERS_ROLE_ID_COL};
            $ExamList = array();
            $active_tab = "";
            $grade_id = '';
            $stdata = array();
            $student_id = '';
            $difficultyLevels = PreConfigurationDiffiltyLevel::where(cn::PRE_CONFIGURE_DIFFICULTY_CURRICULUM_YEAR_ID_COL,$this->GetCurriculumYear())->get();

            if(isset($request->active_tab) && !empty($request->active_tab)){
                $active_tab = $request->active_tab;
            }
            $ExamsData = array();
            // Get Self Learning Exams List
            $ExamsData['exercise_list'] =   Exam::with(['attempt_exams' => fn($query) => $query->where(cn::ATTEMPT_EXAMS_STUDENT_STUDENT_ID, Auth::user()->{cn::USERS_ID_COL})])
                                            ->whereRaw("find_in_set($userId,student_ids)")
                                            ->where([
                                                cn::EXAM_TYPE_COLS => 1,
                                                cn::EXAM_TABLE_IS_GROUP_TEST_COL => 0,
                                                cn::EXAM_TABLE_CREATED_BY_COL => $this->LoggedUserId(),
                                                cn::EXAM_TABLE_SELF_LEARNING_TEST_TYPE_COL => 1,
                                                cn::EXAM_TABLE_STATUS_COLS => 'publish',
                                                cn::EXAM_CURRICULUM_YEAR_ID_COL=>$this->GetCurriculumYear()
                                            ])
                                            ->orderBy(cn::EXAM_TABLE_CREATED_AT,'DESC')
                                            ->get();
            return view('backend/student/self_learning/self_learning_exercise_list',compact('difficultyLevels','ExamsData'));
        }catch(Exception $exception){
            return back()->withError($exception->getMessage());
        }
    }

    /**
     * USE : Get the Testing Zone List (Exam Type:Test)
     */
    public function getTestingZoneList(Request $request){
        try{
            $userId = Auth::id();
            $schoolId = Auth::user()->{cn::USERS_SCHOOL_ID_COL};
            $roleId = Auth::user()->{cn::USERS_ROLE_ID_COL};
            $ExamList = array();
            $student_id = '';
            // $difficultyLevels = PreConfigurationDiffiltyLevel::all();
            $difficultyLevels = PreConfigurationDiffiltyLevel::where(cn::PRE_CONFIGURE_DIFFICULTY_CURRICULUM_YEAR_ID_COL,$this->GetCurriculumYear())->get();
            $ExamsData = array();
            $ExamsData['test_list'] = Exam::with(['attempt_exams' => fn($query) => $query->where(cn::ATTEMPT_EXAMS_STUDENT_STUDENT_ID, Auth::user()->{cn::USERS_ID_COL})])
                                            ->whereRaw("find_in_set($userId,student_ids)")
                                            ->where([
                                                cn::EXAM_TYPE_COLS => 1,
                                                cn::EXAM_TABLE_IS_GROUP_TEST_COL => 0,
                                                cn::EXAM_TABLE_CREATED_BY_COL => $this->LoggedUserId(),
                                                cn::EXAM_TABLE_SELF_LEARNING_TEST_TYPE_COL => 2,
                                                cn::EXAM_TABLE_STATUS_COLS => 'publish',
                                                cn::EXAM_CURRICULUM_YEAR_ID_COL => $this->GetCurriculumYear()
                                            ])
                                            ->orderBy(cn::EXAM_TABLE_CREATED_AT,'DESC')
                                            ->get();
            return view('backend/student/testing_zone/self_learning_test_list',compact('difficultyLevels','ExamsData'));
        }catch(Exception $exception){
            return back()->withError($exception->getMessage());
        }
    }

    /**
     * USE : Student Create Self Learning Exercise
     */
    public function CreateSelfLearningExercise(Request $request){
        $difficultyLevels = PreConfigurationDiffiltyLevel::all();
        $RequiredQuestionPerSkill = [];
        $RequiredQuestionPerSkill = [
            'minimum_question_per_skill' => $this->getGlobalConfiguration('no_of_questions_per_learning_skills'),
            'maximum_question_per_skill' => $this->getGlobalConfiguration('max_no_question_per_learning_objectives')
        ];
        // Get Strand List
        $strandsList = Strands::all();
        $learningObjectivesConfiguration = array();
        if(!empty($strandsList)){
            $LearningUnits = LearningsUnits::where(cn::LEARNING_UNITS_STRANDID_COL, $strandsList[0]->{cn::STRANDS_ID_COL})->get();
            if(!empty($LearningUnits)){
                // $LearningObjectives = LearningsObjectives::whereIn(cn::LEARNING_OBJECTIVES_LEARNING_UNITID_COL, $LearningUnits->pluck(cn::LEARNING_OBJECTIVES_ID_COL))->get();
                $LearningObjectives = LearningsObjectives::IsAvailableQuestion()->whereIn(cn::LEARNING_OBJECTIVES_LEARNING_UNITID_COL, $LearningUnits->pluck(cn::LEARNING_OBJECTIVES_ID_COL))->get();
            }
        }
        return view('backend.student.self_learning.create_self_learning_exercise',compact('difficultyLevels','strandsList','LearningUnits','LearningObjectives','RequiredQuestionPerSkill','learningObjectivesConfiguration'));
    }

    /**
     * USE : Student Create Self Learning Exercise
     */
    public function CreateSelfLearningTest(Request $request){
        $difficultyLevels = PreConfigurationDiffiltyLevel::all();
        $RequiredQuestionPerSkill = [];
        $RequiredQuestionPerSkill = [
            'minimum_question_per_skill' => $this->getGlobalConfiguration('no_of_questions_per_learning_skills'),
            'maximum_question_per_skill' => $this->getGlobalConfiguration('max_no_question_per_learning_objectives')
        ];
        // Get Strand List
        $strandsList = Strands::all();
        $learningObjectivesConfiguration = array();
        if(!empty($strandsList)){
            $LearningUnits = LearningsUnits::where(cn::LEARNING_UNITS_STRANDID_COL, $strandsList[0]->{cn::STRANDS_ID_COL})->get();
            if(!empty($LearningUnits)){
                // $LearningObjectives = LearningsObjectives::whereIn(cn::LEARNING_OBJECTIVES_LEARNING_UNITID_COL, $LearningUnits->pluck(cn::LEARNING_OBJECTIVES_ID_COL))->get();
                $LearningObjectives = LearningsObjectives::IsAvailableQuestion()->whereIn(cn::LEARNING_OBJECTIVES_LEARNING_UNITID_COL, $LearningUnits->pluck(cn::LEARNING_OBJECTIVES_ID_COL))->get();
            }
        }
        return view('backend.student.testing_zone.create_self_learning_test',compact('difficultyLevels','strandsList','LearningUnits','LearningObjectives','RequiredQuestionPerSkill','learningObjectivesConfiguration'));
    }

    /**
     * USE : Get Question Ids in School from AI Api
     */
    public function CreateSelfLearning(Request $request){
        if(isset($request)){
            $difficultyLevels = PreConfigurationDiffiltyLevel::all();
            $result = array();
            $minimumQuestionPerSkill = Helper::getGlobalConfiguration('no_of_questions_per_learning_skills') ?? 2 ;
            $learningUnitArray = array();
            $coded_questions_list_all = array();
            $difficulty_lvl = $request->difficulty_lvl;
            $no_of_questions = 10;
            if(isset($request->total_no_of_questions) && !empty($request->total_no_of_questions)){
                $no_of_questions = $request->total_no_of_questions;
            }

            if($request->self_learning_test_type==1){
                $QuestionType = array(2,3);
            }else{
                $QuestionType = array(1);
            }

            $learningUnitArray = array();
            if(isset($request->learning_unit) && !empty($request->learning_unit)){
                foreach($request->learning_unit as $learningUnitId => $learningUnitData){
                    $learningObjectiveQuestionArray = array();
                    if(isset($learningUnitData['learning_objective']) && !empty($learningUnitData['learning_objective'])){
                        foreach($learningUnitData['learning_objective'] as $id => $data){
                            $learningObjectiveSkillQuestionArray = array();
                            $learningObjectiveQuestionArray = array();
                            $coded_questions_list = array();
                            $oldQuestionIds = array();
                            //if(isset($data['learning_objectives_difficulty_level']) && !empty($data['learning_objectives_difficulty_level']) && isset($data['get_no_of_question_learning_objectives']) && !empty($data['get_no_of_question_learning_objectives']) && $request->difficulty_mode == 'manual'){
                            $objective_mapping_id = StrandUnitsObjectivesMappings::where([
                                                        cn::OBJECTIVES_MAPPINGS_LEARNING_UNIT_ID_COL => $learningUnitId,
                                                        cn::OBJECTIVES_MAPPINGS_LEARNING_OBJECTIVES_ID_COL => $id
                                                    ])
                                                    ->pluck(cn::OBJECTIVES_MAPPINGS_ID_COL)
                                                    ->toArray();
                            $LearningsSkillAll = array_keys($learningUnitData['learning_objective']);
                            $selected_levels = array();
                            if($request->difficulty_mode == 'manual' && array_key_exists('learning_objectives_difficulty_level',$data)){
                                foreach($data['learning_objectives_difficulty_level'] as $difficulty_value){
                                    $selected_levels[] = ($difficulty_value - 1);
                                }
                            }
                            $learningsObjectivesData = LearningsObjectives::find($id);
                            $LearningsSkill = $learningsObjectivesData->code;
                            $QuestionSkill = Question::with('PreConfigurationDifficultyLevel')
                                            ->whereIn(cn::QUESTION_OBJECTIVE_MAPPING_ID_COL,$objective_mapping_id)
                                            //->where(cn::QUESTION_QUESTION_TYPE_COL,$request->test_type)
                                            ->whereIn(cn::QUESTION_QUESTION_TYPE_COL,[2,3])
                                            ->inRandomOrder()
                                            ->groupBy(cn::QUESTION_E_COL)
                                            ->pluck(cn::QUESTION_E_COL)
                                            ->toArray();
                            $no_of_questions = $data['get_no_of_question_learning_objectives'];
                            if(!empty($QuestionSkill)){
                                foreach($QuestionSkill as $skillKey => $skillName){
                                    $QuestionQuery = Question::with('PreConfigurationDifficultyLevel')
                                                        ->whereNotIn(cn::QUESTION_TABLE_ID_COL,$oldQuestionIds)
                                                        ->whereIn(cn::QUESTION_OBJECTIVE_MAPPING_ID_COL,$objective_mapping_id)
                                                        //->where(cn::QUESTION_QUESTION_TYPE_COL,$request->test_type)
                                                        ->whereIn(cn::QUESTION_QUESTION_TYPE_COL,[2,3])
                                                        ->where(cn::QUESTION_E_COL,$skillName);
                                    if($request->difficulty_mode == 'manual' && array_key_exists('learning_objectives_difficulty_level',$data)){
                                        $QuestionQuery->whereIn(cn::QUESTION_DIFFICULTY_LEVEL_COL,$data['learning_objectives_difficulty_level']);
                                    }
                                    //->limit($minimumQuestionPerSkill)
                                    $questionArray = $QuestionQuery->inRandomOrder()->get()->toArray();
                                    if(!empty($questionArray)){
                                        $coded_questions_list = array();
                                        foreach ($questionArray as $question_key => $question_value) {
                                            $oldQuestionIds[] = $question_value['id'];
                                            $coded_questions_list[] = array($question_value[cn::QUESTION_NAMING_STRUCTURE_CODE_COL],floatval($question_value['pre_configuration_difficulty_level']['title']),0);                                                    
                                        }
                                        if(!empty($coded_questions_list)){
                                            if($skillKey==0){
                                                $learningObjectiveSkillQuestionArray[] = array($selected_levels,$coded_questions_list, floatval(round($no_of_questions/sizeOf($QuestionSkill))),0.01);
                                            }else{
                                                $learningObjectiveSkillQuestionArray[] = array($selected_levels,$coded_questions_list, floatval(floor($no_of_questions/sizeOf($QuestionSkill))),0.01);
                                            }
                                        }
                                    }
                                }
                            }
                            $learningUnitArray[] = $learningObjectiveSkillQuestionArray;
                        }
                    }
                }
            }

            if(sizeof($learningUnitArray) > 0){
                if(isset($learningUnitArray) && !empty($learningUnitArray)){
                    $requestPayload = new \Illuminate\Http\Request();
                    // call api based on selected mode for ai-api
                    switch($request->difficulty_mode){
                        case 'manual':
                                $requestPayload =   $requestPayload->replace([
                                                        'learning_units'       => array($learningUnitArray)
                                                    ]);
                                $response = $this->AIApiService->Assign_Questions_Manually_To_Learning_Units($requestPayload);
                            break;
                    }
                    $responseQuestionCodesArray = array();
                    if(isset($response) && !empty($response)){
                        foreach($response as $learningObjectiveArray){
                            foreach($learningObjectiveArray as $learningSkillArray){
                                foreach($learningSkillArray as $value){
                                    //foreach($value as $questionData){
                                    foreach($value[0] as $questionData){
                                        //$questionDataCodes = array_column($questionData,0);
                                        $questionDataCodes = $questionData[0];
                                        if(isset($questionDataCodes) && !empty($questionDataCodes)){
                                            //$responseQuestionCodesArray = array_merge($responseQuestionCodesArray,$questionDataCodes);
                                            $responseQuestionCodesArray = array_merge($responseQuestionCodesArray,[$questionDataCodes]);
                                        }
                                    }
                                }
                            }
                        }

                        if(isset($responseQuestionCodesArray) && !empty($responseQuestionCodesArray)){
                            $question_list = Question::with(['answers','PreConfigurationDifficultyLevel','objectiveMapping'])
                                            ->whereIn(cn::QUESTION_NAMING_STRUCTURE_CODE_COL,$responseQuestionCodesArray)
                                            ->get();
                            $question_id_list = Question::whereIn(cn::QUESTION_NAMING_STRUCTURE_CODE_COL,$responseQuestionCodesArray)
                                                ->inRandomOrder()
                                                ->pluck(cn::QUESTION_TABLE_ID_COL)
                                                ->toArray();
                            if(isset($question_id_list) && !empty($question_id_list)){
                                $questionId_data_list = implode(',',array_unique($question_id_list));
                                $request = array_merge($request->all(), ['questionIds' => $questionId_data_list]);
                                $response = $this->selfExamCreate($request);
                                if(isset($response) && !empty($response)){
                                    return $this->sendResponse($response);
                                }else{
                                    return $this->sendError(__('languages.problem_was_occur_please_try_again'), 422);
                                }
                            }else{
                                return $this->sendError(__('languages.questions-not-found'), 422);
                            }
                        }else{
                            return $this->sendError(__('languages.not_enough_questions_in_that_objective'), 422);
                        }
                    }else{
                        return $this->sendError(__('languages.problem_was_occur_please_try_again'), 422);
                    }
                }
            }else{
                return $this->sendError(__('languages.not_enough_questions_in_that_objective'), 422);
            }
        }
    }
}