<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Subjects;
use App\Models\Grades;
use App\Models\StrandUnitsObjectivesMappings;
use App\Models\GradeSchoolMappings;
use App\Models\ClassSubjectMapping;
use App\Models\SubjectSchoolMappings;
use App\Constants\DbConstant As cn;
use App\Traits\Common;
use App\Traits\ResponseFormat;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use App\Helpers\Helper;
use Auth;

class SubjectController extends Controller
{
    use Common, ResponseFormat;
    
    public function index(Request $request){
        try{
            //  Laravel Pagination set in Cookie
            //$this->paginationCookie('SchoolSubjectList',$request);
            if(!in_array('subject_management_read', Helper::getPermissions(Auth::user()->{cn::USERS_ID_COL}))) {
                return  redirect(Helper::redirectRoleBasedDashboard(Auth::user()->{cn::USERS_ID_COL}));
            }
            $items = $request->items ?? 10;
            $countData = Subjects::all()->count();
            $TotalFilterData ='';
            $List = SubjectSchoolMappings::with('subjects')->where(cn::SUBJECT_MAPPING_SCHOOL_ID_COL,$this->isSchoolLogin())->orderBy(cn::SUBJECTS_ID_COL, 'DESC')->sortable()->paginate($items);
            if(isset($request->filter)){
                $Query = Subjects::select('*');
                $Query->where(cn::SUBJECTS_SCHOOL_ID_COL,'=',auth()->user()->school_id);
                if(isset($request->subjectname) && !empty($request->subjectname)){
                    $Query->where(cn::SUBJECTS_NAME_COL,'like','%'.$request->subjectname.'%')->orWhere(cn::SUBJECTS_CODE_COL,'like','%'.$request->subjectname.'%');
                }
                if(isset($request->status) && $request->status!=""){
                    $Query->where(cn::SUBJECTS_STATUS_COL,$request->status);
                }
                $TotalFilterData = $Query->count();
                $List = $Query->sortable()->paginate($items);
                $this->StoreAuditLogFunction($request->all(),'Subjects',cn::SUBJECTS_ID_COL,'','Subject Details Filter',cn::SUBJECTS_TABLE_NAME,'');
            }
            return view('backend.subject.list',compact('List','countData','items','TotalFilterData')); 
        } catch (\Exception $exception) {
            return back()->withError($exception->getMessage())->withInput();
        }
    }

    public function create(){
        try {
            if(!in_array('subject_management_create', Helper::getPermissions(Auth::user()->{cn::USERS_ID_COL}))) {
                return  redirect(Helper::redirectRoleBasedDashboard(Auth::user()->{cn::USERS_ID_COL}));
            }
            $classList = GradeSchoolMappings::with('grades')->where(cn::GRADES_SCHOOL_ID_COL,$this->isSchoolLogin())->get();
            return view('backend.subject.add',compact('classList'));
        } catch (\Exception $exception) {
            return back()->withError($exception->getMessage())->withInput();
        }
    }

    public function store(Request $request){
        try {
            if(!in_array('subject_management_create', Helper::getPermissions(Auth::user()->{cn::USERS_ID_COL}))) {
                return  redirect(Helper::redirectRoleBasedDashboard(Auth::user()->{cn::USERS_ID_COL}));
            }
            ini_set('max_execution_time', -1);//for time execution issue
            // Check validation
            $validator = Validator::make($request->all(), Subjects::rules($request, 'create'),Subjects::rulesMessages('create'));
            if ($validator->fails()) {
                return back()->withErrors($validator)->withInput();
            }
            $Subjects ='';
            $PostData = array(
                cn::SUBJECTS_NAME_COL => $request->name,
                cn::SUBJECTS_CODE_COL => $request->code,
                cn::SUBJECTS_STATUS_COL => $request->status
            );
            if(Subjects::where(cn::SUBJECTS_NAME_COL,$request->name)->doesntExist()){
                $Subjects = Subjects::create($PostData);
            }else{
                $Subjects = Subjects::where('name',$request->name)->first();
            }
            if(!empty($Subjects)){
                $this->StoreAuditLogFunction($PostData,'Subjects',cn::SUBJECTS_ID_COL,'','Create Student',cn::SUBJECTS_TABLE_NAME,array(cn::CLASS_SUBJECT_MAPPING_TABLE_NAME));
                if(SubjectSchoolMappings::where([cn::SUBJECT_MAPPING_SCHOOL_ID_COL => $this->isSchoolLogin(),
                cn::SUBJECT_MAPPING_SUBJECT_ID_COL => $Subjects->id])->doesntExist()){
                    SubjectSchoolMappings::create([
                        cn::SUBJECT_MAPPING_SCHOOL_ID_COL => $this->isSchoolLogin(),
                        cn::SUBJECT_MAPPING_SUBJECT_ID_COL => $Subjects->id
                    ]);
                }
                //for a subject mapping code
                if(!empty($request->class_ids)){
                    $gradeId = $request->class_ids;
                    foreach($gradeId as $grade){
                        $Grades = Grades::find($grade);
                        if(ClassSubjectMapping::where([
                            cn::CLASS_SUBJECT_MAPPING_SUBJECT_ID_COL => $Subjects->id,
                            cn::CLASS_SUBJECT_MAPPING_CLASS_ID_COL => $Grades->id,
                            cn::CLASS_SUBJECT_MAPPING_SCHOOL_ID_COL => $this->isSchoolLogin(),
                            cn::CLASS_SUBJECT_MAPPING_STATUS_COL => 1
                        ])){
                            ClassSubjectMapping::create([
                                cn::CLASS_SUBJECT_MAPPING_SUBJECT_ID_COL => $Subjects->id,
                                cn::CLASS_SUBJECT_MAPPING_CLASS_ID_COL => $Grades->id,
                                cn::CLASS_SUBJECT_MAPPING_SCHOOL_ID_COL => $this->isSchoolLogin(),
                                cn::CLASS_SUBJECT_MAPPING_STATUS_COL => 1
                            ]);
                        }
                        // Clone and mapping data
                        $this->StrandUnitObjectivesMappingClone($Grades->id,$Subjects->id);
                    }
                }
                Log::info('Job Success - Redirect success page');
                return redirect('subject')->with('success_msg', __('languages.subject_added_successfully'));
            }else{
                return back()->with('error_msg', __('languages.problem_was_occur_please_try_again'));
            }
        } catch (\Exception $exception) {
            return back()->withError($exception->getMessage())->withInput();
        }
    }

    public function edit($id){
        try{
            if(!in_array('subject_management_update', Helper::getPermissions(Auth::user()->{cn::USERS_ID_COL}))) {
                return  redirect(Helper::redirectRoleBasedDashboard(Auth::user()->{cn::USERS_ID_COL}));
            }
            $data = '';
            $classList = GradeSchoolMappings::with('grades')->where(cn::GRADES_SCHOOL_ID_COL,$this->isSchoolLogin())->get();
            $subjectMappingData = SubjectSchoolMappings::find($id);
            if(!empty($subjectMappingData)){
                $data = Subjects::where([cn::SUBJECTS_ID_COL => $subjectMappingData->subject_id])->first();
            }
            $existingclassIds = array();
            $existingclassIds = ClassSubjectMapping::where(cn::CLASS_SUBJECT_MAPPING_SUBJECT_ID_COL,$subjectMappingData->subject_id)
                                ->where('school_id',$this->isSchoolLogin())
                                ->pluck(cn::CLASS_SUBJECT_MAPPING_CLASS_ID_COL)
                                ->toArray();
            return view('backend.subject.edit',compact('data','classList','existingclassIds'));
        } catch (\Exception $exception) {
            return back()->withError($exception->getMessage())->withInput();
        }
    }

    public function update(Request $request, $id){
        try{
            if(!in_array('subject_management_update', Helper::getPermissions(Auth::user()->{cn::USERS_ID_COL}))) {
                return  redirect(Helper::redirectRoleBasedDashboard(Auth::user()->{cn::USERS_ID_COL}));
            }
            ini_set('max_execution_time', -1);//for time execution issue            
            $validator = Validator::make($request->all(), Subjects::rules($request, 'update', $id),Subjects::rulesMessages('update'));
            if ($validator->fails()) {
                return back()->withErrors($validator)->withInput();
            }
            $PostData = array(
                cn::SUBJECTS_NAME_COL => $request->name,
                cn::SUBJECTS_CODE_COL => $request->code,
                cn::SUBJECTS_STATUS_COL => $request->status
            );
            $this->StoreAuditLogFunction($PostData,'Subjects',cn::SUBJECTS_ID_COL,$id,'Update Subject',cn::SUBJECTS_TABLE_NAME,array(cn::CLASS_SUBJECT_MAPPING_TABLE_NAME));
            $Subjects = Subjects::where(cn::SUBJECTS_ID_COL,$id)->update($PostData);
            if(isset($request->class_ids) && !empty($request->class_ids)){
                $old_data = ClassSubjectMapping::withTrashed()->where(cn::CLASS_SUBJECT_MAPPING_SUBJECT_ID_COL,$id)->whereNotIn(cn::CLASS_SUBJECT_MAPPING_CLASS_ID_COL,$request->class_ids)->get()->toArray();
                $old_data = array_column($old_data,cn::CLASS_SUBJECT_MAPPING_ID_COL);
                ClassSubjectMapping::whereIn(cn::CLASS_SUBJECT_MAPPING_ID_COL,$old_data)->delete();
                ClassSubjectMapping::withTrashed()->whereNotIn(cn::CLASS_SUBJECT_MAPPING_ID_COL,$old_data)->update([cn::DELETED_AT_COL => NULL,cn::CLASS_SUBJECT_MAPPING_SCHOOL_ID_COL => Auth()->user()->school_id]);
                $Subjects = Subjects::where(cn::SUBJECTS_ID_COL,$id)->first();
                $Subjects->class()->syncWithoutDetaching($request->class_ids);
                //for  strand_units_objectives_mappings following code
                if(!empty($request->class_ids)){
                    foreach($request->class_ids as $classId){
                        $Grades = Grades::where(cn::GRADES_NAME_COL,$classId)->first();
                        if(StrandUnitsObjectivesMappings::where([cn::OBJECTIVES_MAPPINGS_GRADE_ID_COL=>$classId,cn::OBJECTIVES_MAPPINGS_SUBJECT_ID_COL=>$Subjects->id])->doesntExist()){
                            // Clone and mapping data
                            $this->StrandUnitObjectivesMappingClone($Grades->id,$Subjects->id);
                        }
                    }
                }
            }else{
                ClassSubjectMapping::where(cn::CLASS_SUBJECT_MAPPING_SUBJECT_ID_COL,$id)->delete();
            }
            if(!empty($Subjects)){
                Log::info('Job Success - Redirect success page');
                return redirect('subject')->with('success_msg', __('languages.subject_updated_successfully'));
            }else{
                return back()->with('error_msg', __('languages.problem_was_occur_please_try_again'));
            }
        }catch(Exception $exception){
            return $this->sendError($exception->getMessage(), 404);
        }
    }

    public function destroy($id){
        try{
            if(!in_array('subject_management_delete', Helper::getPermissions(Auth::user()->{cn::USERS_ID_COL}))) {
                return  redirect(Helper::redirectRoleBasedDashboard(Auth::user()->{cn::USERS_ID_COL}));
            }
            $this->StoreAuditLogFunction('','Subjects','','','Delete Subject ID '.$id,cn::SUBJECTS_TABLE_NAME,'');
            $mappingSubject = SubjectSchoolMappings::find($id);
            $Subjects = Subjects::find($mappingSubject->subject_id);
            if($mappingSubject->delete()){
                StrandUnitsObjectivesMappings::where(cn::OBJECTIVES_MAPPINGS_SUBJECT_ID_COL,$Subjects->id)->delete();
                return $this->sendResponse([], __('languages.subject_deleted_successfully'));
            }else{
                return $this->sendError(__('languages.problem_was_occur_please_try_again'), 422);
            }
        }catch(\Exception $exception){
            return $this->sendError($exception->getMessage(), 404);
        }
    }
}
