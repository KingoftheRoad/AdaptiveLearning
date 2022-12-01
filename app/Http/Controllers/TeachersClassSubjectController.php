<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Constants\DbConstant As cn;
use App\Models\GradeSchoolMappings;
use App\Models\GradeClassMapping;
use App\Models\SubjectSchoolMappings;
use App\Traits\Common;
use App\Traits\ResponseFormat;
use App\Http\Repositories\UsersRepository;
use Exception;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use App\Models\Grades;
use App\Models\TeachersClassSubjectAssign;
use Auth;
use App\Helpers\Helper;

class TeachersClassSubjectController extends Controller
{
    use Common, ResponseFormat;
    
    public function index(Request $request){
        try{
            // Laravel Pagination set in Cookie
            //$this->paginationCookie('TeachersClassSubjectList',$request);
            if(!in_array('teacher_class_and_subject_assign_read', Helper::getPermissions(Auth::user()->{cn::USERS_ID_COL}))) {
               return  redirect(Helper::redirectRoleBasedDashboard(Auth::user()->{cn::USERS_ID_COL}));
            }

            $items = $request->items ?? 10;
            $countData = TeachersClassSubjectAssign::where(cn::TEACHER_CLASS_SUBJECT_TABLE_NAME.'.'.cn::TEACHER_CLASS_SUBJECT_SCHOOL_ID_COL,'=',auth()->user()->school_id)->count();
            $TotalFilterData ='';
            $classGradeName = GradeClassMapping::all();
            $List = TeachersClassSubjectAssign::with('getTeacher')->with('getClass')
                    ->where(cn::TEACHER_CLASS_SUBJECT_TABLE_NAME.'.'.cn::TEACHER_CLASS_SUBJECT_SCHOOL_ID_COL,'=',auth()->user()->school_id)
                    ->orderBy(cn::TEACHER_CLASS_SUBJECT_ID_COL, 'DESC')->sortable()->paginate($items);
            return view('backend.teacherclasssubjectmanagement.list',compact('List','countData','items','TotalFilterData')); 
        } catch (Exception $exception) {
            return back()->withError($exception->getMessage())->withInput();
        }
    }

    public function create(){
        try {
            if(!in_array('teacher_class_and_subject_assign_create', Helper::getPermissions(Auth::user()->{cn::USERS_ID_COL}))) {
               return  redirect(Helper::redirectRoleBasedDashboard(Auth::user()->{cn::USERS_ID_COL}));
            }
            $teacherList = User::where(cn::USERS_SCHOOL_ID_COL,'=',auth()->user()->school_id)->where(cn::USERS_ROLE_ID_COL,cn::TEACHER_ROLE_ID)->get();
            $gradeList = GradeSchoolMappings::with('grades')->where(cn::GRADES_MAPPING_SCHOOL_ID_COL,$this->isSchoolLogin())->get();
            $subjectList = SubjectSchoolMappings::with('subjects')->where(cn::SUBJECT_MAPPING_SCHOOL_ID_COL,$this->isSchoolLogin())->get();
            return view('backend.teacherclasssubjectmanagement.add',compact('teacherList','gradeList','subjectList'));
        } catch (Exception $exception) {
            return back()->withError($exception->getMessage())->withInput();
        }
    }

    public function store(Request $request){
        try {
            if(!in_array('teacher_class_and_subject_assign_create', Helper::getPermissions(Auth::user()->{cn::USERS_ID_COL}))) {
               return  redirect(Helper::redirectRoleBasedDashboard(Auth::user()->{cn::USERS_ID_COL}));
            }
            // Check validation
            $validator = Validator::make($request->all(), TeachersClassSubjectAssign::rules($request, 'create'));
            if ($validator->fails()) {
                return back()->withErrors($validator)->withInput();
            }

            if($request->has('subject_id')){
                foreach($request->subject_id as $subjectid){
                    if($request->has('class_type')){
                        $TeachersClassSubjectAssign = TeachersClassSubjectAssign::where(cn::TEACHER_CLASS_SUBJECT_SCHOOL_ID_COL, auth()->user()->school_id)
                                        ->where(cn::TEACHER_CLASS_SUBJECT_TEACHER_ID_COL,$request->teacher_id)
                                        ->where(cn::TEACHER_CLASS_SUBJECT_CLASS_ID_COL,$request->class_id)
                                        ->where(cn::TEACHER_CLASS_SUBJECT_SUBJECT_ID_COL,$subjectid)
                                        ->first();
                        if(isset($TeachersClassSubjectAssign) && !empty($TeachersClassSubjectAssign)){
                            $existsClasses = explode(',',$TeachersClassSubjectAssign->class_name_id);
                            if($existsClasses && $request->class_type){
                                $diffrentClasses = array_diff($request->class_type, $existsClasses);
                                $newClasses = array_merge($existsClasses,$diffrentClasses);                                
                                $TeachersClassSubjectAssign->update([cn::TEACHER_CLASS_SUBJECT_CLASS_NAME_ID_COL => ($newClasses) ? implode(',',$newClasses) : '']);
                            }else{
                                $PostData = array(
                                    cn::TEACHER_CLASS_SUBJECT_SCHOOL_ID_COL   => auth()->user()->school_id,
                                    cn::TEACHER_CLASS_SUBJECT_TEACHER_ID_COL  => $request->teacher_id,
                                    cn::TEACHER_CLASS_SUBJECT_CLASS_ID_COL    => $request->class_id,
                                    cn::TEACHER_CLASS_SUBJECT_SUBJECT_ID_COL  => $request->has('subject_id') ? implode(',',$request->subject_id) : '',
                                    cn::TEACHER_CLASS_SUBJECT_CLASS_NAME_ID_COL => ($request->class_type) ? implode(',',$request->class_type) : '',
                                    cn::TEACHER_CLASS_SUBJECT_STATUS_COL      => $request->status                    
                                );
                                $TeachersClassSubjectAssign = TeachersClassSubjectAssign::updateOrCreate($PostData);
                            }
                        }else{
                            $PostData = array(
                                cn::TEACHER_CLASS_SUBJECT_SCHOOL_ID_COL   => auth()->user()->school_id,
                                cn::TEACHER_CLASS_SUBJECT_TEACHER_ID_COL  => $request->teacher_id,
                                cn::TEACHER_CLASS_SUBJECT_CLASS_ID_COL    => $request->class_id,
                                cn::TEACHER_CLASS_SUBJECT_SUBJECT_ID_COL  => $request->has('subject_id') ? implode(',',$request->subject_id) : '',
                                cn::TEACHER_CLASS_SUBJECT_CLASS_NAME_ID_COL => ($request->class_type) ? implode(',',$request->class_type) : '',
                                cn::TEACHER_CLASS_SUBJECT_STATUS_COL      => $request->status                    
                            );
                            $TeachersClassSubjectAssign = TeachersClassSubjectAssign::updateOrCreate($PostData);
                        }
                    }
                }
            }

            if(!empty($TeachersClassSubjectAssign)){
                return redirect('teache-class-subject-assign')->with('success_msg', __('languages.teacher_class_subject_assign_successfully'));
            }else{
                return back()->with('error_msg', __('languages.problem_was_occur_please_try_again'));
            }
        } catch (Exception $exception) {
            return back()->withError($exception->getMessage())->withInput();
        }
    }

    public function edit($id){
        try{
            if(!in_array('teacher_class_and_subject_assign_update', Helper::getPermissions(Auth::user()->{cn::USERS_ID_COL}))) {
               return  redirect(Helper::redirectRoleBasedDashboard(Auth::user()->{cn::USERS_ID_COL}));
            }
            $teacherList = User::where(cn::USERS_SCHOOL_ID_COL,'=',auth()->user()->school_id)->where(cn::USERS_ROLE_ID_COL, cn::TEACHER_ROLE_ID)->get();
            $gradeList = GradeSchoolMappings::with('grades')->where(cn::GRADES_MAPPING_SCHOOL_ID_COL,$this->isSchoolLogin())->get();
            $subjectList = SubjectSchoolMappings::with('subjects')->where(cn::SUBJECT_MAPPING_SCHOOL_ID_COL,$this->isSchoolLogin())->get();
            $data = TeachersClassSubjectAssign::find($id);
            $gradeClassData = GradeClassMapping::where([cn::GRADE_CLASS_MAPPING_SCHOOL_ID_COL=>$this->isSchoolLogin(),cn::GRADE_CLASS_MAPPING_GRADE_ID_COL=>$data->class_id])->get();
            return view('backend.teacherclasssubjectmanagement.edit',compact('data','teacherList','gradeList','subjectList','gradeClassData'));
        } catch (Exception $exception) {
            return back()->withError($exception->getMessage())->withInput();
        }
    }

    public function update(Request $request, $id){
        try{
            if(!in_array('teacher_class_and_subject_assign_update', Helper::getPermissions(Auth::user()->{cn::USERS_ID_COL}))) {
               return  redirect(Helper::redirectRoleBasedDashboard(Auth::user()->{cn::USERS_ID_COL}));
            }
            $validator = Validator::make($request->all(), TeachersClassSubjectAssign::rules($request, 'update'));
            if ($validator->fails()) {
                return back()->withErrors($validator)->withInput();
            }
            if($request->has('subject_id')){
                foreach($request->subject_id as $subjectid){
                    if($request->has('class_type')){
                        $TeachersClassSubjectAssign = TeachersClassSubjectAssign::find($id);
                        if(isset($TeachersClassSubjectAssign) && !empty($TeachersClassSubjectAssign)){
                            $existsClasses = explode(',',$TeachersClassSubjectAssign->class_name_id);
                            if($existsClasses && $request->class_type){
                                $TeachersClassSubjectAssign->update([cn::TEACHER_CLASS_SUBJECT_CLASS_NAME_ID_COL => ($request->class_type) ? implode(',',$request->class_type) : '']);
                            }else{
                                $PostData = array(
                                    cn::TEACHER_CLASS_SUBJECT_SCHOOL_ID_COL   => auth()->user()->school_id,
                                    cn::TEACHER_CLASS_SUBJECT_TEACHER_ID_COL  => $request->teacher_id,
                                    cn::TEACHER_CLASS_SUBJECT_CLASS_ID_COL    => $request->class_id,
                                    cn::TEACHER_CLASS_SUBJECT_SUBJECT_ID_COL  => $request->has('subject_id') ? implode(',',$request->subject_id) : '',
                                    cn::TEACHER_CLASS_SUBJECT_CLASS_NAME_ID_COL => ($request->class_type) ? implode(',',$request->class_type) : '',
                                    cn::TEACHER_CLASS_SUBJECT_STATUS_COL      => $request->status                    
                                );
                                $TeachersClassSubjectAssign = TeachersClassSubjectAssign::find($id)->update($PostData);
                            }
                        }else{
                            $PostData = array(
                                cn::TEACHER_CLASS_SUBJECT_SCHOOL_ID_COL   => auth()->user()->school_id,
                                cn::TEACHER_CLASS_SUBJECT_TEACHER_ID_COL  => $request->teacher_id,
                                cn::TEACHER_CLASS_SUBJECT_CLASS_ID_COL    => $request->class_id,
                                cn::TEACHER_CLASS_SUBJECT_SUBJECT_ID_COL  => $request->has('subject_id') ? implode(',',$request->subject_id) : '',
                                cn::TEACHER_CLASS_SUBJECT_CLASS_NAME_ID_COL => ($request->class_type) ? implode(',',$request->class_type) : '',
                                cn::TEACHER_CLASS_SUBJECT_STATUS_COL      => $request->status                    
                            );
                            $TeachersClassSubjectAssign = TeachersClassSubjectAssign::find($id)->update($PostData);
                        }
                    }
                }
            }
            if(!empty($TeachersClassSubjectAssign)){
                return redirect('teache-class-subject-assign')->with('success_msg', __('languages.teacher_class_subject_assign_update_successfully'));
            }else{
                return back()->with('error_msg', __('languages.problem_was_occur_please_try_again'));
            }
        }catch(Exception $exception){
            return $this->sendError($exception->getMessage(), 404);
        }
    }

    public function destroy($id){
        try{
            if(!in_array('teacher_class_and_subject_assign_delete', Helper::getPermissions(Auth::user()->{cn::USERS_ID_COL}))) {
               return  redirect(Helper::redirectRoleBasedDashboard(Auth::user()->{cn::USERS_ID_COL}));
            }
            $TeachersClassSubjectAssign = TeachersClassSubjectAssign::find($id);            
            if($TeachersClassSubjectAssign->delete()){
                 $this->StoreAuditLogFunction('','TeachersClassSubjectAssign','','','Delete Teachers Class Subject Assign ID '.$id,cn::TEACHER_CLASS_SUBJECT_TABLE_NAME,'');
                return $this->sendResponse([], __('languages.deleted_successfully'));
            }else{
                return $this->sendError(__('languages.problem_was_occur_please_try_again'), 422);
            }
        }catch (Exception $exception) {
            return $this->sendError($exception->getMessage(), 404);
        }
    }

    public function chechteacherid(Request $request){
        $data = TeachersClassSubjectAssign::where(cn::TEACHER_CLASS_SUBJECT_TEACHER_ID_COL,$request->teacher_id)->select(cn::TEACHER_CLASS_SUBJECT_CLASS_ID_COL)->get()->toArray();
        return $data;
    }

    /**
     * USE : Get Classlist by grade id
     */
    public function getClassType(Request $request){
        $html ='';
        if(!empty($request->grade_id)){
            if($this->isSchoolLogin()){
                if($request->grade_id == 'all'){
                    $GradeClassMapping = GradeSchoolMappings::where('school_id',Auth::user()->{cn::USERS_SCHOOL_ID_COL})->get();
                }else{
                    $GradeClassMapping = GradeClassMapping::where([
                                            cn::GRADE_CLASS_MAPPING_GRADE_ID_COL => $request->grade_id,
                                            cn::GRADE_CLASS_MAPPING_SCHOOL_ID_COL => Auth::user()->{cn::USERS_SCHOOL_ID_COL}
                                        ])->get();
                }  
            }
            if($this->isPrincipalLogin()){
                $GradeClassMapping = GradeClassMapping::whereIn(cn::GRADE_CLASS_MAPPING_GRADE_ID_COL,$request->grade_id)
                                    ->where(cn::GRADE_CLASS_MAPPING_SCHOOL_ID_COL ,Auth::user()->{cn::USERS_SCHOOL_ID_COL})
                                    ->get();
            }
            if($this->isTeacherLogin()){
                if(is_array($request->grade_id)){
                    $gradeClass = TeachersClassSubjectAssign::where([cn::TEACHER_CLASS_SUBJECT_TEACHER_ID_COL => Auth()->user()->{cn::USERS_ID_COL}])->pluck(cn::TEACHER_CLASS_SUBJECT_CLASS_NAME_ID_COL)->toArray();
                    if(isset($gradeClass) && !empty($gradeClass)){
                        $gradeClass = implode(',', $gradeClass);
                        $gradeClassId = explode(',',$gradeClass);
                    }
                    $GradeClassMapping = GradeClassMapping::whereIn(cn::GRADE_CLASS_MAPPING_GRADE_ID_COL,$request->grade_id)->whereIn(cn::GRADE_CLASS_MAPPING_ID_COL,$gradeClassId)->where(cn::GRADE_CLASS_MAPPING_SCHOOL_ID_COL ,$this->isTeacherLogin())->get();
                }else{
                    $grade_id = array($request->grade_id);
                    if($request->grade_id == 'all'){
                        $gradeid = TeachersClassSubjectAssign::where([cn::TEACHER_CLASS_SUBJECT_TEACHER_ID_COL => Auth()->user()->{cn::USERS_ID_COL}])->pluck(cn::TEACHER_CLASS_SUBJECT_CLASS_ID_COL)->toArray();
                        $grade_id = $gradeid;
                    }
                    $gradeClass = TeachersClassSubjectAssign::where([cn::TEACHER_CLASS_SUBJECT_TEACHER_ID_COL => Auth()->user()->{cn::USERS_ID_COL}])->pluck(cn::TEACHER_CLASS_SUBJECT_CLASS_NAME_ID_COL)->toArray();
                    if(isset($gradeClass) && !empty($gradeClass)){
                        $gradeClass = implode(',', $gradeClass);
                        $gradeClassId = explode(',',$gradeClass);
                    }
                    $GradeClassMapping = GradeClassMapping::whereIn(cn::GRADE_CLASS_MAPPING_ID_COL,$gradeClassId)->whereIn(cn::GRADE_CLASS_MAPPING_GRADE_ID_COL, $grade_id)->where([cn::GRADE_CLASS_MAPPING_SCHOOL_ID_COL => $this->isTeacherLogin()])->get();
                }
            }
            if($this->isAdmin()){
                $GradeClassMapping = GradeClassMapping::where([cn::GRADE_CLASS_MAPPING_GRADE_ID_COL => $request->grade_id, cn::GRADE_CLASS_MAPPING_SCHOOL_ID_COL => $request->schoolid])->get();
            }
        }
        if(!empty($GradeClassMapping)){
            foreach($GradeClassMapping as $class){
                $GradeList = Grades::find($class->grade_id);
                if($request->grade_id=="all"){
                    $html .= '<option value='.strtoupper($class->id).' selected>'.$GradeList->name.strtoupper($class->name).'</option>';
                }else{
                    $html .= '<option value='.strtoupper($class->id). '>'.$GradeList->name.strtoupper($class->name).'</option>';
                }
                
            }
        }
        return $this->sendResponse($html, '');
    }
}