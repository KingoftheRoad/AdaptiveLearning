<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Traits\Common;
use App\Traits\ResponseFormat;
use App\Constants\DbConstant As cn;
use App\Models\StudentGroup;
use App\Models\User;
use App\Models\School;
use App\Models\Grades;
use Exception;
use App\Models\Exam;
use App\Models\AttemptExams;
use App\Helpers\Helper;
use Auth;

class StudentGroupController extends Controller
{
    // Load Common Traits
    use Common, ResponseFormat;

    public function index(Request $request){
        try{
            //  Laravel Pagination set in Cookie
            //$this->paginationCookie('StudentGroupList',$request);
            if(!in_array('group_management_read', Helper::getPermissions(Auth::user()->{cn::USERS_ID_COL}))) {
                return  redirect(Helper::redirectRoleBasedDashboard(Auth::user()->{cn::USERS_ID_COL}));
            } 
            $items = $request->items ?? 10; //For Pagination
            $TotalStudentGroupData = StudentGroup::all()->count();
            $Grades = Grades::All()->unique(cn::GRADES_NAME_COL);
            $GroupList = StudentGroup::sortable()->orderBy(cn::STUDENT_GROUP_ID_COL,'DESC')->paginate($items);
            return view('backend/student_group/list',compact('Grades','GroupList','items','TotalStudentGroupData'));
        }catch(\Exception $exception){
            return redirect('studentgroup')->withError($exception->getMessage());
        }
    }

    public function store(Request $request){
        try {
            if(!in_array('group_management_create', Helper::getPermissions(Auth::user()->{cn::USERS_ID_COL}))) {
                return  redirect(Helper::redirectRoleBasedDashboard(Auth::user()->{cn::USERS_ID_COL}));
            }
            $result = StudentGroup::create([
                cn::STUDENT_GROUP_GRADE_ID_COL => $request->{cn::STUDENT_GROUP_GRADE_ID_COL},
                cn::STUDENT_GROUP_NAME_COL => $request->{cn::STUDENT_GROUP_NAME_COL},
                cn::STUDENT_GROUP_STATUS_COL => $request->{cn::STUDENT_GROUP_STATUS_COL}
            ]);
            if($result){
                $this->StoreAuditLogFunction($request->all(),'StudentGroup',cn::STUDENT_GROUP_ID_COL,'','Create Student Group',cn::STUDENT_GROUP_TABLE_NAME,'');
                return $this->sendResponse($result, __('languages.group_created_successfully'));
            }else{
                return $this->sendError(__('languages.problem_was_occur_please_try_again'), 422);
            }
        } catch (\Exception $ex) {
            return $this->sendError($ex->getMessage(), 404);
        }
    }

    public function edit($id){
        try {
            if(!in_array('group_management_update', Helper::getPermissions(Auth::user()->{cn::USERS_ID_COL}))) {
                return  redirect(Helper::redirectRoleBasedDashboard(Auth::user()->{cn::USERS_ID_COL}));
            } 
            $StudentGroup = StudentGroup::find($id);
            if($StudentGroup){
                return $this->sendResponse($StudentGroup);
            }else{
                return $this->sendError(__('languages.data_not_found'), 422);
            }
        } catch (\Exception $ex) {
            return $this->sendError($ex->getMessage(), 404);
        }
    }

    public function update(Request $request, $id){
        try {
            if(!in_array('group_management_update', Helper::getPermissions(Auth::user()->{cn::USERS_ID_COL}))) {
                return  redirect(Helper::redirectRoleBasedDashboard(Auth::user()->{cn::USERS_ID_COL}));
            }
            $this->StoreAuditLogFunction($request->all(),'StudentGroup',cn::STUDENT_GROUP_ID_COL,$id,'Updated Student Group',cn::STUDENT_GROUP_TABLE_NAME,'');
            $result = StudentGroup::find($id)->update([
                cn::STUDENT_GROUP_GRADE_ID_COL => $request->{cn::STUDENT_GROUP_GRADE_ID_COL},
                cn::STUDENT_GROUP_NAME_COL => $request->{cn::STUDENT_GROUP_NAME_COL},
                cn::STUDENT_GROUP_STATUS_COL => $request->{cn::STUDENT_GROUP_STATUS_COL}
            ]);
            if($result){
                return $this->sendResponse($result, __('languages.group_updated_successfully'));
            }else{
                return $this->sendError(__('languages.problem_was_occur_please_try_again'), 422);
            }
        } catch (\Exception $ex) {
            return $this->sendError($ex->getMessage(), 404);
        }
    }

    public function destroy($id){
        try{
            if($id){
                if(!in_array('group_management_delete', Helper::getPermissions(Auth::user()->{cn::USERS_ID_COL}))) {
                    return  redirect(Helper::redirectRoleBasedDashboard(Auth::user()->{cn::USERS_ID_COL}));
                } 
                $result = StudentGroup::find($id)->delete();
                if($result){
                    $this->StoreAuditLogFunction('','StudentGroup','','','Delete Student group ID '.$id,cn::STUDENT_GROUP_TABLE_NAME,'');
                    return $this->sendResponse($result, __('languages.group_deleted_successfully'));
                }else{
                    return $this->sendError(__('languages.problem_was_occur_please_try_again'), 422);
                }
            }else{
                return $this->sendError('Id not found', 422);
            }
        } catch (\Exception $ex) {
            return $this->sendError($ex->getMessage(), 404);
        }
    }

    /**
     * USE : Create from student in to existing groups
     */
    public function CreateFormAddStudentInGroup(Request $request, $id){
        try {
            $items = $request->items ?? 10;
            $TotalStudentData = User::all()->count();
            $checked = '';
            $SchoolData = School::all();
            $studentList = User::where(cn::USERS_ROLE_ID_COL,cn::STUDENT_ROLE_ID)->paginate($items);
            $StudentGroupData = StudentGroup::find($id);
            if(isset($studentList) && isset($StudentGroupData) && !empty($StudentGroupData->{cn::STUDENT_GROUP_STUDENT_ID_COL})){
                $existingStudentids = explode(',',$StudentGroupData->{cn::STUDENT_GROUP_STUDENT_ID_COL});
                $students = array_column($studentList->toArray(),cn::USERS_ID_COL);
                sort($existingStudentids);
                sort($students);
                if($existingStudentids == $students){                    
                    $checked = 'checked';
                }
            }
            if(isset($request->filter)){
                $SearchStudentQuery = User::select('*');
                // Search by schools
                if(isset($request->School) && !empty($request->School)){
                    $SearchStudentQuery->where(cn::USERS_SCHOOL_ID_COL,$request->School);
                }
                // Search by student name
                if(isset($request->name) && !empty($request->name)){
                    $SearchStudentQuery->where(cn::USERS_NAME_EN_COL,'like','%'.$this->encrypt($request->name).'%')->orwhere(cn::USERS_NAME_CH_COL,'like','%'.$this->encrypt($request->name).'%');
                }
                $studentList = $SearchStudentQuery->paginate($items);
            }
            return view('backend.student_group.add',compact('studentList','StudentGroupData','checked','items','TotalStudentData','SchoolData'));
        } catch (\Exception $exception) {
            return redirect('studentgroup/create-student/'.$id)->withError($exception->getMessage())->withInput();
        }
    }

    /**
     * USE : Add student into existing groups
     */
    public function AddStudentInGroup(Request $request, $groupId){
        if($groupId){
            $GroupsData = StudentGroup::find($groupId);
            if(isset($GroupsData)){
                if(!empty($GroupsData->{cn::STUDENT_GROUP_STUDENT_ID_COL})){
                    $existingStudents = explode(',',$GroupsData->student_ids);
                    $MergeStudentIds = array_merge($existingStudents,$request->student_ids);
                    $this->StoreAuditLogFunction('','StudentGroup','','','Update Student id in Student Group Exams ID '.implode(',',array_unique($MergeStudentIds)),cn::STUDENT_GROUP_TABLE_NAME,'');
                    $save = StudentGroup::find($groupId)->update([cn::STUDENT_GROUP_STUDENT_ID_COL => implode(',',array_unique($MergeStudentIds))]);
                }else{
                    $this->StoreAuditLogFunction('','StudentGroup','','','Update Student id in Student Group Exams ID '.implode(',',array_unique($request->student_ids)),cn::STUDENT_GROUP_TABLE_NAME,'');
                    $save = StudentGroup::find($groupId)->update([cn::STUDENT_GROUP_STUDENT_ID_COL => implode(',',array_unique($request->student_ids))]);
                }
                // Group Exams into add student
                if(!empty($GroupsData->{cn::STUDENT_GROUP_EXAM_GROP_IDS_COL})){
                    $existingExamIds = explode(',',$GroupsData->{cn::STUDENT_GROUP_EXAM_GROP_IDS_COL});
                    if(!empty($existingExamIds)){
                        foreach($existingExamIds as $examId){
                            $ExamData = Exam::find($examId);
                            if(isset($ExamData)){
                                if(!empty($ExamData->{cn::EXAM_TABLE_STUDENT_IDS_COL})){
                                    $existingExamsStudents = explode(',',$ExamData->{cn::EXAM_TABLE_STUDENT_IDS_COL});
                                    if(!in_array($request->student_ids,$existingExamsStudents)){
                                        $MergeExamStudentIds = array_merge($existingExamsStudents,$request->student_ids);
                                        $this->StoreAuditLogFunction('','Exam','','','Update Student id in exam Exams ID '.implode(',',array_unique($MergeExamStudentIds)),cn::EXAM_TABLE_NAME,'');
                                        Exam::find($examId)->update([cn::EXAM_TABLE_STUDENT_IDS_COL => implode(',',array_unique($MergeExamStudentIds))]);
                                    }
                                }else{
                                    $this->StoreAuditLogFunction('','Exam','','','Update Student id in exam Exams ID '.implode(',',array_unique($request->student_ids)),cn::EXAM_TABLE_NAME,'');
                                    Exam::find($examId)->update([cn::EXAM_TABLE_STUDENT_IDS_COL => implode(',',array_unique($request->student_ids))]);
                                }
                            }
                        }
                    }
                }
            }
            if($save){
                return $this->sendResponse($save, __('languages.student_added_successfully'));
            }else{
                return $this->sendError(__('languages.problem_was_occur_please_try_again'), 422);
            }
        }else{
            return $this->sendError(__('languages.invalid_group_id'), 422);
        }
    }

    /**
     * USE : Add student into existing groups
     */
    public function removeStudentInGroup(Request $request, $groupId){
        if($groupId){
            $save = false;
            $GroupsData = StudentGroup::find($groupId);
            if(isset($GroupsData)){
                if(!empty($GroupsData->{cn::STUDENT_GROUP_STUDENT_ID_COL})){
                    $studentIds = array_unique(explode(',',$GroupsData->{cn::STUDENT_GROUP_STUDENT_ID_COL}));
                    if(!empty($request->student_ids)){
                        foreach($request->student_ids as $studentid){
                            $position = array_search($studentid, $studentIds);
                            unset($studentIds[$position]);       
                        }
                    }
                    if(isset($studentIds) && !empty($studentIds)){
                        $this->StoreAuditLogFunction('','StudentGroup','','','Update Student id in Student Group ID '.$groupId,cn::STUDENT_GROUP_TABLE_NAME,'');
                        $save = StudentGroup::find($groupId)->update([cn::STUDENT_GROUP_STUDENT_ID_COL => implode(',',array_unique($studentIds))]);
                    }else{
                        $this->StoreAuditLogFunction('','StudentGroup','','','Update Student id in Student Group ID '.$groupId,cn::STUDENT_GROUP_TABLE_NAME,'');
                        $save = StudentGroup::find($groupId)->update([cn::STUDENT_GROUP_STUDENT_ID_COL => null]);
                    }
                }else{
                    $this->StoreAuditLogFunction('','StudentGroup','','','Update Student id in Student Group ID '.$groupId,cn::STUDENT_GROUP_TABLE_NAME,'');
                    $save = StudentGroup::find($groupId)->update([cn::STUDENT_GROUP_STUDENT_ID_COL => implode(',',[$request->student_id])]);
                }
            }
            if($save){
                // If we remove the student from the group, then after that we also delete that student from the exam.
                if(!empty($GroupsData->{cn::STUDENT_GROUP_EXAM_GROP_IDS_COL})){
                    $existingExamIds = explode(',',$GroupsData->{cn::STUDENT_GROUP_EXAM_GROP_IDS_COL});
                    if(!empty($existingExamIds)){
                        foreach($existingExamIds as $examId){
                            $ExamData = Exam::find($examId);
                            if(isset($ExamData)){
                                if(!empty($ExamData->{cn::EXAM_TABLE_STUDENT_IDS_COL})){
                                    $existingExamsStudents = explode(',',$ExamData->{cn::EXAM_TABLE_STUDENT_IDS_COL});
                                    if(!empty($request->student_ids)){
                                        foreach($request->student_ids as $Examstudentid){
                                            $position = array_search($Examstudentid, $existingExamsStudents);
                                            unset($existingExamsStudents[$position]);       
                                        }
                                    }
                                    if(isset($existingExamsStudents) && !empty($existingExamsStudents)){
                                        $this->StoreAuditLogFunction('','StudentGroup','','','Update Student id in Student Group ID '.$groupId,cn::STUDENT_GROUP_TABLE_NAME,'');
                                        $save = Exam::find($examId)->update([cn::EXAM_TABLE_STUDENT_IDS_COL => implode(',',array_unique($existingExamsStudents))]);
                                    }else{
                                        $this->StoreAuditLogFunction('','StudentGroup','','','Update Student id in Student Group ID '.$groupId,cn::STUDENT_GROUP_TABLE_NAME,'');
                                        $save = Exam::find($examId)->update([cn::EXAM_TABLE_STUDENT_IDS_COL => null]);
                                    }
                                }else{
                                    Exam::find($examId)->update([cn::EXAM_TABLE_STUDENT_IDS_COL => implode(',',array_unique($request->student_ids))]);
                                }
                                // After removing the student from the group, we delete the attempted exams of that student.
                                $this->StoreAuditLogFunction('','AttemptExams','','','Delet exam_id in Attempt Exams ID '.$examId,cn::ATTEMPT_EXAMS_TABLE_NAME,'');
                                AttemptExams::where('exam_id',$examId)->whereIn('student_id',$request->student_ids)->delete();
                            }
                        }
                    }
                }
                return $this->sendResponse($save, __('languages.student_deleted_successfully'));
            }else{
                return $this->sendError(__('languages.problem_was_occur_please_try_again'), 422);
            }
        }else{
            return $this->sendError(__('languages.invalid_group_id'), 422);
        }
    }
}
