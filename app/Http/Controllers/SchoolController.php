<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Traits\ResponseFormat;
use App\Traits\Common;
use App\Models\School;
use App\Models\User;
use App\Models\Grades;
use App\Models\Subjects;
use App\Models\StrandUnitsObjectivesMappings;
use App\Models\GradeSchoolMappings;
use App\Models\SubjectSchoolMappings;
use App\Models\ClassSubjectMapping;
use Illuminate\Support\Facades\Hash;
use App\Jobs\StrandsUnitsMapping;
use App\Constants\DbConstant As cn;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Helpers\Helper;

class SchoolController extends Controller
{
   use Common;

    public function index(Request $request){
        try{
            //  Laravel Pagination set in Cookie
            //$this->paginationCookie('SchoolList',$request);
            
            if(!in_array('school_management_read', Helper::getPermissions(Auth::user()->{cn::USERS_ID_COL}))) {
                return  redirect(Helper::redirectRoleBasedDashboard(Auth::user()->{cn::USERS_ID_COL}));
            } 
            $items = $request->items ?? 10;
            $TotalFilterData = '';
            $countSchoolData = School::all()->count();
            $schoolList = School::sortable()->orderBy(cn::SCHOOL_ID_COLS,'DESC')->paginate($items);
            $statusList = $this->getStatusList();
            //Filteration on School code and School Name
            $Query = School::select('*');
            if(isset($request->filter)){
                if(isset($request->SchoolName) && !empty($request->SchoolName)){
                    $Query->where(cn::SCHOOL_SCHOOL_NAME_COL,'Like','%'.$request->SchoolName.'%');
                }
                if(isset($request->SchoolCode) && !empty($request->SchoolCode)){
                    $Query->where(cn::SCHOOL_SCHOOL_CODE_COL,$request->SchoolCode);
                }
                if(isset($request->Status) && !empty($request->Status)){
                    $Query->where(cn::SCHOOL_SCHOOL_STATUS,$request->Status);
                }
                if(isset($request->SchoolCity) && !empty($request->SchoolCity)){
                    $Query->where(cn::SCHOOL_SCHOOL_CITY,'Like','%'.$request->SchoolCity.'%');
                }
                $TotalFilterData = $Query->count();
                $schoolList = $Query->sortable()->orderBy(cn::SCHOOL_ID_COLS,'DESC')->paginate($items);
            }
            return view('backend.schools.list',compact('schoolList','statusList','items','countSchoolData','TotalFilterData'));
        }catch(Exception $exception){
            return redirect('users')->withError($exception->getMessage())->withInput();
        }
    }

    public function create(){
        try{
            if(!in_array('school_management_create', Helper::getPermissions(Auth::user()->{cn::USERS_ID_COL}))) {
                return  redirect(Helper::redirectRoleBasedDashboard(Auth::user()->{cn::USERS_ID_COL}));
            } 
            return view('backend.schools.add');
        }catch(Exception $exception){
           return back()->with('error_msg', __('languages.problem_was_occur_please_try_again'));
        }
    }

    /**
     * USE : Add new schools
     */
    public function store(Request $request){
        try{
            if(!in_array('school_management_create', Helper::getPermissions(Auth::user()->{cn::USERS_ID_COL}))) {
                return  redirect(Helper::redirectRoleBasedDashboard(Auth::user()->{cn::USERS_ID_COL}));
            } 
            // use for maintain default Main grade maintain
            $Grades = $gradeMappingData =  $Subjects = $subjectMappingData ='';
        
            //  Check validation
            $validator = Validator::make($request->all(), School::rules($request, 'create'), School::rulesMessages('create'));
            if ($validator->fails()) {
                return back()->withErrors($validator)->withInput();
            }
            $PostData = array(
                cn::SCHOOL_SCHOOL_NAME_COL => $this->encrypt($request->school_name_en),
                cn::SCHOOL_SCHOOL_NAME_EN_COL => $this->encrypt($request->school_name_en),
                cn::SCHOOL_SCHOOL_NAME_CH_COL => $this->encrypt($request->school_name_ch),
                cn::SCHOOL_SCHOOL_CODE_COL => $request->school_code,
                cn::SCHOOL_SCHOOL_EMAIL_COL=> $request->email,
                cn::SCHOOL_SCHOOL_ADDRESS  => ($request->address_en) ? $this->encrypt($request->address_en) : null,
                cn::SCHOOL_SCHOOL_ADDRESS_EN_COL  => ($request->address_en) ? $this->encrypt($request->address_en) : null,
                cn::SCHOOL_SCHOOL_ADDRESS_CH_COL  => ($request->address_ch) ? $this->encrypt($request->address_ch) : null,
                cn::SCHOOL_SCHOOL_CITY     => ($request->city) ? $this->encrypt($request->city) : null,
                cn::SCHOOL_SCHOOL_STATUS   => $request->status
            );
            $this->StoreAuditLogFunction($PostData,'School','','','Create School',cn::SCHOOL_TABLE_NAME,'');
            if(User::where(cn::USERS_EMAIL_COL,$request->email)->doesntExist()){
                $Schools = School::create($PostData);
                // when school create default create grade 4 
                $GradeList = Grades::All();
                if($GradeList->isEmpty()){
                    $Grades = Grades::create([cn::GRADES_NAME_COL => cn::DEFAULT_GRADE_NAME,cn::GRADES_CODE_COL => cn::DEFAULT_GRADE_CODE]);
                    if(!empty($Grades)){
                        GradeSchoolMappings::create([cn::GRADES_MAPPING_SCHOOL_ID_COL => $Schools->{cn::SCHOOL_ID_COLS},cn::GRADES_MAPPING_GRADE_ID_COL => $Grades->{cn::GRADES_ID_COL}]);
                        $SubjectsData = Subjects::All();
                        //Subject Table empty then create default mathematics subject
                        if($SubjectsData->isEmpty()){
                            $PostData = array(
                                cn::SUBJECTS_NAME_COL => cn::SUBJECTMATHEMATICS,
                                cn::SUBJECTS_CODE_COL => cn::CODEMATHEMATICS,
                                cn::SUBJECTS_STATUS_COL => 1
                            );
                            $Subjects = Subjects::create($PostData);
                            if(!empty($Subjects)){
                                $ClassSubjectMapping = ClassSubjectMapping::create([cn::CLASS_SUBJECT_MAPPING_SUBJECT_ID_COL=>$Subjects->{cn::SUBJECTS_ID_COL},cn::CLASS_SUBJECT_MAPPING_CLASS_ID_COL=>$Grades->{cn::GRADES_ID_COL},cn::CLASS_SUBJECT_MAPPING_SCHOOL_ID_COL => $Schools->{cn::SCHOOL_ID_COLS},cn::CLASS_SUBJECT_MAPPING_STATUS_COL=>1]);
                                $subjectMapping = SubjectSchoolMappings::create([cn::SUBJECT_MAPPING_SCHOOL_ID_COL=>$Schools->{cn::SCHOOL_ID_COLS},cn::SUBJECT_MAPPING_SUBJECT_ID_COL => $Subjects->{cn::SUBJECTS_ID_COL},cn::SUBJECT_MAPPING_STATUS_COL => 'active']);
                            }
                            StrandsUnitsMapping::dispatch($Grades,$Subjects)->delay(now()->addSeconds(5));
                        }else{
                            $Subjects = Subjects::where(cn::SUBJECTS_NAME_COL,cn::SUBJECTMATHEMATICS)->first();
                            $ClassSubjectMapping = ClassSubjectMapping::create([cn::CLASS_SUBJECT_MAPPING_SUBJECT_ID_COL=>$Subjects->{cn::SUBJECTS_ID_COL},cn::CLASS_SUBJECT_MAPPING_CLASS_ID_COL=>$Grades->{cn::GRADES_ID_COL},cn::CLASS_SUBJECT_MAPPING_SCHOOL_ID_COL => $Schools->{cn::SCHOOL_ID_COLS},cn::CLASS_SUBJECT_MAPPING_STATUS_COL=>1]);
                        }
                    }
                }else if( Grades::where([cn::GRADES_NAME_COL => cn::DEFAULT_GRADE_NAME])->exists()){
                    $Grades = Grades::where(cn::GRADES_NAME_COL,cn::DEFAULT_GRADE_NAME)->first();
                    $gradeMappingData = GradeSchoolMappings::create([cn::GRADES_MAPPING_SCHOOL_ID_COL => $Schools->{cn::SCHOOL_ID_COLS},cn::GRADES_MAPPING_GRADE_ID_COL => $Grades->{cn::GRADES_ID_COL}]);
                    $Subjects = Subjects::where([cn::SUBJECTS_NAME_COL=>cn::SUBJECTMATHEMATICS])->first();
                    if(!empty($Subjects)){
                        $subjectMapping = SubjectSchoolMappings::create([cn::SUBJECT_MAPPING_SCHOOL_ID_COL=>$Schools->{cn::SCHOOL_ID_COLS},cn::SUBJECT_MAPPING_SUBJECT_ID_COL => $Subjects->{cn::SUBJECTS_ID_COL}]);
                        $Subjects = Subjects::where(cn::SUBJECTS_NAME_COL,cn::SUBJECTMATHEMATICS)->first();
                        $ClassSubjectMapping = ClassSubjectMapping::create([cn::CLASS_SUBJECT_MAPPING_SUBJECT_ID_COL=>$Subjects->{cn::SUBJECTS_ID_COL},cn::CLASS_SUBJECT_MAPPING_CLASS_ID_COL=>$Grades->{cn::GRADES_ID_COL},cn::CLASS_SUBJECT_MAPPING_SCHOOL_ID_COL => $Schools->{cn::SCHOOL_ID_COLS},cn::CLASS_SUBJECT_MAPPING_STATUS_COL=>1]);
                    }else{
                        $PostData =array(
                            cn::SUBJECTS_NAME_COL => cn::SUBJECTMATHEMATICS,
                            cn::SUBJECTS_CODE_COL => cn::CODEMATHEMATICS,
                            cn::SUBJECTS_STATUS_COL => 1
                        );
                        $Subjects = Subjects::create($PostData);
                        if(!empty($Subjects)){
                            $ClassSubjectMapping = ClassSubjectMapping::create([cn::CLASS_SUBJECT_MAPPING_SUBJECT_ID_COL=>$Subjects->{cn::SUBJECTS_ID_COL},cn::CLASS_SUBJECT_MAPPING_CLASS_ID_COL=>$Grades->{cn::GRADES_ID_COL},cn::CLASS_SUBJECT_MAPPING_SCHOOL_ID_COL => $Schools->{cn::SCHOOL_ID_COLS},cn::CLASS_SUBJECT_MAPPING_STATUS_COL=>1]);
                            $subjectMapping = SubjectSchoolMappings::create([cn::SUBJECT_MAPPING_SCHOOL_ID_COL=>$Schools->{cn::SCHOOL_ID_COLS},cn::SUBJECT_MAPPING_SUBJECT_ID_COL => $Subjects->{cn::SUBJECTS_ID_COL},cn::SUBJECT_MAPPING_STATUS_COL => 'active']);
                        }
                    }
                    if(!empty($gradeMappingData)){
                        StrandsUnitsMapping::dispatch($Grades,$Subjects)->delay(now()->addSeconds(5));
                    }
                }
            }else{
                return back()->with('error_msg', __('languages.email_already_exists'));
            }
            if(!empty($Schools)){
                $school_add_in_user_table = array(
                    cn::USERS_ROLE_ID_COL   => cn::SCHOOL_ROLE_ID,
                    cn::USERS_SCHOOL_ID_COL => $Schools->{cn::SCHOOL_ID_COLS},
                    cn::USERS_NAME_EN_COL   => $this->encrypt($request->school_name_en),
                    cn::USERS_NAME_CH_COL   => $this->encrypt($request->school_name_ch),
                    cn::USERS_EMAIL_COL     => $request->email,
                    cn::USERS_ADDRESS_COL   => ($request->address_en) ? $this->encrypt($request->address_en) : null,
                    cn::USERS_CITY_COL      => ($request->city) ? $this->encrypt($request->city) : null,
                    cn::USERS_PASSWORD_COL  => Hash::make($request->password),
                    cn::USERS_STATUS_COL    => $request->status,
                    cn::USERS_CREATED_BY_COL => Auth::user()->{cn::USERS_ID_COL}
                );
                $this->StoreAuditLogFunction($school_add_in_user_table,'User','','','Create School',cn::USERS_TABLE_NAME,'');
                $user = User::create($school_add_in_user_table);
                if(!empty($user)){
                    if(isset($request->addAdmins) && !empty($request->addAdmins)){
                        $subadminInserted = $this->createSubAdmin($request, $Schools);
                        if($subadminInserted != 1){
                            return back()->with('error_msg', __('languages.problem_was_occur_please_try_again'));
                        }
                    }
                    Log::info('Job Success - Redirect success page');
                    return redirect('schoolmanagement')->with('success_msg', __('languages.school_added_successfully'));
                }else{
                    return back()->with('error_msg', __('languages.problem_was_occur_please_try_again'));
                }
            }else{
                return back()->with('error_msg', __('languages.problem_was_occur_please_try_again'));
            }
        }catch(Exception $exception){
            return redirect('schoolmanagement')->withError($exception->getMessage())->withInput(); 
        }
    }

    public function createSubAdmin(Request $request, $SchoolData){
        $flag = 1;
        if((!empty($request->subAdminName) && isset($request->subAdminName)) && (!empty($request->subAdminEmail) && isset($request->subAdminEmail)) && (!empty($request->subAdminPassword) && isset($request->subAdminPassword))){
            for ($i = 0; $i < count($request->subAdminName); $i++) {
                if(isset($request->u_id[$i]) &&  !empty($request->u_id[$i])){
                    if(isset($request->subAdminPassword[$i]) && !empty($request->subAdminPassword[$i])){
                        $subAdmin = User::withTrashed()
                            ->where(cn::USERS_ROLE_ID_COL,cn::SCHOOL_ROLE_ID)
                            ->where(cn::USERS_ID_COL,$request->u_id[$i])
                            ->update([
                                cn::USERS_NAME_COL      => $this->encrypt($request->subAdminName[$i]),
                                cn::USERS_NAME_EN_COL   => $this->encrypt($request->subAdminName[$i]),
                                cn::USERS_NAME_CH_COL   => $this->encrypt($request->subAdminNameCh[$i]),
                                cn::USERS_EMAIL_COL     => $request->subAdminEmail[$i],
                                cn::USERS_PASSWORD_COL  => Hash::make($request->subAdminPassword[$i]),
                                cn::USERS_SCHOOL_ID_COL => $SchoolData->{cn::SCHOOL_ID_COLS},
                            ]);
                    }else{
                        $subAdmin = User::where(cn::USERS_ROLE_ID_COL,cn::SCHOOL_ROLE_ID)
                        ->where(cn::USERS_ID_COL,$request->u_id[$i])
                        ->update([
                            cn::USERS_NAME_COL      => $this->encrypt($request->subAdminName[$i]),
                            cn::USERS_NAME_EN_COL   => $this->encrypt($request->subAdminName[$i]),
                            cn::USERS_NAME_CH_COL   => $this->encrypt($request->subAdminNameCh[$i]),
                            cn::USERS_EMAIL_COL     => $request->subAdminEmail[$i],
                            cn::USERS_SCHOOL_ID_COL => $SchoolData->{cn::SCHOOL_ID_COLS},
                        ]);
                    }
                }else{
                    if(User::withTrashed()->where(cn::USERS_ROLE_ID_COL,cn::SCHOOL_ROLE_ID)->where(cn::USERS_EMAIL_COL,$request->subAdminEmail[$i])->exists()){
                        $subAdmin = User::withTrashed()->where(cn::USERS_ROLE_ID_COL,cn::SCHOOL_ROLE_ID)->where(cn::USERS_EMAIL_COL,$request->subAdminEmail[$i])
                            ->update([
                            cn::USERS_ROLE_ID_COL   => cn::SCHOOL_ROLE_ID,
                            cn::USERS_SCHOOL_ID_COL => $SchoolData->{cn::SCHOOL_ID_COLS},
                            cn::USERS_NAME_COL      => $this->encrypt($request->subAdminName[$i]),
                            cn::USERS_NAME_EN_COL   => $this->encrypt($request->subAdminName[$i]),
                            cn::USERS_NAME_CH_COL   => $this->encrypt($request->subAdminNameCh[$i]),
                            cn::USERS_EMAIL_COL     => $request->subAdminEmail[$i],
                            cn::USERS_PASSWORD_COL  => Hash::make($request->subAdminPassword[$i]),
                            cn::USERS_CREATED_BY_COL => Auth::user()->{cn::USERS_ROLE_ID_COL},
                            cn::USERS_STATUS_COL    => $request->status,
                            cn::USERS_DELETED_AT_COL => null
                        ]);
                    }else{
                        $subAdmin = User::create([
                            cn::USERS_ROLE_ID_COL   => cn::SCHOOL_ROLE_ID,
                            cn::USERS_SCHOOL_ID_COL => $SchoolData->{cn::SCHOOL_ID_COLS},
                            cn::USERS_NAME_COL      => $this->encrypt($request->subAdminName[$i]),
                            cn::USERS_NAME_EN_COL   => $this->encrypt($request->subAdminName[$i]),
                            cn::USERS_NAME_CH_COL   => $this->encrypt($request->subAdminNameCh[$i]),
                            cn::USERS_EMAIL_COL     => $request->subAdminEmail[$i],
                            cn::USERS_PASSWORD_COL  => Hash::make($request->subAdminPassword[$i]),
                            cn::USERS_CREATED_BY_COL => Auth::user()->{cn::USERS_ROLE_ID_COL},
                            cn::USERS_STATUS_COL    => $request->status
                        ]);
                    }
                }
                if(empty($subAdmin)){
                    $flag = 0;
                    break;
                }
            }
            if($flag == 1){
                return 1;
            }else{
                return 0;
            }
        }
    }

    /**
     * USE : Get edited record for school
     */
    public function edit($id){
        try{
            if(!in_array('school_management_update', Helper::getPermissions(Auth::user()->{cn::USERS_ID_COL}))) {
                return  redirect(Helper::redirectRoleBasedDashboard(Auth::user()->{cn::USERS_ID_COL}));
            } 
            $Schoolemail['email'] = '';
            $SchoolData = School::where(cn::SCHOOL_ID_COLS,$id)->first();
            //$UserData = User::where(cn::USERS_NAME_COL,$SchoolData->school_name)->first();
            $UserData = User::where(cn::USERS_SCHOOL_ID_COL,$id)->first();
            if(isset($UserData) && !empty($UserData)){
                $Schoolemail['email'] = $UserData->{cn::USERS_EMAIL_COL};
            }
            $UserOtherData = User::where(cn::USERS_SCHOOL_ID_COL,$id)->where(cn::USERS_ROLE_ID_COL,cn::SCHOOL_ROLE_ID)->orderBy(cn::USERS_ID_COL,'asc')->get()->toArray();

            return view('backend.schools.edit',compact('SchoolData','Schoolemail','UserOtherData'));
        }catch(Exception $exception){
            return redirect('schoolmanagement')->withError($exception->getMessage())->withInput(); 
        }
    }

    /**
     * USE : Update School record
     */
    public function update(Request $request, $id){
        try{
            if(!in_array('school_management_update', Helper::getPermissions(Auth::user()->{cn::USERS_ID_COL}))) {
                return  redirect(Helper::redirectRoleBasedDashboard(Auth::user()->{cn::USERS_ID_COL}));
            }
            $validator = Validator::make($request->all(), School::rules($request, 'update'), School::rulesMessages('update'));
            if ($validator->fails()) {
                return back()->withErrors($validator)->withInput();
            }
            $PostData = array(
                cn::SCHOOL_SCHOOL_NAME_COL => $this->encrypt($request->school_name_en),
                cn::SCHOOL_SCHOOL_NAME_EN_COL => $this->encrypt($request->school_name_en),
                cn::SCHOOL_SCHOOL_NAME_CH_COL => $this->encrypt($request->school_name_ch),
                cn::SCHOOL_SCHOOL_CODE_COL => $request->school_code,
                cn::SCHOOL_SCHOOL_EMAIL_COL=> $request->email,
                cn::SCHOOL_SCHOOL_ADDRESS  => ($request->address_en) ? $this->encrypt($request->address_en) : null,
                cn::SCHOOL_SCHOOL_ADDRESS_EN_COL  => ($request->address_en) ? $this->encrypt($request->address_en) : null,
                cn::SCHOOL_SCHOOL_ADDRESS_CH_COL  => ($request->address_ch) ? $this->encrypt($request->address_ch) : null,
                cn::SCHOOL_SCHOOL_CITY     => ($request->city) ? $this->encrypt($request->city) : null,
                cn::SCHOOL_SCHOOL_STATUS   => $request->status
            );
            $this->StoreAuditLogFunction($PostData,'School',cn::SCHOOL_ID_COLS,$id,'Update School',cn::SCHOOL_TABLE_NAME,'');
            $Schools = School::where(cn::SCHOOL_ID_COLS,$id)->update($PostData);
            if(!empty($Schools)){
                $UserData = User::where(cn::USERS_ROLE_ID_COL,cn::SCHOOL_ROLE_ID)->where(cn::USERS_SCHOOL_ID_COL,$id)->first();
                if(User::where(cn::USERS_ROLE_ID_COL,cn::SCHOOL_ROLE_ID)->where(cn::USERS_SCHOOL_ID_COL,$id)->where(cn::USERS_ID_COL,$UserData->{cn::USERS_ID_COL})->exists()){
                    $school_add_in_user_table = array(
                        cn::USERS_ROLE_ID_COL   => cn::SCHOOL_ROLE_ID,
                        cn::USERS_SCHOOL_ID_COL => $id,
                        cn::USERS_NAME_COL      => $this->encrypt($request->school_name_en),
                        cn::USERS_EMAIL_COL     => $request->email,
                        cn::USERS_ADDRESS_COL   => ($request->address) ? $this->encrypt($request->address_en) : null,
                        cn::USERS_CITY_COL      => ($request->city) ? $this->encrypt($request->city) : null,
                        cn::USERS_STATUS_COL    => $request->status
                    );
                    $this->StoreAuditLogFunction($school_add_in_user_table,'User',cn::USERS_SCHOOL_ID_COL,$id,'Update School',cn::USERS_TABLE_NAME,'');
                    $update = User::where(cn::USERS_ROLE_ID_COL,cn::SCHOOL_ROLE_ID)->where(cn::USERS_SCHOOL_ID_COL,$id)->where(cn::USERS_ID_COL,$UserData->{cn::USERS_ID_COL})->update($school_add_in_user_table);
                }else{
                    $school_add_in_user_table = array(
                        cn::USERS_ROLE_ID_COL   => cn::SCHOOL_ROLE_ID,
                        cn::USERS_SCHOOL_ID_COL => $id,
                        cn::USERS_PASSWORD_COL  => Hash::make(123456),
                        cn::USERS_NAME_COL      => $this->encrypt($request->school_name_en),//$request->school_name,
                        cn::USERS_EMAIL_COL     => $request->email,
                        cn::USERS_ADDRESS_COL   => ($request->address_en) ? $this->encrypt($request->address_en) : null,
                        cn::USERS_CITY_COL      => ($request->city) ? $this->encrypt($request->city) : null,
                        cn::USERS_STATUS_COL    => $request->status
                    );
                    $this->StoreAuditLogFunction($school_add_in_user_table,'User',cn::USERS_SCHOOL_ID_COL,$id,'Update School',cn::USERS_TABLE_NAME,'');
                    $update = User::where(cn::USERS_ROLE_ID_COL,cn::SCHOOL_ROLE_ID)->where(cn::USERS_SCHOOL_ID_COL,$id)->create($school_add_in_user_table);
                }
                if(!empty($update)){
                    $SchoolData = School::where(cn::SCHOOL_ID_COLS,$id)->first();
                    // Create subadmin into school panel
                    if(isset($request->addAdmins) && !empty($request->addAdmins)){
                        $this->createSubAdmin($request, $SchoolData);
                    }
                    return redirect('schoolmanagement')->with('success_msg', __('languages.school_updated_successfully'));
                }else{
                    return back()->with('error_msg', __('languages.problem_was_occur_please_try_again'));
                }
            }else{
                return back()->with('error_msg', __('languages.problem_was_occur_please_try_again'));
            }
        }catch(Exception $exception){
            return $this->sendError($exception->getMessage(), 404);
        }
    }

    public function destroy($id){
        try{
            if(!in_array('school_management_delete', Helper::getPermissions(Auth::user()->{cn::USERS_ID_COL}))) {
                return  redirect(Helper::redirectRoleBasedDashboard(Auth::user()->{cn::USERS_ID_COL}));
            }
            $School = School::find($id);
            $this->StoreAuditLogFunction('','School','','','Delete School ID '.$id,cn::USERS_TABLE_NAME,'');
            if($School->delete()){
                User::where([cn::USERS_SCHOOL_ID_COL => $id, cn::USERS_ROLE_ID_COL => cn::SCHOOL_ROLE_ID])->delete();
                return $this->sendResponse([], __('languages.school_deleted_successfully'));
            }else{
                return $this->sendError(__('languages.please_try_again'), 422);
            }
        }catch (\Exception $exception) {
            return $this->sendError($exception->getMessage(), 404);
        }
    }
}
