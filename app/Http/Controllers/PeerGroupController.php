<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\CommonController;
use App\Models\GradeClassMapping;
use App\Models\TeachersClassSubjectAssign;
use App\Models\Grades;
use App\Models\User;
use App\Models\Subjects;
use App\Models\PeerGroup;
use App\Models\PeerGroupMember;
use Illuminate\Support\Facades\Auth;
use App\Constants\DbConstant As cn;
use App\Traits\Common;
use App\Traits\ResponseFormat;
use Exception;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\View;
use App\Helpers\Helper;
use App\Models\SubjectSchoolMappings;
use App\Models\GradeSchoolMappings;
use App\Models\CurriculumYearStudentMappings;
use App\Http\Services\TeacherGradesClassService;
use App\Http\Services\AIApiService;

class PeerGroupController extends Controller
{
    use Common, ResponseFormat;

    protected $TeacherGradesClassService;
    protected $AIApiService,$CommonController;

    public function __construct(){
        $this->middleware('auth');
        $this->middleware('preventBackHistory');
        $this->TeacherGradesClassService = new TeacherGradesClassService;
        $this->AIApiService = new AIApiService();
        $this->CommonController = new CommonController();
    }

    /**
     * USE : Peer Group Listing Page
     */
    public function index(Request $request){
        if(!in_array('peer_group_read', Helper::getPermissions(Auth::user()->{cn::USERS_ID_COL}))) {
            return  redirect(Helper::redirectRoleBasedDashboard(Auth::user()->{cn::USERS_ID_COL}));
        }

        $items = $request->items ?? 10;
        $PeerGroupQuery = '';
        $SubjectList = '';
        $PeerGroupQuery = PeerGroup::select('*')
                            ->with(['subject','Members'])
                            ->where(cn::PEER_GROUP_CURRICULUM_YEAR_ID_COL,$this->GetCurriculumYear());
        if($this->isSchoolLogin()){
            $PeerGroupList = $PeerGroupQuery->where(cn::PEER_GROUP_SCHOOL_ID_COL,Auth::user()->{cn::USERS_SCHOOL_ID_COL})
                            ->sortable()
                            ->orderBy(cn::PEER_GROUP_ID_COL,'DESC')
                            ->paginate($items);
            $subjectsIds =  SubjectSchoolMappings::where([
                                cn::SUBJECT_MAPPING_CURRICULUM_YEAR_ID_COL => $this->GetCurriculumYear(),
                                cn::CLASS_SUBJECT_MAPPING_SCHOOL_ID_COL => Auth::user()->{cn::USERS_SCHOOL_ID_COL}
                            ])
                            ->pluck(cn::CLASS_SUBJECT_MAPPING_SCHOOL_ID_COL)
                            ->unique()
                            ->toArray();
            if(!empty($subjectsIds)){
                $SubjectList = Subjects::whereIn(cn::SUBJECTS_ID_COL,$subjectsIds)->get();
            }
        }
        if($this->isTeacherLogin()){
            $PeerGroupList = $PeerGroupQuery->where(cn::PEER_GROUP_CREATED_BY_USER_ID_COL,Auth::user()->{cn::USERS_ID_COL})
                                ->sortable()
                                ->orderBy(cn::PEER_GROUP_ID_COL,'DESC')
                                ->paginate($items);
            // Find the subjects assigned to this teacher
            $SubjectList = [];
            $TeachersClassSubjectAssign = TeachersClassSubjectAssign::where([
                                            cn::TEACHER_CLASS_SUBJECT_CURRICULUM_YEAR_ID_COL => $this->GetCurriculumYear(),
                                            cn::TEACHER_CLASS_SUBJECT_TEACHER_ID_COL => Auth()->user()->{cn::USERS_ID_COL}
                                        ])
                                        ->pluck(cn::TEACHER_CLASS_SUBJECT_SUBJECT_ID_COL)
                                        ->toArray();
            if(isset($TeachersClassSubjectAssign) && !empty($TeachersClassSubjectAssign)){
                $mergeSubjects =  implode(',',$TeachersClassSubjectAssign);
                $subjects = explode(',',$mergeSubjects);
                $teacherSubject = array_unique($subjects);
                if(!empty($teacherSubject)){
                    $SubjectList = Subjects::whereIn(cn::SUBJECTS_ID_COL,$teacherSubject)->get();
                }
            }
            
        }
        
        if(isset($request->filter)){
            $Query = PeerGroup::with('subject')
                        ->where(cn::PEER_GROUP_CURRICULUM_YEAR_ID_COL,$this->GetCurriculumYear());
            if($request->searchName){
                $Query->where(cn::PEER_GROUP_GROUP_NAME_COL,'like','%'.$request->searchName.'%');
            }
            if($request->subject){
                $Query->whereHas('Subject',function ($q) use($request){
                    $q->where(cn::SUBJECTS_ID_COL,$request->subject);
                });
            }
            if(isset($request->status)){
                $Query->where(cn::PEER_GROUP_STATUS_COL,$request->status);
            }
            if($this->isTeacherLogin()){
                $PeerGroupList = $Query->where(cn::PEER_GROUP_CREATED_BY_USER_ID_COL,Auth::user()->{cn::USERS_ID_COL})->sortable()->orderBy(cn::PEER_GROUP_ID_COL,'DESC')->paginate($items);
            }
            if($this->isSchoolLogin()){
                $PeerGroupList = $Query->where(cn::PEER_GROUP_SCHOOL_ID_COL,Auth::user()->{cn::USERS_SCHOOL_ID_COL})->sortable()->orderBy(cn::PEER_GROUP_ID_COL,'DESC')->paginate($items);
            }
        }
        return view('backend.peer_group.peer_group_list',compact('items','PeerGroupList','SubjectList'));
    }

    /**
     * USE : Create Peer Group Form Page
     */
    public function create(){
        if(!in_array('peer_group_create', Helper::getPermissions(Auth::user()->{cn::USERS_ID_COL}))) {
            return  redirect(Helper::redirectRoleBasedDashboard(Auth::user()->{cn::USERS_ID_COL}));
        }
        $schoolId = Auth::user()->{cn::USERS_SCHOOL_ID_COL};
        $teacherId = $schoolId = Auth::user()->{cn::USERS_ID_COL};
        $gradeList = '';
        $SubjectList = '';
        $classTypeOptions = '';
        if($this->isTeacherLogin()){
            $gradeid =  TeachersClassSubjectAssign::where([
                            cn::TEACHER_CLASS_SUBJECT_CURRICULUM_YEAR_ID_COL => $this->GetCurriculumYear(),
                            cn::TEACHER_CLASS_SUBJECT_TEACHER_ID_COL => Auth()->user()->{cn::USERS_ID_COL}
                        ])
                        ->pluck(cn::TEACHER_CLASS_SUBJECT_CLASS_ID_COL)
                        ->toArray();

            $gradeList = Grades::whereIn(cn::GRADES_ID_COL,$gradeid)->get();

            // Get all assigned class
            $gradeClass = TeachersClassSubjectAssign::where([
                            cn::TEACHER_CLASS_SUBJECT_CURRICULUM_YEAR_ID_COL => $this->GetCurriculumYear(),
                            cn::TEACHER_CLASS_SUBJECT_TEACHER_ID_COL => Auth()->user()->{cn::USERS_ID_COL}
                        ])
                        ->pluck(cn::TEACHER_CLASS_SUBJECT_CLASS_NAME_ID_COL)
                        ->toArray();
            if(isset($gradeClass) && !empty($gradeClass)){
                $gradeClass = implode(',', $gradeClass);
                $gradeClassId = explode(',',$gradeClass);
            }
            if(!empty($gradeClassId)){
                $GradeClassData = GradeClassMapping::whereIn(cn::GRADE_CLASS_MAPPING_GRADE_ID_COL,$gradeid)
                                    ->whereIn(cn::GRADE_CLASS_MAPPING_ID_COL,$gradeClassId)
                                    ->where([
                                        cn::GRADE_CLASS_MAPPING_CURRICULUM_YEAR_ID_COL => $this->GetCurriculumYear(),
                                        cn::GRADE_CLASS_MAPPING_SCHOOL_ID_COL => $this->isTeacherLogin()
                                    ])->get();
            }
            $classTypeOptions = '';
            if(!empty($GradeClassData)){
                foreach($GradeClassData as $class){
                    $GradeList = Grades::find($class->{cn::GRADE_CLASS_MAPPING_GRADE_ID_COL});
                    $selected = '';
                    $classTypeOptions .= '<option '.$selected.' value='.strtoupper($class->{cn::GRADE_CLASS_MAPPING_ID_COL}).'>'.$GradeList->{cn::GRADES_NAME_COL}.strtoupper($class->{cn::GRADE_CLASS_MAPPING_NAME_COL}).'</option>';
                }
            }

            // Find the subjects assigned to this teacher
            $SubjectList = [];
            $TeachersClassSubjectAssign = TeachersClassSubjectAssign::where([
                                            cn::TEACHER_CLASS_SUBJECT_CURRICULUM_YEAR_ID_COL => $this->GetCurriculumYear(),
                                            cn::TEACHER_CLASS_SUBJECT_TEACHER_ID_COL => Auth()->user()->{cn::USERS_ID_COL}
                                        ])
                                        ->pluck('subject_id')
                                        ->toArray();
            if(isset($TeachersClassSubjectAssign) && !empty($TeachersClassSubjectAssign)){
                $mergeSubjects =  implode(',',$TeachersClassSubjectAssign);
                $subjects = explode(',',$mergeSubjects);
                $teacherSubject = array_unique($subjects);
                if(!empty($teacherSubject)){
                    $SubjectList = Subjects::whereIn(cn::SUBJECTS_ID_COL,$teacherSubject)->get();
                }
            }
        }
        if($this->isSchoolLogin()){
            $userData = User::with('classes')
                        ->where([cn::USERS_ROLE_ID_COL => cn::STUDENT_ROLE_ID,cn::USERS_SCHOOL_ID_COL => Auth::user()->{cn::USERS_SCHOOL_ID_COL}]);
            $gradeid = GradeSchoolMappings::where(cn::GRADES_MAPPING_CURRICULUM_YEAR_ID_COL,$this->GetCurriculumYear())
                        ->where(cn::GRADES_MAPPING_SCHOOL_ID_COL,$schoolId)
                        ->pluck(cn::GRADES_MAPPING_GRADE_ID_COL)
                        ->unique()
                        ->toArray();
            $gradeClass = GradeClassMapping::where(cn::GRADE_CLASS_MAPPING_CURRICULUM_YEAR_ID_COL,$this->GetCurriculumYear())
                        ->where(cn::GRADE_CLASS_MAPPING_SCHOOL_ID_COL,$schoolId)
                        ->pluck(cn::GRADE_CLASS_MAPPING_ID_COL)
                        ->toArray();
            $gradesList = Grades::whereIn(cn::GRADES_ID_COL,$gradeid)->get();
            if(!empty($gradeClassId)){
                $GradeClassData = GradeClassMapping::where(cn::GRADE_CLASS_MAPPING_CURRICULUM_YEAR_ID_COL,$this->GetCurriculumYear())
                                    ->whereIn(cn::GRADE_CLASS_MAPPING_GRADE_ID_COL,$gradeid)
                                    ->whereIn(cn::GRADE_CLASS_MAPPING_ID_COL,$gradeClassId)
                                    ->get();
            }
            $classTypeOptions = '';
            if(!empty($GradeClassData)){
                foreach($GradeClassData as $class){
                    $GradeList = Grades::find($class->{cn::GRADE_CLASS_MAPPING_GRADE_ID_COL});
                    $selected = '';
                    $classTypeOptions .= '<option '.$selected.' value='.strtoupper($class->{cn::GRADE_CLASS_MAPPING_ID_COL}).'>'.$GradeList->{cn::GRADES_NAME_COL}.strtoupper($class->{cn::GRADE_CLASS_MAPPING_NAME_COL}).'</option>';
                }
            }
            $subjectIds = SubjectSchoolMappings::where(cn::SUBJECT_MAPPING_CURRICULUM_YEAR_ID_COL,$this->GetCurriculumYear())
                            ->where(cn::SUBJECT_MAPPING_SCHOOL_ID_COL,Auth::user()->{cn::USERS_SCHOOL_ID_COL})
                            ->pluck(cn::SUBJECT_MAPPING_SUBJECT_ID_COL)
                            ->unique()
                            ->toArray();
            if(!empty($subjectIds)){
                $SubjectList = Subjects::whereIn(cn::SUBJECTS_ID_COL,$subjectIds)->get();
            }
        }
        return view('backend.peer_group.peer_group_add',compact('gradeList','classTypeOptions','SubjectList'));
    }

    /**
     * USE : Store peer group informations
     */
    public function store(Request $request){
        try{
            if(!in_array('peer_group_create', Helper::getPermissions(Auth::user()->{cn::USERS_ID_COL}))) {
                return  redirect(Helper::redirectRoleBasedDashboard(Auth::user()->{cn::USERS_ID_COL}));
            }
            $validator = Validator::make($request->all(), PeerGroup::rules($request, 'create'), PeerGroup::rulesMessages('create'));
            if($validator->fails()){
                return back()->withErrors($validator)->withInput();
            }

            // Store one variable into school id
            $schoolId = Auth::user()->{cn::USERS_SCHOOL_ID_COL};

            // if the validation is true then store process is start
            $PeerGroup = PeerGroup::Create([
                                        cn::PEER_GROUP_CURRICULUM_YEAR_ID_COL => $this->GetCurriculumYear(),
                                        cn::PEER_GROUP_DREAMSCHAT_GROUP_ID => $request->dreamschat_group_id,
                                        cn::PEER_GROUP_SCHOOL_ID_COL => $schoolId,
                                        cn::PEER_GROUP_GROUP_NAME_COL => $request->group_name,
                                        cn::PEER_GROUP_CREATED_BY_USER_ID_COL => Auth::user()->{cn::USERS_ID_COL},
                                        // 'subject_id' => $request->peer_group_subject_id,
                                        cn::PEER_GROUP_SUBJECT_ID_COL => 1,
                                        cn::PEER_GROUP_STATUS_COL => ($request->status == 'active') ? 1 : 0
                                    ]);
            if($PeerGroup){
                // Store data into peer group member mapping to selected members
                if(isset($request->memberIdsList) && !empty($request->memberIdsList)){
                    $peerGroupMember = [];
                    $memberIds = array_unique(explode(',',$request->memberIdsList));
                    foreach($memberIds as $memberid){
                        $peerGroupMember[] = [
                            cn::PEER_GROUP_MEMBERS_CURRICULUM_YEAR_ID_COL => $this->GetCurriculumYear(),
                            cn::PEER_GROUP_MEMBERS_PEER_GROUP_ID_COL => $PeerGroup->{cn::PEER_GROUP_ID_COL},
                            cn::PEER_GROUP_MEMBERS_MEMBER_ID_COL => $memberid
                        ];
                    }
                    if(!empty($peerGroupMember)){
                        PeerGroupMember::insert($peerGroupMember);
                    }
                }
                return redirect('peer-group')->with('success_msg', __('languages.peer_group_created_successfully'));
            }else{
                return back()->withInput()->with('error_msg', __('languages.problem_was_occur_please_try_again'));
            }
        }catch(\Exception $exception) {
            return back()->withError($exception->getMessage())->withInput();
        }
    }

    public function GetStudentClassId($studentId){
        if(!empty($studentId)){
            // $UserData = User::find($studentId);
            // return $UserData->{cn::USERS_CLASS_ID_COL};

            $UserData = User::with(['curriculum_year_mapping' => fn($query) => $query->where('user_id',$studentId)])->find($studentId);
            return $UserData->curriculum_year_mapping->class_id;
        }
    }

    public function edit(Request $request,$id){
        try{
            if(!in_array('peer_group_update', Helper::getPermissions(Auth::user()->{cn::USERS_ID_COL}))) {
                return  redirect(Helper::redirectRoleBasedDashboard(Auth::user()->{cn::USERS_ID_COL}));
            }
            $schoolId = Auth::user()->{cn::USERS_SCHOOL_ID_COL};
            $gradesList = '';
            $gradeList = '';
            if($this->isTeacherLogin()){    
                $gradesList = TeachersClassSubjectAssign::where([
                                    cn::TEACHER_CLASS_SUBJECT_CURRICULUM_YEAR_ID_COL => $this->GetCurriculumYear(),
                                    cn::TEACHER_CLASS_SUBJECT_TEACHER_ID_COL => Auth()->user()->{cn::USERS_ID_COL}
                                ])
                                ->with('getClass')
                                ->get()
                                ->unique(cn::TEACHER_CLASS_SUBJECT_CLASS_ID_COL);
                
                $gradeid = TeachersClassSubjectAssign::where([
                            cn::TEACHER_CLASS_SUBJECT_CURRICULUM_YEAR_ID_COL => $this->GetCurriculumYear(),
                            cn::TEACHER_CLASS_SUBJECT_TEACHER_ID_COL => Auth()->user()->{cn::USERS_ID_COL}
                        ])
                        ->pluck(cn::TEACHER_CLASS_SUBJECT_CLASS_ID_COL)
                        ->toArray();
                $gradeList = Grades::whereIn(cn::GRADES_ID_COL,$gradeid)->get();

                // Get all assigned class
                $gradeClass = TeachersClassSubjectAssign::where([
                                cn::TEACHER_CLASS_SUBJECT_CURRICULUM_YEAR_ID_COL => $this->GetCurriculumYear(),
                                cn::TEACHER_CLASS_SUBJECT_TEACHER_ID_COL => Auth()->user()->{cn::USERS_ID_COL}
                            ])
                            ->pluck(cn::TEACHER_CLASS_SUBJECT_CLASS_NAME_ID_COL)
                            ->toArray();
                if(isset($gradeClass) && !empty($gradeClass)){
                    $gradeClass = implode(',', $gradeClass);
                    $gradeClassId = explode(',',$gradeClass);
                }
                $GradeClassData = GradeClassMapping::whereIn(cn::GRADE_CLASS_MAPPING_GRADE_ID_COL,$gradeid)
                                    ->whereIn(cn::GRADE_CLASS_MAPPING_ID_COL,$gradeClassId)
                                    ->where([
                                        cn::GRADE_CLASS_MAPPING_CURRICULUM_YEAR_ID_COL => $this->GetCurriculumYear(),
                                        cn::GRADE_CLASS_MAPPING_SCHOOL_ID_COL => $this->isTeacherLogin()
                                    ])->get();
                $classTypeOptions = '';
                if(!empty($GradeClassData)){
                    foreach($GradeClassData as $class){
                        $GradeList = Grades::find($class->{cn::GRADE_CLASS_MAPPING_GRADE_ID_COL});
                        $classTypeOptions .= '<option selected value='.strtoupper($class->{cn::GRADE_CLASS_MAPPING_ID_COL}).'>'.$GradeList->{cn::GRADES_NAME_COL}.strtoupper($class->{cn::GRADE_CLASS_MAPPING_NAME_COL}).'</option>';
                    }
                }

                // Find the subjects assigned to this teacher
                $SubjectList = [];
                $TeachersClassSubjectAssign = TeachersClassSubjectAssign::where([
                                                cn::TEACHER_CLASS_SUBJECT_CURRICULUM_YEAR_ID_COL => $this->GetCurriculumYear(),
                                                cn::TEACHER_CLASS_SUBJECT_TEACHER_ID_COL => Auth()->user()->{cn::USERS_ID_COL}
                                            ])->pluck('subject_id')->toArray();
                if(isset($TeachersClassSubjectAssign) && !empty($TeachersClassSubjectAssign)){
                    $mergeSubjects =  implode(',',$TeachersClassSubjectAssign);
                    $subjects = explode(',',$mergeSubjects);
                    $teacherSubject = array_unique($subjects);
                    if(!empty($teacherSubject)){
                        $SubjectList = Subjects::whereIn(cn::SUBJECTS_ID_COL,$teacherSubject)->get();
                    }
                }
            }
            
            if($this->isSchoolLogin()){                
                $gradeid = GradeSchoolMappings::where(cn::GRADES_MAPPING_CURRICULUM_YEAR_ID_COL,$this->GetCurriculumYear())->where(cn::GRADES_MAPPING_SCHOOL_ID_COL,Auth::user()->{cn::USERS_SCHOOL_ID_COL})
                            ->pluck(cn::GRADES_MAPPING_GRADE_ID_COL)
                            ->unique()->toArray();
                $gradeClassId = GradeClassMapping::where(cn::GRADE_CLASS_MAPPING_CURRICULUM_YEAR_ID_COL,$this->GetCurriculumYear())->where(cn::GRADE_CLASS_MAPPING_SCHOOL_ID_COL,Auth::user()->{cn::USERS_SCHOOL_ID_COL})->pluck(cn::GRADE_CLASS_MAPPING_ID_COL)->toArray();
                if(!empty($gradeid) && !empty($gradeClassId)){
                    //$userData = User::with('grades')->whereIn(cn::USERS_GRADE_ID_COL,$gradeid)->whereIn(cn::USERS_CLASS_ID_COL,$gradeClassId)->where([cn::USERS_ROLE_ID_COL=> cn::STUDENT_ROLE_ID,cn::USERS_SCHOOL_ID_COL => Auth::user()->{cn::USERS_SCHOOL_ID_COL}]);
                    $gradesList = Grades::whereIn(cn::GRADES_ID_COL,$gradeid)->get();
                }
                if(!empty($gradeClassId)){
                    $GradeClassData = GradeClassMapping::where(cn::GRADE_CLASS_MAPPING_CURRICULUM_YEAR_ID_COL,$this->GetCurriculumYear())->whereIn(cn::GRADE_CLASS_MAPPING_GRADE_ID_COL,$gradeid)->whereIn(cn::GRADE_CLASS_MAPPING_ID_COL,$gradeClassId)->get();
                }
                $classTypeOptions = '';
                if(!empty($GradeClassData)){
                    foreach($GradeClassData as $class){
                        $GradeList = Grades::find($class->grade_id);
                        $selected = '';
                        $classTypeOptions .= '<option '.$selected.' value='.strtoupper($class->id).'>'.$GradeList->name.strtoupper($class->name).'</option>';
                    }
                }
                $subjectIds = SubjectSchoolMappings::where(cn::SUBJECT_MAPPING_CURRICULUM_YEAR_ID_COL,$this->GetCurriculumYear())
                                ->where(cn::SUBJECT_MAPPING_SCHOOL_ID_COL,Auth::user()->{cn::USERS_SCHOOL_ID_COL})
                                ->pluck(cn::SUBJECT_MAPPING_SUBJECT_ID_COL)
                                ->unique()->toArray();
                if(!empty($subjectIds)){
                    $SubjectList = Subjects::whereIn(cn::SUBJECTS_ID_COL,$subjectIds)->get();
                }
            }
            $peerGroupData = PeerGroup::where(cn::PEER_GROUP_CURRICULUM_YEAR_ID_COL,$this->GetCurriculumYear())->where(cn::PEER_GROUP_ID_COL,$id)->first();
            $peerMembers = PeerGroupMember::with('Student')
                            ->where(cn::PEER_GROUP_MEMBERS_CURRICULUM_YEAR_ID_COL,$this->GetCurriculumYear())
                            ->where(cn::PEER_GROUP_MEMBERS_PEER_GROUP_ID_COL,$peerGroupData->id)
                            ->get();
            return view('backend.peer_group.peer_group_edit',compact('gradesList','gradeList','classTypeOptions','SubjectList','peerGroupData','peerMembers'));
        }catch(Exception $exception){
            return back()->withError($exception->getMessage())->withInput();
        }
    }

    public function update(Request $request, $id){
       try{
            if(!in_array('peer_group_update', Helper::getPermissions(Auth::user()->{cn::USERS_ID_COL}))) {
                return  redirect(Helper::redirectRoleBasedDashboard(Auth::user()->{cn::USERS_ID_COL}));
            }
           //Update Group Details
           $postData = [
                cn::PEER_GROUP_GROUP_NAME_COL    => $request->group_name,
                //cn::PEER_GROUP_SUBJECT_ID_COL       => $request->peer_group_subject_id,
                cn::PEER_GROUP_DREAMSCHAT_GROUP_ID       => $request->dreamschat_group_id,
                cn::PEER_GROUP_SUBJECT_ID_COL       => 1,
                cn::PEER_GROUP_STATUS_COL           => ($request->status=="active") ? 1 : 0
           ];
           PeerGroup::find($id)->update($postData);
           //Code for Update Members
           if(!empty($request->memberIdsList)){
                $memberIdsList=explode(',',$request->memberIdsList);
                foreach($memberIdsList as $existsMembersId){
                    if(PeerGroupMember::where([cn::PEER_GROUP_MEMBERS_PEER_GROUP_ID_COL => $id, cn::PEER_GROUP_MEMBERS_MEMBER_ID_COL => $existsMembersId])->doesntExist()){
                        PeerGroupMember::Create([cn::PEER_GROUP_MEMBERS_PEER_GROUP_ID_COL=>$id, cn::PEER_GROUP_MEMBERS_MEMBER_ID_COL => $existsMembersId]);
                    }
                }
                PeerGroupMember::where(cn::PEER_GROUP_MEMBERS_PEER_GROUP_ID_COL,$id)->whereNotIn(cn::PEER_GROUP_MEMBERS_MEMBER_ID_COL, $memberIdsList)->delete();
           }else{
                PeerGroupMember::where([cn::PEER_GROUP_MEMBERS_PEER_GROUP_ID_COL => $id,cn::PEER_GROUP_MEMBERS_DELETED_AT_COL => Null])->delete();
           }
            return redirect('peer-group')->with('success_msg', __('languages.peer_group_updated_successfully'));
       }catch(Exception $exception){
            return back()->withError($exception->getMessage())->withInput();
       }
    }
    
    public function destroy($id){
        try{
            if(!in_array('peer_group_delete', Helper::getPermissions(Auth::user()->{cn::USERS_ID_COL}))) {
                return  redirect(Helper::redirectRoleBasedDashboard(Auth::user()->{cn::USERS_ID_COL}));
            }
            $PeerGroup = PeerGroup::where(cn::PEER_GROUP_CURRICULUM_YEAR_ID_COL,$this->GetCurriculumYear())->find($id);
            $ChatGroupId = $PeerGroup->dreamschat_group_id;
            if($PeerGroup->delete()){
                // Remove peer group members after deleting groups
                $PeerGroup->Members()->delete();
                return $this->sendResponse(['ChatGroupId' => $ChatGroupId], __('languages.peer_group_deleted_successfully'));
            }else{
                return $this->sendError(__('languages.problem_was_occur_please_try_again'), 422);
            }
        }catch(\Exception $exception){
            return $this->sendError($exception->getMessage(), 404);
        }
    }

    /**
     * Use : Get student list by grade class
     */
    public function getStudentListByGradeClass(Request $request){
        $studentListHtml = "";
        if(isset($request->gradeId) && isset($request->classIds)){
            // Find the student list
            // $studentList = User::where(cn::USERS_GRADE_ID_COL,$request->gradeId)
            //                 ->whereIn(cn::USERS_CLASS_ID_COL,$request->classIds)
            //                 ->where([
            //                     cn::USERS_ROLE_ID_COL=>cn::STUDENT_ROLE_ID
            //                 ])
            //                 ->where(cn::USERS_GRADE_ID_COL,'<>','')
            //                 ->get();
            $studentList = User::where([
                                cn::USERS_ROLE_ID_COL=>cn::STUDENT_ROLE_ID
                            ])
                            ->where(cn::USERS_GRADE_ID_COL,'<>','')
                            ->get()
                            ->whereIn('id',$this->curriculum_year_mapping_student_ids($request->gradeId,$request->classIds,''))
                            ->whereIn();
            if(!$studentList->isEmpty()){
                if(!empty($studentList)){
                    $studentListHtml .= '<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                            <strong for="student">Students</strong>
                                        </div>';
                    $studentListHtml .='<div class="all-student-checkbox-list">
                                            <input type="checkbox" class="select-all-peer-group-student" value="all">
                                            <label>All Students</label>
                                        </div>';
                    foreach($studentList as $student){
                        $studentListHtml .='<div class="student-checkbox-list">
                                                <input type="checkbox" class="peer_group_student_id" name="peer_group_student[]" value="'.$student->{cn::USERS_ID_COL}.'">
                                                <label for="">';                                
                                                $name = 'DecryptName'.ucfirst(app()->getLocale());
                                                if(!empty($student->{cn::USERS_CLASS_STUDENT_NUMBER})){
                                                    $studentListHtml .= $student->$name.' ('.$student->{cn::USERS_CLASS_STUDENT_NUMBER}.')';
                                                }else{
                                                    $studentListHtml .= $student->$name;
                                                }
                        $studentListHtml .='    </label></div>';
                    }
                }
                return $this->sendResponse($studentListHtml);
            }else{
                return $this->sendError("languages.student_not_available_as_per_you_selected_grade_and_class");
            }
        }else{
            return $this->sendError('languages.please_select_grade_and_class_first', 422);
        }
    }

    /**
     * USE : Find the member list for add new group member into peer group
     */
    public function memberlist(Request $request){
        $gradesList = '';
        $classTypeOptions = '';
        $MemberIds = $request->MemberIds ?? [];
        $gradeid =  array();
        $gradeClassId = array();
        $schoolId = Auth::user()->{cn::USERS_SCHOOL_ID_COL};
        if($this->isTeacherLogin()){
            $gradesList = TeachersClassSubjectAssign::where([
                            cn::TEACHER_CLASS_SUBJECT_CURRICULUM_YEAR_ID_COL => $this->GetCurriculumYear(),
                            cn::TEACHER_CLASS_SUBJECT_TEACHER_ID_COL => Auth()->user()->{cn::USERS_ID_COL}
                        ])
                        ->with('getClass')
                        ->get()
                        ->unique(cn::TEACHER_CLASS_SUBJECT_CLASS_ID_COL);
            $gradeid =  TeachersClassSubjectAssign::where([
                            cn::TEACHER_CLASS_SUBJECT_CURRICULUM_YEAR_ID_COL => $this->GetCurriculumYear(),
                            cn::TEACHER_CLASS_SUBJECT_TEACHER_ID_COL => Auth()->user()->{cn::USERS_ID_COL}
                        ])
                        ->pluck(cn::TEACHER_CLASS_SUBJECT_CLASS_ID_COL)
                        ->toArray();
            $gradeClass = TeachersClassSubjectAssign::where([
                            cn::TEACHER_CLASS_SUBJECT_CURRICULUM_YEAR_ID_COL => $this->GetCurriculumYear(),
                            cn::TEACHER_CLASS_SUBJECT_TEACHER_ID_COL => Auth()->user()->{cn::USERS_ID_COL}
                        ])
                        ->pluck(cn::TEACHER_CLASS_SUBJECT_CLASS_NAME_ID_COL)
                        ->toArray();
        }

        if($this->isSchoolLogin()){
            $gradeid = GradeSchoolMappings::where(cn::GRADES_MAPPING_CURRICULUM_YEAR_ID_COL,$this->GetCurriculumYear())
                        ->where(cn::GRADES_MAPPING_SCHOOL_ID_COL,$schoolId)
                        ->pluck(cn::GRADES_MAPPING_GRADE_ID_COL)
                        ->unique()
                        ->toArray();
            $gradeClass = GradeClassMapping::where(cn::GRADE_CLASS_MAPPING_CURRICULUM_YEAR_ID_COL,$this->GetCurriculumYear())
                            ->where(cn::GRADE_CLASS_MAPPING_SCHOOL_ID_COL,$schoolId)
                            ->pluck(cn::GRADE_CLASS_MAPPING_ID_COL)
                            ->toArray();
            if(!empty($gradeid) && !empty($gradeClass)){
                $userData = User::with('grades')
                                ->whereIn(cn::USERS_ID_COL,$this->curriculum_year_mapping_student_ids($gradeid,$gradeClass,Auth::user()->{cn::USERS_SCHOOL_ID_COL}))
                                ->where([
                                    cn::USERS_ROLE_ID_COL=> cn::STUDENT_ROLE_ID,
                                    cn::USERS_SCHOOL_ID_COL => Auth::user()->{cn::USERS_SCHOOL_ID_COL}
                                ]);
                $gradesList = Grades::whereIn(cn::GRADES_ID_COL,$gradeid)->get();
            }
        }

        if(isset($gradeClass) && !empty($gradeClass)){
            $gradeClass = implode(',', $gradeClass);
            $gradeClassId = explode(',',$gradeClass);
        }

        if(isset($request->filter) && isset($request->student_grade_id) && !empty($request->student_grade_id) && $request->student_grade_id!='all'){
            $gradeid = array($request->student_grade_id);
        }

        if(isset($request->filter) && isset($request->class_type_id) && !empty($request->class_type_id) && $request->class_type_id!='all'){
            $classids = $request->class_type_id;
        }
        
        $classTypeOptions = '';
        $grade_id = empty($request->grade) ? 0 : $request->grade;
        $gradeData = Grades::whereIn(cn::GRADES_ID_COL,$gradeid)->get();
        $studentList = User::where([cn::USERS_SCHOOL_ID_COL=>Auth::user()->{cn::USERS_SCHOOL_ID_COL},cn::USERS_ROLE_ID_COL=>cn::STUDENT_ROLE_ID])
                        ->get()
                        ->whereIn('CurriculumYearGradeId',$gradeid)
                        ->whereIn('CurriculumYearClassId',$gradeClassId);
        $countStudentData = User::whereIn(cn::USERS_GRADE_ID_COL,$gradeid)
                                    ->where([
                                        cn::USERS_SCHOOL_ID_COL => Auth::user()->{cn::USERS_SCHOOL_ID_COL},
                                        cn::USERS_ROLE_ID_COL => cn::STUDENT_ROLE_ID
                                    ])
                                    ->get()
                                    ->whereIn('CurriculumYearGradeId',$gradeid)
                                    ->whereIn('CurriculumYearClassId',$gradeClassId)
                                    ->count();


        $GradeClassData = GradeClassMapping::whereIn(cn::GRADE_CLASS_MAPPING_GRADE_ID_COL,$gradeid)
                            ->whereIn(cn::GRADE_CLASS_MAPPING_ID_COL,$gradeClassId)
                            ->where([
                                cn::GRADE_CLASS_MAPPING_CURRICULUM_YEAR_ID_COL => $this->GetCurriculumYear(),
                                cn::GRADE_CLASS_MAPPING_SCHOOL_ID_COL => $schoolId
                            ])
                            ->get();        
        if(!empty($GradeClassData)){
            foreach($GradeClassData as $class){
                $GradeList = Grades::find($class->grade_id);
                $selected='';
                if(isset($request->student_grade_id) && $request->student_grade_id=='all' && !isset($request->class_type_id)){
                    $selected='selected="selected"';
                }else if(!isset($request->student_grade_id) && !isset($request->class_type_id)){
                    $selected='selected="selected"';
                }else if(isset($request->class_type_id) && !empty($request->class_type_id) && in_array($class->id,$request->class_type_id)){
                    $selected='selected="selected"';
                }
                $classTypeOptions .= '<option '.$selected.' value='.strtoupper($class->id).'>'.$GradeList->name.strtoupper($class->name).'</option>';
            }
        }
        if(isset($request->filter)){
            $Query = User::select('*')->where([
                        cn::USERS_SCHOOL_ID_COL => Auth::user()->{cn::USERS_SCHOOL_ID_COL},
                        cn::USERS_ROLE_ID_COL => cn::STUDENT_ROLE_ID
                    ]);

            // For Only search text in Field in filtration
            if(isset($request->searchtext) && !empty($request->searchtext)){
                $Query->where(function($query) use ($request){
                    $query->Where(cn::USERS_EMAIL_COL,'like','%'.$request->searchtext.'%')
                    ->orWhere(cn::USERS_NAME_EN_COL,'like','%'.$this->encrypt($request->searchtext).'%')
                    ->orWhere(cn::USERS_NAME_CH_COL,'like','%'.$this->encrypt($request->searchtext).'%');
                });
            }
            
            if(isset($request->student_grade_id) && !empty($request->student_grade_id) && $request->student_grade_id=='all'){
                // $studentIdsArray = CurriculumYearStudentMappings::where([
                //                         cn::CURRICULUM_YEAR_STUDENT_MAPPING_CURRICULUM_YEAR_ID_COL => $this->GetCurriculumYear(),
                //                         cn::CURRICULUM_YEAR_STUDENT_MAPPING_SCHOOL_ID_COL => Auth::user()->{cn::USERS_SCHOOL_ID_COL}
                //                     ])
                //                     //->where(cn::CURRICULUM_YEAR_STUDENT_MAPPING_GRADE_ID_COL,$request->student_grade_id)
                //                     ->whereIn(cn::CURRICULUM_YEAR_STUDENT_MAPPING_CLASS_ID_COL,$gradeClassId)
                //                     ->pluck(cn::CURRICULUM_YEAR_STUDENT_MAPPING_USER_ID_COL)
                //                     ->toArray();
                $studentIdsArray = $this->curriculum_year_mapping_student_ids('','',Auth::user()->{cn::USERS_SCHOOL_ID_COL});
                if(!empty($studentIdsArray)){
                    $Query->whereIn('id',$studentIdsArray);
                }
                // //$Query->whereIn(cn::USERS_CLASS_ID_COL,$gradeClassId);
            }

            if(isset($request->student_grade_id) && !empty($request->student_grade_id) && $request->student_grade_id!='all'){
                $studentIdsArray = $this->curriculum_year_mapping_student_ids($request->student_grade_id,'',Auth::user()->{cn::USERS_SCHOOL_ID_COL});                 
                $Query->whereIn('id',$studentIdsArray);
            }

            if(isset($request->class_type_id) && !empty($request->class_type_id)){
                // $studentIdsArray = CurriculumYearStudentMappings::where([
                //                     cn::CURRICULUM_YEAR_STUDENT_MAPPING_CURRICULUM_YEAR_ID_COL => $this->GetCurriculumYear(),
                //                     cn::CURRICULUM_YEAR_STUDENT_MAPPING_SCHOOL_ID_COL => Auth::user()->{cn::USERS_SCHOOL_ID_COL}
                //                 ])
                //                 ->whereIn(cn::CURRICULUM_YEAR_STUDENT_MAPPING_CLASS_ID_COL,$request->class_type_id)
                //                 ->pluck(cn::CURRICULUM_YEAR_STUDENT_MAPPING_USER_ID_COL)
                //                 ->toArray();
                $studentIdsArray = $this->curriculum_year_mapping_student_ids('',$request->class_type_id,Auth::user()->{cn::USERS_SCHOOL_ID_COL});
                if(!empty($studentIdsArray)){
                    $Query->whereIn('id',$studentIdsArray);
                }
                //$Query->whereIn(cn::USERS_CLASS_ID_COL,$request->class_type_id);
            }
            $studentList = $Query->orderBy(cn::USERS_ID_COL,'DESC')->get();
           
            $studentIdsArray = [];            
            $studentIdsArray = $this->curriculum_year_mapping_student_ids($gradeid,'',Auth::user()->{cn::USERS_SCHOOL_ID_COL});
            $countStudentData = User::whereIn('id',$studentIdsArray)
                                ->where([
                                    cn::USERS_SCHOOL_ID_COL => Auth::user()->{cn::USERS_SCHOOL_ID_COL},
                                    cn::USERS_ROLE_ID_COL => cn::STUDENT_ROLE_ID
                                ])
                                ->where(cn::USERS_GRADE_ID_COL,'<>','')
                                ->count();
        }
        $view = (string)View::make('backend.peer_group.add_member',compact('gradesList','studentList','countStudentData','classTypeOptions','MemberIds'));
        return $this->sendResponse($view);
    }

    /**
     * USE : Find the member list for add new group member into peer group
     */
    public function getSelectedMemberList(Request $request){
        $gradeid =  array();
        $gradeClassId = array();
        $gradesList = '';
        $classTypeOptions = '';
        if($this->isTeacherLogin()){
            $gradesList = TeachersClassSubjectAssign::where([
                            cn::TEACHER_CLASS_SUBJECT_CURRICULUM_YEAR_ID_COL => $this->GetCurriculumYear(),
                            cn::TEACHER_CLASS_SUBJECT_TEACHER_ID_COL => Auth()->user()->{cn::USERS_ID_COL}
                        ])
                        ->with('getClass')
                        ->get()
                        ->unique(cn::TEACHER_CLASS_SUBJECT_CLASS_ID_COL);
            $gradeid =  TeachersClassSubjectAssign::where([
                            cn::TEACHER_CLASS_SUBJECT_CURRICULUM_YEAR_ID_COL => $this->GetCurriculumYear(),
                            cn::TEACHER_CLASS_SUBJECT_TEACHER_ID_COL => Auth()->user()->{cn::USERS_ID_COL}
                        ])
                        ->pluck(cn::TEACHER_CLASS_SUBJECT_CLASS_ID_COL)
                        ->toArray();
            $gradeClass = TeachersClassSubjectAssign::where([
                            cn::TEACHER_CLASS_SUBJECT_CURRICULUM_YEAR_ID_COL => $this->GetCurriculumYear(),
                            cn::TEACHER_CLASS_SUBJECT_TEACHER_ID_COL => Auth()->user()->{cn::USERS_ID_COL}
                        ])
                        ->pluck(cn::TEACHER_CLASS_SUBJECT_CLASS_NAME_ID_COL)
                        ->toArray();
        }

        if($this->isSchoolLogin()){
            $gradeid = GradeSchoolMappings::where(cn::GRADES_MAPPING_SCHOOL_ID_COL,Auth::user()->{cn::USERS_SCHOOL_ID_COL})
                        ->where(cn::GRADES_MAPPING_CURRICULUM_YEAR_ID_COL,$this->GetCurriculumYear())
                        ->pluck(cn::GRADES_MAPPING_GRADE_ID_COL)
                        ->unique()
                        ->toArray();
            $gradeClass = GradeClassMapping::where(cn::GRADE_CLASS_MAPPING_SCHOOL_ID_COL,Auth::user()->{cn::USERS_SCHOOL_ID_COL})
                            ->where(cn::GRADE_CLASS_MAPPING_CURRICULUM_YEAR_ID_COL,$this->GetCurriculumYear())
                            ->pluck(cn::GRADE_CLASS_MAPPING_ID_COL)
                            ->toArray();
            if(!empty($gradeid) && !empty($gradeClass)){
                // $userData = User::with('grades')
                //             ->whereIn(cn::USERS_GRADE_ID_COL,$gradeid)
                //             ->whereIn(cn::USERS_CLASS_ID_COL,$gradeClass)
                //             ->where([
                //                 cn::USERS_ROLE_ID_COL => cn::STUDENT_ROLE_ID,
                //                 cn::USERS_SCHOOL_ID_COL => Auth::user()->{cn::USERS_SCHOOL_ID_COL}
                //             ]);

                $userData = User::with('grades')
                            ->with(['curriculum_year_mapping' => fn($query) => $query->whereIn([cn::CURRICULUM_YEAR_STUDENT_MAPPING_GRADE_ID_COL => $gradeid, cn::CURRICULUM_YEAR_STUDENT_MAPPING_CLASS_ID_COL => $gradeClass])])
                            ->where([
                                cn::USERS_ROLE_ID_COL => cn::STUDENT_ROLE_ID,
                                cn::USERS_SCHOOL_ID_COL => Auth::user()->{cn::USERS_SCHOOL_ID_COL}
                            ]);

                $gradesList = Grades::whereIn(cn::GRADES_ID_COL,$gradeid)->get();
            }
        }

        if(isset($gradeClass) && !empty($gradeClass)){
            $gradeClass = implode(',', $gradeClass);
            $gradeClassId = explode(',',$gradeClass);
        }
        
        if(isset($request->filter) && isset($request->student_grade_id) && !empty($request->student_grade_id) && $request->student_grade_id!='all'){
            $gradeid = array($request->student_grade_id);
        }
        $classTypeOptions = '';
        $grade_id = empty($request->grade) ? 0 : $request->grade;
        $gradeData = Grades::whereIn(cn::GRADES_ID_COL,$gradeid)->get();
        // $studentList = User::whereIn(cn::USERS_GRADE_ID_COL,$gradeid)
        //                 ->whereIn(cn::USERS_CLASS_ID_COL,$gradeClassId)
        //                 ->where([
        //                     cn::USERS_SCHOOL_ID_COL => Auth::user()->{cn::USERS_SCHOOL_ID_COL},
        //                     cn::USERS_ROLE_ID_COL => cn::STUDENT_ROLE_ID
        //                 ])
        //                 ->where(cn::USERS_GRADE_ID_COL,'<>','')
        //                 ->whereIn(cn::USERS_ID_COL,$request->MemberIds)
        //                 ->get();

        $studentList = User::with(['curriculum_year_mapping' => fn($query) => $query->whereIn(cn::CURRICULUM_YEAR_STUDENT_MAPPING_GRADE_ID_COL,$gradeid)->whereIn(cn::CURRICULUM_YEAR_STUDENT_MAPPING_CLASS_ID_COL,$gradeClassId)])
                        ->where([
                            cn::USERS_SCHOOL_ID_COL => Auth::user()->{cn::USERS_SCHOOL_ID_COL},
                            cn::USERS_ROLE_ID_COL => cn::STUDENT_ROLE_ID
                        ])
                        //->where(cn::USERS_GRADE_ID_COL,'<>','')
                        ->whereIn(cn::USERS_ID_COL,$request->MemberIds)
                        ->get();

        // $countStudentData = User::whereIn(cn::USERS_GRADE_ID_COL,$gradeid)
        //                     ->where([
        //                         cn::USERS_SCHOOL_ID_COL => Auth::user()->{cn::USERS_SCHOOL_ID_COL},
        //                         cn::USERS_ROLE_ID_COL => cn::STUDENT_ROLE_ID
        //                     ])
        //                     ->count();

        $countStudentData = User::with(['curriculum_year_mapping' => fn($query) => $query->whereIn([cn::CURRICULUM_YEAR_STUDENT_MAPPING_GRADE_ID_COL => $gradeid])])
                            ->where([
                                cn::USERS_SCHOOL_ID_COL => Auth::user()->{cn::USERS_SCHOOL_ID_COL},
                                cn::USERS_ROLE_ID_COL => cn::STUDENT_ROLE_ID
                            ])
                            ->count();

        $GradeClassData = GradeClassMapping::whereIn(cn::GRADE_CLASS_MAPPING_GRADE_ID_COL,$gradeid)
                            ->whereIn(cn::GRADE_CLASS_MAPPING_ID_COL,$gradeClassId)
                            ->where([
                                cn::GRADE_CLASS_MAPPING_CURRICULUM_YEAR_ID_COL => $this->GetCurriculumYear(),
                                cn::GRADE_CLASS_MAPPING_SCHOOL_ID_COL => Auth::user()->{cn::USERS_SCHOOL_ID_COL}
                            ])
                            ->get();
        if(!empty($GradeClassData)){
            foreach($GradeClassData as $class){
                $GradeList = Grades::find($class->grade_id);
                $selected='';
                if(isset($request->student_grade_id) && $request->student_grade_id=='all' && !isset($request->class_type_id)){
                    $selected='selected="selected"';
                }else if(!isset($request->student_grade_id) && !isset($request->class_type_id)){
                    $selected='selected="selected"';
                }else if(isset($request->class_type_id) && !empty($request->class_type_id) && in_array($class->id,$request->class_type_id)){
                    $selected='selected="selected"';
                }
                $classTypeOptions .= '<option '.$selected.' value='.strtoupper($class->id).'>'.$GradeList->name.strtoupper($class->name).'</option>';
            }
        }
        if(isset($request->filter)){
            $Query = User::select('*')->where([
                        cn::USERS_SCHOOL_ID_COL => Auth::user()->{cn::USERS_SCHOOL_ID_COL},
                        cn::USERS_ROLE_ID_COL => cn::STUDENT_ROLE_ID                
                    ])
                    ->whereIn(cn::USERS_ID_COL,$request->MemberIds);
           
            // For Only search text in Field in filtration
            if(isset($request->searchtext) && !empty($request->searchtext)){
                $Query->where(function($query) use ($request){
                    $query->Where(cn::USERS_EMAIL_COL,'like','%'.$request->searchtext.'%')
                    ->orWhere(cn::USERS_NAME_EN_COL,'like','%'.$this->encrypt($request->searchtext).'%')
                    ->orWhere(cn::USERS_NAME_CH_COL,'like','%'.$this->encrypt($request->searchtext).'%');
                });
            }
            if(isset($request->student_grade_id) && !empty($request->student_grade_id) && $request->student_grade_id!='all'){
                //$Query->where(cn::USERS_GRADE_ID_COL,$request->student_grade_id)->whereIn(cn::USERS_CLASS_ID_COL,$gradeClassId);
                $Query->whereIn('id',$this->curriculum_year_mapping_student_ids($request->student_grade_id,$gradeClassId));
            }
            if(isset($request->student_grade_id) && !empty($request->student_grade_id) && $request->student_grade_id=='all'){
                //$Query->whereIn(cn::USERS_CLASS_ID_COL,$gradeClassId);
                $Query->whereIn('id',$this->curriculum_year_mapping_student_ids('',$gradeClassId));
            }
            if(isset($request->class_type_id) && !empty($request->class_type_id)){
                //$Query->whereIn(cn::USERS_CLASS_ID_COL,$request->class_type_id);
                $Query->whereIn('id',$this->curriculum_year_mapping_student_ids('',$request->class_type_id));
            }
            $studentList = $Query->orderBy(cn::USERS_ID_COL,'DESC')->get();

            // $countStudentData = User::whereIn(cn::USERS_GRADE_ID_COL,$gradeid)
            //                     ->where([
            //                         cn::USERS_SCHOOL_ID_COL => Auth::user()->{cn::USERS_SCHOOL_ID_COL},
            //                         cn::USERS_ROLE_ID_COL => cn::STUDENT_ROLE_ID
            //                     ])
            //                     ->where(cn::USERS_GRADE_ID_COL,'<>','')
            //                     ->count();

            $countStudentData = User::whereIn('id',$this->curriculum_year_mapping_student_ids($gradeid))
                                ->where([
                                    cn::USERS_SCHOOL_ID_COL => Auth::user()->{cn::USERS_SCHOOL_ID_COL},
                                    cn::USERS_ROLE_ID_COL => cn::STUDENT_ROLE_ID
                                ])
                                ->where(cn::USERS_GRADE_ID_COL,'<>','')
                                ->count();
        }

        $memberIds = $request->MemberIds;
        $view = (string)View::make('backend.peer_group.selected_member_list',compact('gradesList','studentList','countStudentData','classTypeOptions','memberIds'));        
        return $this->sendResponse($view);
    }

    /**
     * Delete Member into existing group member
     */
    public function removeMember(Request $request){
        if(PeerGroupMember::where([
            cn::PEER_GROUP_MEMBERS_CURRICULUM_YEAR_ID_COL => $this->GetCurriculumYear(),
            cn::PEER_GROUP_MEMBERS_PEER_GROUP_ID_COL => $request->peerGroupId,
            cn::PEER_GROUP_MEMBERS_MEMBER_ID_COL => $request->deleteMemberid
        ])->exists()){
            PeerGroupMember::where([
                cn::PEER_GROUP_MEMBERS_CURRICULUM_YEAR_ID_COL => $this->GetCurriculumYear(),
                cn::PEER_GROUP_MEMBERS_PEER_GROUP_ID_COL => $request->peerGroupId,
                cn::PEER_GROUP_MEMBERS_MEMBER_ID_COL => $request->deleteMemberid
            ])->delete();
        }
        return $this->sendResponse([], __('languages.member_deleted_successfully'));
    }

    /**
     * USE : GET PEER GROUP LIST FOR STUDENT PANEL DISPLAY
     */
    public function GetStudentPeerGroupList(Request $request){
        $PeerGroupList = collect();
        $items = $request->items ?? 10;
        $assignGroupIds =   PeerGroupMember::where([
                                cn::PEER_GROUP_MEMBERS_CURRICULUM_YEAR_ID_COL => $this->GetCurriculumYear(),
                                cn::PEER_GROUP_MEMBERS_MEMBER_ID_COL => Auth::user()->{cn::USERS_ID_COL},
                                cn::PEER_GROUP_MEMBERS_STATUS_COL => 1
                            ])
                            ->pluck(cn::PEER_GROUP_MEMBERS_PEER_GROUP_ID_COL);
        if(!empty($assignGroupIds)){
            $PeerGroupList  = PeerGroup::where(cn::PEER_GROUP_CURRICULUM_YEAR_ID_COL,$this->GetCurriculumYear())
                                ->whereIn(cn::PEER_GROUP_ID_COL,$assignGroupIds)
                                ->orderBy(cn::PEER_GROUP_ID_COL,'DESC')
                                ->paginate($items);
        }

        $Query = PeerGroup::where(cn::PEER_GROUP_CURRICULUM_YEAR_ID_COL,$this->GetCurriculumYear())
                    ->whereIn(cn::PEER_GROUP_ID_COL,$assignGroupIds);
        if(isset($request->filter) && !empty($request->filter)){
            if(!empty($request->searchName)){
                $Query->where(cn::PEER_GROUP_GROUP_NAME_COL,'Like','%'.$request->searchName.'%');
            }
            if(isset($request->status)){
                $Query->where(cn::PEER_GROUP_STATUS_COL,$request->status);
            }
            $PeerGroupList = $Query->orderBy(cn::PEER_GROUP_ID_COL,'desc')->paginate($items);
        }
        return view("backend.peer_group.student_peer_group_list",compact('PeerGroupList'));    
    }

    /**
     * USE : Create Auto Peer Group
     */
    public function createViewAutoPeerGroup(){
        $GradeClassData = collect();
        $groupType = $this->getPeerGroupType();
        if($this->isTeacherLogin()){
            $schoolId = $this->isTeacherLogin();
            // Get Teachers Grades
            $TeacherGradeClass = $this->TeacherGradesClassService->getTeacherAssignedGradesClass(Auth::user()->{cn::USERS_SCHOOL_ID_COL}, Auth::user()->{cn::USERS_ID_COL});
            $GradeClassData =   Grades::with(['classes' => fn($query) => $query->whereIn(cn::GRADE_CLASS_MAPPING_ID_COL,$TeacherGradeClass['class'])
                                ->where(cn::GRADE_CLASS_MAPPING_CURRICULUM_YEAR_ID_COL,$this->GetCurriculumYear())
                                ->where([cn::GRADE_CLASS_MAPPING_SCHOOL_ID_COL => $this->isTeacherLogin()])])
                                ->whereIn('id',$TeacherGradeClass['grades'])
                                ->get();

            // Get Peer Group List
            $PeerGroupList = PeerGroup::where([
                                cn::PEER_GROUP_CURRICULUM_YEAR_ID_COL => $this->GetCurriculumYear(),
                                cn::PEER_GROUP_CREATED_BY_USER_ID_COL => Auth()->user()->{cn::USERS_ID_COL},
                                cn::PEER_GROUP_STATUS_COL => '1'
                            ])->get();

            // get student list
            // $StudentList = User::whereIn(cn::USERS_GRADE_ID_COL,$TeacherGradeClass['grades'])
            //                 ->whereIn(cn::USERS_CLASS_ID_COL,$TeacherGradeClass['class'])
            //                 ->where([cn::USERS_ROLE_ID_COL => cn::STUDENT_ROLE_ID,cn::USERS_STATUS_COL => 'active'])
            //                 ->get();

            $StudentList =  User::whereIn('id',$this->curriculum_year_mapping_student_ids($TeacherGradeClass['grades'],$TeacherGradeClass['class']))
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

            $GradeMapping = GradeSchoolMappings::with('grades')
                            ->where(cn::GRADES_MAPPING_CURRICULUM_YEAR_ID_COL,$this->GetCurriculumYear())
                            ->where(cn::GRADES_MAPPING_SCHOOL_ID_COL,$schoolId)
                            ->get()
                            ->pluck(cn::GRADES_MAPPING_GRADE_ID_COL);
            $gradeClass = GradeClassMapping::where(cn::GRADE_CLASS_MAPPING_CURRICULUM_YEAR_ID_COL,$this->GetCurriculumYear())
                            ->where(cn::GRADE_CLASS_MAPPING_SCHOOL_ID_COL,$schoolId)
                            ->whereIn(cn::GRADE_CLASS_MAPPING_GRADE_ID_COL,$GradeMapping)
                            ->pluck(cn::GRADE_CLASS_MAPPING_ID_COL)
                            ->toArray();
            if(isset($gradeClass) && !empty($gradeClass)){
                $gradeClass = implode(',', $gradeClass);
                $gradeClassId = explode(',',$gradeClass);
            }
            $GradeClassData = Grades::with(['classes' => fn($query) => $query->whereIn(cn::GRADE_CLASS_MAPPING_ID_COL,$gradeClassId)->where([cn::GRADE_CLASS_MAPPING_SCHOOL_ID_COL => $schoolId,cn::GRADE_CLASS_MAPPING_CURRICULUM_YEAR_ID_COL => $this->GetCurriculumYear()])])->whereIn(cn::GRADES_ID_COL,$GradeMapping)->get();
            
            // get student list
            $StudentList = User::where(cn::USERS_SCHOOL_ID_COL,Auth::user()->{cn::USERS_SCHOOL_ID_COL})
                            ->where(cn::USERS_ROLE_ID_COL,'=',cn::STUDENT_ROLE_ID)
                            ->with('grades')
                            ->get();

            // Get Peer Group List
            $PeerGroupList = PeerGroup::where([cn::PEER_GROUP_SCHOOL_ID_COL => $schoolId, cn::PEER_GROUP_STATUS_COL => '1'])->get();
        }

        return view("backend.peer_group.auto_peer_group",compact('GradeClassData','StudentList','groupType'));
    }

    /**
     * USE : Create Auto Peer Group
     */
    public function createAutoPeerGroup(Request $request){
        $coded_students_list = [];
        $getFormData  = $request->all();
        $formData = [];
        if($this->isTeacherLogin()){
            $schoolId = $this->isTeacherLogin();
        }
        if($this->isSchoolLogin()){
            $schoolId = $this->isSchoolLogin();
        }
        if($this->isPrincipalLogin()){
            $schoolId = $this->isPrincipalLogin();
        }
        $requestPayloadUser = new Request();
        $requestPayloadUser['uid'] = Auth::user()->id;
        $AdminUserId = array();
        $AdminUserData = $this->CommonController->GetUserInfo($requestPayloadUser);
        if(isset($AdminUserData) && !empty($AdminUserData)){
            $AdminUserId = $AdminUserData->getData()->data;
        }
        parse_str($getFormData['formData'], $formData);
        $oldPrefixGroupName =   PeerGroup::where([
                                    cn::PEER_GROUP_SCHOOL_ID_COL => $schoolId,
                                    cn::PEER_GROUP_GROUP_PREFIX_COL => trim($formData['prefix_group_name'])
                                ])
                                ->get()
                                ->toArray();
        if(isset($oldPrefixGroupName) && !empty($oldPrefixGroupName)){
            return $this->sendError('languages.group_prefix_already_exits', 422);
            exit;
        }
        $userData = User::whereIn('id',$formData['studentIds'])->get();
        foreach($userData as $user){
            $coded_students_list[] = [$user->id, (float)$user->overall_ability];
        }
        $requestPayload = new Request();
        $requestPayload = $requestPayload->replace([
            'coded_students_list' => $coded_students_list,
            'groups' => (int)$formData['no_of_group'],
            'method' => $formData['peer_group_type']
        ]);

        // Call to AI API from AI Server
        $GroupList = $this->AIApiService->Create_Auto_Peer_Groups($requestPayload);
        $GroupListData = array();
        if(isset($GroupList) && !empty($GroupList)){
            $i = 1;
            foreach($GroupList as $Group){
                $GroupMemberData = array();
                $GroupStudentIds = array_column($Group,0);
                $group_name = $formData['prefix_group_name'].'-0'.$i;
                $peerGroupInsert = PeerGroup::create([
                    cn::PEER_GROUP_CURRICULUM_YEAR_ID_COL => $this->GetCurriculumYear(),
                    cn::PEER_GROUP_SCHOOL_ID_COL     =>  Auth::user()->school_id,
                    cn::PEER_GROUP_GROUP_NAME_COL    => $group_name,
                    cn::PEER_GROUP_GROUP_PREFIX_COL  => $formData['prefix_group_name'],
                    cn::PEER_GROUP_SUBJECT_ID_COL    => 1,
                    cn::PEER_GROUP_CREATED_TYPE_COL  => 'auto',
                    cn::PEER_GROUP_AUTO_GROUP_BY_COL => $formData['peer_group_type'],
                    cn::PEER_GROUP_CREATED_BY_USER_ID_COL    => Auth::user()->id, 
                    cn::PEER_GROUP_STATUS_COL                => 1
                ]);
                

                foreach($GroupStudentIds as $memberId){
                    $requestPayloadUser = new Request();
                    $requestPayloadUser['uid']=$memberId;
                    $StudentData=$this->CommonController->GetUserInfo($requestPayloadUser);
                    if(isset($StudentData) && !empty($StudentData)){
                        $StudentDataArray = $StudentData->getData()->data;
                    }
                    $GroupMemberData[] = $StudentDataArray;
                    $peerGroupMemberInsert = PeerGroupMember::create([
                                                                    cn::PEER_GROUP_MEMBERS_PEER_GROUP_ID_COL => $peerGroupInsert->id,
                                                                    cn::PEER_GROUP_MEMBERS_MEMBER_ID_COL     => $memberId,
                                                                    cn::PEER_GROUP_MEMBERS_STATUS_COL        => 1
                                                                ]);
                }

                $GroupListData[] =  array(
                                        'id' => $peerGroupInsert->id,
                                        'group_name' => $group_name,
                                        'group_admin' => $AdminUserId,
                                        'student_list' => $GroupMemberData
                                    );
                $i += 1; 
            }
        }
        return $this->sendResponse($GroupListData, __('languages.peer_group_created_successfully'));
    }

    public function updateGroupIdAutoPeerGroup($id,$group_id){
        //Update Group Details
        $postData = [
            cn::PEER_GROUP_DREAMSCHAT_GROUP_ID => $group_id,
        ];
        PeerGroup::find($id)->update($postData);
    }
}
