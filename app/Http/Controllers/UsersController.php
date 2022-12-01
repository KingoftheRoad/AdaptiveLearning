<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Grades;
use App\Models\School;
use App\Models\Role;
use App\Models\OtherRoles;
use App\Models\GradeClassMapping;
use App\Models\GradeSchoolMappings;
use App\Models\ParentChildMapping;
use App\Models\CurriculumYearStudentMappings;
use App\Models\CurriculumYear;
use App\Constants\DbConstant As cn;
use App\Traits\Common;
use App\Traits\ResponseFormat;
use App\Http\Repositories\UsersRepository;
use Illuminate\Support\Facades\Crypt;
use Exception;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Rules\MatchOldPassword;
use App\Helpers\Helper;

class UsersController extends Controller
{
    use Common, ResponseFormat;

    protected $UsersRepository;

    public function __construct(){
        $this->UsersRepository = new UsersRepository();
    }

    public function index(Request $request){
        try{
            //  Laravel Pagination set in Cookie
            //$this->paginationCookie('UserList',$request);
            if(!in_array('user_management_read', Helper::getPermissions(Auth::user()->{cn::USERS_ID_COL}))) {
               return  redirect(Helper::redirectRoleBasedDashboard(Auth::user()->{cn::USERS_ID_COL}));
            }
            $items = $request->items ?? 10;
            
            $UsersList = $this->UsersRepository->getAllUsersList($items);
            $schoolList = School::all();
            $gradeList = Grades::all();
            $roleList = Role::all();
            if(isset($request->filter)){
                $Query = User::select('*');
                //search by school
                if(isset($request->school_id) && !empty($request->school_id)){
                    $Query->where(cn::USERS_SCHOOL_ID_COL,'=',$request->school_id);
                }
                //search by Role
                if(isset($request->Role) && !empty($request->Role)){
                    $Query->where(cn::USERS_ROLE_ID_COL,$request->Role);
                }
                //search by grade
                if(isset($request->grade_id) && !empty($request->grade_id)){
                    $Query->where(cn::USERS_GRADE_ID_COL,'=',$request->grade_id);
                }
                //search by username
                if(isset($request->username) && !empty($request->username)){
                    $Query->where(cn::USERS_NAME_EN_COL,'like','%'.$this->encrypt($request->username).'%');
                    $Query->orWhere(cn::USERS_NAME_CH_COL,'like','%'.$this->encrypt($request->username).'%');
                    $Query->orWhere(cn::USERS_NAME_COL,'like','%'.$request->username.'%');
                }
                if(isset($request->email) && !empty($request->email)){
                    $Query->where(cn::USERS_EMAIL_COL,'like','%'.$request->email.'%');
                }
                $UsersList = $Query->orderBy(cn::USERS_ID_COL,'DESC')->sortable()->paginate($items);
            }
            return view('backend.UsersManagement.list',compact('roleList','UsersList','schoolList','gradeList','items')); 
            
        } catch (\Exception $exception) {
            return redirect('users')->withError($exception->getMessage())->withInput();
        }
    }

    /**
     * USE : Create Users Form
     */
    public function create(){
        try {
            if(!in_array('user_management_create', Helper::getPermissions(Auth::user()->{cn::USERS_ID_COL}))) {
               return  redirect(Helper::redirectRoleBasedDashboard(Auth::user()->{cn::USERS_ID_COL}));
            }
            $Grades = Grades::all();
            $Schools = School::all();
            $Roles = Role::get();
            $SubRoleList = OtherRoles::all();
            return view('backend.UsersManagement.add',compact('Grades','Schools','Roles','SubRoleList'));
        } catch (\Exception $exception) {
            return back()->withError($exception->getMessage())->withInput();
        }
    }

    /**
     * USE : Store Users
     */
    public function store(Request $request){        
        try{
            if(!in_array('user_management_create', Helper::getPermissions(Auth::user()->{cn::USERS_ID_COL}))) {
               return  redirect(Helper::redirectRoleBasedDashboard(Auth::user()->{cn::USERS_ID_COL}));
            }
            // Check validation
            $validator = Validator::make($request->all(), User::rules($request, 'create'), User::rulesMessages('create'));
            if($validator->fails()) {
                return back()->withErrors($validator)->withInput();
            }
            // Store user detail
            $Users = $this->UsersRepository->storeUserDetails($request);
            if($Users){
                if($request->role == 4 && isset($request->student_ids) && !empty($request->student_ids)){
                    $this->StoreAuditLogFunction($request->all(),'User',cn::USERS_ID_COL,'','Create User',cn::USERS_TABLE_NAME,array('parent_child_mapping'));
                    $Users->parentchild()->attach($request->student_ids);
                }else{
                    $this->StoreAuditLogFunction($request->all(),'User',cn::USERS_ID_COL,'','Create User',cn::USERS_TABLE_NAME,'');
                }
                return redirect('users')->with('success_msg', __('languages.user_added_successfully'));
            }else{
                return back()->with('error_msg', __('languages.problem_was_occur_please_try_again'));
            }
        }catch(\Exception $exception) {
            return back()->withError($exception->getMessage())->withInput();
        }
    }

    /**
     * USE : Edit Form Users
     */
    public function edit($id){
        try{
            if(!in_array('user_management_update', Helper::getPermissions(Auth::user()->{cn::USERS_ID_COL}))) {
                return redirect("/");
            }
            $user = User::find($id);
            $Grades = Grades::all();
            $Schools = School::all();
            $Roles = Role::get();
            $SubRoleList = OtherRoles::all();
            $ParentChildMapping = ParentChildMapping::where(cn::PARANT_CHILD_MAPPING_PARENT_ID_COL,$id)->get()->toArray();
            if(!empty($ParentChildMapping)){
                $ParentChildMapping = array_column($ParentChildMapping,cn::PARANT_CHILD_MAPPING_STUDENT_ID_COL);
            }
            return view('backend.UsersManagement.edit',compact('user','Grades','Schools','Roles','ParentChildMapping','SubRoleList'));
           
        }catch(\Exception $exception){
            return back()->withError($exception->getMessage())->withInput();
        }
    }

    /**
     * USE : Update Users detail
     */
    public function update(Request $request, $id){
        try{
            if(!in_array('user_management_update', Helper::getPermissions(Auth::user()->{cn::USERS_ID_COL}))) {
                return redirect("/");
            }
            // Check validation
            $validator = Validator::make($request->all(), User::rules($request, 'update', $id), User::rulesMessages('update'));
            if ($validator->fails()){
                return back()->withErrors($validator)->withInput();
            }
            if($request->role == 4 && isset($request->student_ids) && !empty($request->student_ids)){
                $this->StoreAuditLogFunction($request->all(),'User',cn::USERS_ID_COL,$id,'Update User',cn::USERS_TABLE_NAME,array('parent_child_mapping'));
            }else{
                $this->StoreAuditLogFunction($request->all(),'User',cn::USERS_ID_COL,$id,'Update User',cn::USERS_TABLE_NAME,'');
            }
            // Update user detail
            $Update = $this->UsersRepository->UpdateUserDetails($request, $id);
            if($Update){
                if($request->role == cn::PARENT_ROLE_ID && isset($request->student_ids) && !empty($request->student_ids)){
                    $Users = User::where(cn::USERS_ID_COL,$id)->first();
                    $Users->parentchild()->sync($request->student_ids);
                }
                return redirect('users')->with('success_msg', __('languages.user_updated_successfully'));
            }else{
                return back()->with('error_msg', __('languages.problem_was_occur_please_try_again'));
            }
        }catch(\Exception $exception) {
            return back()->withError($exception->getMessage())->withInput();
        }
    }

    public function destroy($id){
        try{
            if(!in_array('user_management_delete', Helper::getPermissions(Auth::user()->{cn::USERS_ID_COL}))) {
               return  redirect(Helper::redirectRoleBasedDashboard(Auth::user()->{cn::USERS_ID_COL}));
            } 
            $User = User::find($id);
            if($User->delete()){
                $this->StoreAuditLogFunction('','User','','','Delete User ID '.$id,cn::USERS_TABLE_NAME,'');
                return $this->sendResponse([], __('languages.user_deleted_successfully'));
            }else{
                return $this->sendError(__('languages.problem_was_occur_please_try_again'), 422);
            }
        }catch (\Exception $exception) {
            return $this->sendError($exception->getMessage(), 404);
        }
    }
   
    /**
     * USE : Used for get a grade based on school
     */
    public function getGrades(Request $request){
        $grades = array();
        $gradeIds = GradeSchoolMappings::where(cn::GRADES_MAPPING_SCHOOL_ID_COL,$request->school_id)->pluck(cn::GRADES_MAPPING_GRADE_ID_COL);
        if(isset($gradeIds) && !empty($gradeIds)){
            $grades = Grades::whereIn(cn::GRADES_ID_COL, $gradeIds)->get();
        }
        return $this->sendResponse($grades);
    }


    /**
     * USE : Import user using upload csv file
     */
    public function importUsers(Request $request){
        try{
            ini_set('max_execution_time', 1800); // 30 Minutes
            if($request->isMethod('get')){
                $Roles = Role::whereNotIn(cn::ROLES_ROLE_SLUG_COL,['admin'])->get();
                return view('backend.UsersManagement.import_users',compact('Roles'));
            }
            if($request->isMethod('post')){
                $file = $request->file('user_file');
                
                // File Details 
                $filename = $file->getClientOriginalName();
                $fileName_without_ext = \pathinfo($filename, PATHINFO_FILENAME);
                $fileName_with_ext = \pathinfo($filename, PATHINFO_EXTENSION);      
                $filename = $fileName_without_ext.time().'.'.$fileName_with_ext;

                $extension = $file->getClientOriginalExtension();
                $tempPath = $file->getRealPath();
                $fileSize = $file->getSize();
                $mimeType = $file->getMimeType();

                // Valid File Extensions
                $valid_extension = array("csv");

                // 2MB in Bytes
                $maxFileSize = 2097152;
                
                // Check file extension
                if(in_array(strtolower($extension),$valid_extension)){
                    // Check file size
                    if($fileSize <= $maxFileSize){
                        // File upload location
                        $location = 'uploads/import_users';
                        
                        // Upload file
                        $file->move(public_path($location), $filename);

                        // Import CSV to Database
                        $filepath = public_path($location."/".$filename);
                                                                    
                        // Reading file
                        $file = fopen($filepath,"r");
                        $importData_arr = array();
                        $i = 0;
                        
                        while (($filedata = fgetcsv($file, 1000, ",")) !== FALSE) {
                            $num = count($filedata );
                            // Skip first row (Remove below comment if you want to skip the first row)
                            if($i != 0){
                                for ($c=0; $c < $num; $c++) {
                                    $importData_arr[$i][] = $filedata [$c];
                                }   
                            }
                            $i++;
                        }
                        fclose($file);
                        if(isset($importData_arr) && !empty($importData_arr)){
                            // Insert to MySQL database      
                            $school = '';//for school data store
                            $mappingGradeId ='';                    
                            foreach($importData_arr as $importData){
                                if(!empty($importData[4])){
                                    $school = School::where(cn::SCHOOL_SCHOOL_NAME_COL, $importData[4])->first(); //$importData[4] = school id (filteration on School Name.)
                                }
                                // Find grade id
                                $gradeId = null;
                                $gradeData = null;
                                $classStudentNumber = '';
                                if(isset($importData[7]) && !empty($importData[7])){
                                    // $classdata = explode('+',$importData[7]); // $importData[7] = class + class student number
                                    $className = $importData[7] ?? null;
                                    $classStudentNumber =  $importData[8] ?? null;
                                }
                                if(!empty($importData[5])){
                                    $getGradeID = Grades::where([cn::GRADES_NAME_COL => $importData[5]])->first();
                                    if(isset($getGradeID) && !empty($getGradeID)){
                                        $gradeData = GradeSchoolMappings::where([cn::GRADES_MAPPING_GRADE_ID_COL=>$getGradeID->id,cn::GRADES_MAPPING_SCHOOL_ID_COL => $school->id])->first();
                                        if(!empty($gradeData)){
                                            $mappingGradeId = $gradeData->id;
                                        }
                                        else{
                                            //No Grade available.
                                        }
                                    }
                                }
                                
                                // Check users already exists or not
                                $checkUserExists = User::where(cn::USERS_EMAIL_COL,$importData[0])->first();
                                if(empty($checkUserExists)){
                                    User::create([
                                        cn::USERS_ROLE_ID_COL => $request->role,
                                        // cn::USERS_NAME_COL => trim($importData[4]),
                                        cn::USERS_NAME_EN_COL => ($importData[2]) ? $this->encrypt(trim($importData[2])) : null,
                                        cn::USERS_NAME_CH_COL => ($importData[3]) ? $this->encrypt(trim($importData[3])) : null,
                                        cn::USERS_EMAIL_COL =>   ($importData[0]) ? trim($importData[0]) :null,
                                        cn::USERS_PASSWORD_COL => ($importData[1]) ? Hash::make($this->setPassword(trim($importData[1]))) : null,
                                        cn::USERS_SCHOOL_ID_COL => $school->id,
                                        cn::USERS_GRADE_ID_COL => $mappingGradeId,
                                        cn::USERS_STUDENT_NUMBER => ($importData[6]) ? $importData[6] : null,
                                        cn::USERS_CLASS_ID_COL     => $className,
                                        cn::USERS_CLASS_CLASS_STUDENT_NUMBER => ($classStudentNumber) ? $classStudentNumber : null,
                                        cn::USERS_STATUS_COL => 'active'
                                    ]);
                                }else{
                                    User::where(cn::USERS_ID_COL,$checkUserExists->id)->update([
                                        cn::USERS_ROLE_ID_COL => $request->role,
                                        // cn::USERS_NAME_COL => trim($importData[4]),
                                        cn::USERS_NAME_EN_COL => $this->encrypt(trim($importData[2])),
                                        cn::USERS_NAME_CH_COL => $this->encrypt(trim($importData[3])),
                                        cn::USERS_EMAIL_COL => trim($importData[0]),
                                        cn::USERS_PASSWORD_COL => Hash::make($this->setPassword(trim($importData[1]))),
                                        cn::USERS_SCHOOL_ID_COL => $school->id,
                                        cn::USERS_GRADE_ID_COL => $mappingGradeId,
                                        cn::USERS_STUDENT_NUMBER => $importData[6] ?? null,
                                        cn::USERS_CLASS_CLASS_STUDENT_NUMBER => ($classStudentNumber) ? $classStudentNumber : null,
                                        cn::USERS_STATUS_COL => 'active'
                                    ]);
                                }
                            }
                        }
                        $this->StoreAuditLogFunction('','User','','','User Imported successfully. file name '.$filepath,cn::USERS_TABLE_NAME,'');
                        return redirect('users')->with('success_msg', __('languages.user_import_successfully'));
                    }
                }
            }
        } catch (\Exception $exception) {
            return back()->withError($exception->getMessage())->withInput();
        }
    }

    public function getstudentdata(Request $request){
        if (isset($request->gid) && !empty($request->gid) && isset($request->scid) && !empty($request->scid)) {
            $stdata = User::where(cn::USERS_GRADE_ID_COL,'=',$request->gid)->where(cn::USERS_SCHOOL_ID_COL,'=',$request->scid)->where(cn::USERS_ROLE_ID_COL,'=',3)->get()->toArray();
            return $this->sendResponse($stdata);
        }
        return $this->sendResponse([], __('languages.no_student_available'));
    }

    public function getStudentList(Request $request){
        if (isset($request->gid) && !empty($request->gid) && isset($request->scid) && !empty($request->scid)) {
            $st_data = User::where(cn::USERS_GRADE_ID_COL,'=',$request->gid)->where(cn::USERS_SCHOOL_ID_COL,'=',$request->scid)->where(cn::USERS_ROLE_ID_COL,'=',3)->get()->toArray();
            $stdata='';
            $stdata.='<option value="">'.__("languages.select_student").'</option>';
            if(isset($st_data) && !empty($st_data)){
                foreach($st_data as $key => $value){
                    $name_en=\App\Helpers\Helper::decrypt($value['name_en']);
                    $stdata.='<option value="'.$value['id'].'" >'.$name_en.'</option>';
                }
            }
        }
        return $stdata;
    }

    /**
    * USE : Import Assign Student view page
    */
    public function ImportStudents(Request $request){
        try{
            $Grades = '';
            if($request->isMethod('get')){                
                $CurriculumYears = CurriculumYear::IsActiveYear()->get()->toArray();
                $getMappingGradeId = GradeSchoolMappings::where(cn::GRADES_SCHOOL_ID_COL,Auth::user()->{cn::USERS_SCHOOL_ID_COL})->pluck(cn::GRADES_MAPPING_GRADE_ID_COL);
                if(!empty($getMappingGradeId)){
                    $Grades = Grades::whereIn(cn::GRADES_ID_COL, $getMappingGradeId)->get();
                }
                return view('backend.student.import_student',compact('Grades','CurriculumYears'));
            }
            if($request->isMethod('post')){
                $file = $request->file('user_file');
                // File Details 
                $filename = $file->getClientOriginalName();
                $fileName_without_ext = \pathinfo($filename, PATHINFO_FILENAME);
                $fileName_with_ext = \pathinfo($filename, PATHINFO_EXTENSION);      
                $filename = $fileName_without_ext.time().'.'.$fileName_with_ext;

                $extension = $file->getClientOriginalExtension();
                $tempPath = $file->getRealPath();
                $fileSize = $file->getSize();
                $mimeType = $file->getMimeType();

                // Valid File Extensions
                $valid_extension = array("csv");

                // 2MB in Bytes
                $maxFileSize = 2097152;
                
                // Check file extension
                if(in_array(strtolower($extension),$valid_extension)){
                    // Check file size
                    if($fileSize <= $maxFileSize){
                        // File upload location
                        $location = 'uploads/import_students';
                        
                        // Upload file
                        $file->move(public_path($location), $filename);

                        // Import CSV to Database
                        $filepath = public_path($location."/".$filename);
                                                                    
                        // Reading file
                        $file = fopen($filepath,"r");
                        $importData_arr = array();
                        $i = 0;
                        
                        while (($filedata = fgetcsv($file, 1000, ",")) !== FALSE) {
                            $num = count($filedata );
                            // Skip first row (Remove below comment if you want to skip the first row)
                            if($i != 0){
                                for ($c=0; $c < $num; $c++) {
                                    $importData_arr[$i][] = $filedata [$c];
                                }   
                            }
                            $i++;
                        }
                        fclose($file);

                        // Default variable
                        $classId = null;

                        $PostRefrenceNumbers = array_column($importData_arr,'4');

                        // Find the students perment number for this schools
                        $ExistsPermanentReferenceNumbers = User::where(cn::USERS_SCHOOL_ID_COL,Auth::user()->{cn::USERS_SCHOOL_ID_COL})->where(cn::USERS_ROLE_ID_COL,cn::STUDENT_ROLE_ID)->pluck(cn::USERS_PERMANENT_REFERENCE_NUMBER)->toArray();
                        $duplicatePermanentReferenceNumber = implode(',',array_intersect($PostRefrenceNumbers,$ExistsPermanentReferenceNumbers));
                        if(!empty($duplicatePermanentReferenceNumber)){
                            return back()->with('error_msg', 'Duplicated Records ['.$duplicatePermanentReferenceNumber.']');
                        }
                        
                        if(isset($importData_arr) && !empty($importData_arr)){
                            // Insert to MySQL database
                            foreach($importData_arr as $importData){

                                // Find classId by classs name
                                if(isset($importData[5]) && !empty($importData[5])){
                                    // Check grade is already available or not
                                    $Grade = Grades::where(cn::GRADES_NAME_COL,$importData[5])->first();
                                    if(isset($Grade) && !empty($Grade)){
                                        $GradeClassMapping = GradeSchoolMappings::where(cn::GRADES_MAPPING_SCHOOL_ID_COL,Auth::user()->{cn::USERS_SCHOOL_ID_COL})->where(cn::GRADES_MAPPING_GRADE_ID_COL,$Grade->id)->first();
                                        if(isset($GradeClassMapping) && !empty($GradeClassMapping)){
                                            $gradeId = $Grade->id;
                                        }else{
                                            $GradeSchoolMappings = GradeSchoolMappings::create([
                                                'school_id' => Auth::user()->{cn::USERS_SCHOOL_ID_COL},
                                                'grade_id'  => $Grade->id,
                                                'status'    => 'active'
                                            ]);
                                            if($GradeSchoolMappings){
                                                $gradeId = $Grade->id;
                                            }
                                        }
                                    }else{
                                        // If in the syaytem grade is not available then create new grade first
                                        $Grade = Grades::create([
                                            'name' => $importData[5],
                                            'code' => $importData[5],
                                            'status' => 1
                                        ]);
                                        if($Grade){
                                            // Create grade and school mapping
                                            $GradeSchoolMappings = GradeSchoolMappings::create([
                                                'school_id' => Auth::user()->{cn::USERS_SCHOOL_ID_COL},
                                                'grade_id'  => $Grade->id,
                                                'status'    => 'active'
                                            ]);
                                            if($GradeSchoolMappings){
                                                $gradeId = $Grade->id;
                                            }
                                        }
                                    }

                                    // Check class is already available in this school
                                    $ClassData = GradeClassMapping::where(cn::GRADE_CLASS_MAPPING_SCHOOL_ID_COL,$this->isSchoolLogin())->where(cn::GRADE_CLASS_MAPPING_GRADE_ID_COL,$gradeId)->where(cn::GRADE_CLASS_MAPPING_NAME_COL,strtoupper($importData[6]))->first();
                                    if(isset($ClassData) && !empty($ClassData)){
                                        $classId = $ClassData->id;
                                    }else{
                                        // If the class is not available into this school then create new class
                                        $ClassData = GradeClassMapping::create([
                                            'school_id' => $this->isSchoolLogin(),
                                            'grade_id'  => $gradeId,
                                            'name'      => $importData[6],
                                            'status'    => 'active'
                                        ]);
                                        if($ClassData){
                                            $classId = $ClassData->id;
                                        }
                                    }
                                }

                                // Stire one variable into studentNumberWithInClass
                                $studentNumberWithInClass = '';
                                if(isset($importData[7]) && !empty($importData[7])){
                                    if(strlen($importData[7]) == 1){
                                        $studentNumberWithInClass = '0'.$importData[7];
                                    }else{
                                        $studentNumberWithInClass = $importData[7];
                                    }
                                }

                                // check user is already exists or not
                                $checkUserExists = User::where([cn::USERS_EMAIL_COL => $importData[0],cn::USERS_SCHOOL_ID_COL => Auth()->user()->school_id])->first();
                                if(!empty($checkUserExists)){
                                    User::where(cn::USERS_ID_COL,$checkUserExists->id)->update([
                                        cn::USERS_PASSWORD_COL => ($importData[1]) ? Hash::make($this->setPassword(trim($importData[1]))) : null,
                                        cn::USERS_NAME_EN_COL => ($importData[2]) ? $this->encrypt(trim($importData[2])) : null,
                                        cn::USERS_NAME_CH_COL => ($importData[3]) ? $this->encrypt(trim($importData[3])) : null,
                                        cn::USERS_PERMANENT_REFERENCE_NUMBER => ($importData[4]) ? trim($importData[4]) : null,
                                        cn::USERS_GRADE_ID_COL => $gradeId,
                                        cn::USERS_CLASS_ID_COL => $classId,
                                        cn::STUDENT_NUMBER_WITHIN_CLASS => ($studentNumberWithInClass) ? $studentNumberWithInClass : null,
                                        cn::USERS_CLASS => $Grade->name.''.$ClassData->name,
                                        cn::USERS_CLASS_STUDENT_NUMBER => $Grade->name.$ClassData->name.$studentNumberWithInClass,
                                        cn::USERS_SCHOOL_ID_COL => Auth::user()->{cn::USERS_SCHOOL_ID_COL},
                                        cn::USERS_STATUS_COL => 'active',
                                        cn::USERS_IMPORT_DATE_COL => Carbon::now(),
                                        cn::USERS_CREATED_BY_COL => Auth::user()->{cn::USERS_ID_COL}
                                    ]);
                                }else{
                                    // If user is not exists then create new student
                                    User::create([
                                        cn::USERS_EMAIL_COL =>   ($importData[0]) ? trim($importData[0]) :null,
                                        cn::USERS_PASSWORD_COL => ($importData[1]) ? Hash::make($this->setPassword(trim($importData[1]))) : null,
                                        cn::USERS_NAME_EN_COL => ($importData[2]) ? $this->encrypt(trim($importData[2])) : null,
                                        cn::USERS_NAME_CH_COL => ($importData[3]) ? $this->encrypt(trim($importData[3])) : null,
                                        cn::USERS_PERMANENT_REFERENCE_NUMBER => ($importData[4]) ? trim($importData[4]) : null,
                                        cn::USERS_GRADE_ID_COL => $gradeId,
                                        cn::USERS_CLASS_ID_COL => $classId,
                                        cn::STUDENT_NUMBER_WITHIN_CLASS => ($studentNumberWithInClass) ? $studentNumberWithInClass : null,
                                        cn::USERS_CLASS => $Grade->name.''.$ClassData->name,
                                        cn::USERS_CLASS_STUDENT_NUMBER => $Grade->name.$ClassData->name.$studentNumberWithInClass,
                                        cn::USERS_ROLE_ID_COL => cn::STUDENT_ROLE_ID,
                                        cn::USERS_SCHOOL_ID_COL => Auth::user()->{cn::USERS_SCHOOL_ID_COL},
                                        cn::USERS_STATUS_COL => 'active',
                                        cn::USERS_IMPORT_DATE_COL => Carbon::now(),
                                        cn::USERS_CREATED_BY_COL => Auth::user()->{cn::USERS_ID_COL}
                                    ]);
                                }
                            }
                        }
                        $this->StoreAuditLogFunction('','User','','','Student Imported successfully. file name '.$filepath,cn::USERS_TABLE_NAME,'');
                        return redirect('Student')->with('success_msg', __('languages.user_import_successfully'));
                    }
                }
            }
        }catch(Exception $exception){
            return $this->sendError($exception->getMessage(), 404);
        }
    }

    /**
    * USE : Import Assign Student Data Check
    */
    public function ImportStudentsDataCheck(Request $request){
        try{
            $Grades = '';
            $file = $request->file('user_file');
            // File Details 
            $filename = $file->getClientOriginalName();
            $fileName_without_ext = \pathinfo($filename, PATHINFO_FILENAME);
            $fileName_with_ext = \pathinfo($filename, PATHINFO_EXTENSION);      
            $filename = $fileName_without_ext.time().'.'.$fileName_with_ext;

            $extension = $file->getClientOriginalExtension();
            $tempPath = $file->getRealPath();
            $fileSize = $file->getSize();
            $mimeType = $file->getMimeType();

            // Valid File Extensions
            $valid_extension = array("csv");

            // 2MB in Bytes
            $maxFileSize = 2097152;
            
            // Check file extension
            if(in_array(strtolower($extension),$valid_extension)){
                // Check file size
                if($fileSize <= $maxFileSize){
                    // File upload location
                    $location = 'uploads/import_students';
                    
                    // Upload file
                    $file->move(public_path($location), $filename);

                    // Import CSV to Database
                    $filepath = public_path($location."/".$filename);
                                                                
                    // Reading file
                    $file = fopen($filepath,"r");
                    $importData_arr = array();
                    $i = 0;
                    
                    while (($filedata = fgetcsv($file, 1000, ",")) !== FALSE) {
                        $num = count($filedata );
                        // Skip first row (Remove below comment if you want to skip the first row)
                        if($i != 0){
                            for ($c=0; $c < $num; $c++) {
                                $importData_arr[$i][] = $filedata [$c];
                            }   
                        }
                        $i++;
                    }
                    fclose($file);

                    // Default variable
                    $classId = null;
                    $PostRefrenceNumbers = array_column($importData_arr,'4');
                    $PostEmail = array_column($importData_arr,'0');
                    $dataExitsList='';
                    $dataAllList='';
                    $dataList='<div class="row">';
                    foreach($importData_arr as $importData){
                        $oldRecode=0;
                        if(isset($importData[5]) && !empty($importData[5])){
                            // Check grade is already available or not
                            $Grade = Grades::where(cn::GRADES_NAME_COL,$importData[5])->first();
                            if(isset($Grade) && !empty($Grade)){
                                $GradeClassMapping = GradeSchoolMappings::where(cn::GRADES_MAPPING_SCHOOL_ID_COL,Auth::user()->{cn::USERS_SCHOOL_ID_COL})->where(cn::GRADES_MAPPING_GRADE_ID_COL,$Grade->id)->first();
                                if(isset($GradeClassMapping) && !empty($GradeClassMapping)){
                                    $gradeId = $Grade->id;
                                }else{
                                    $GradeSchoolMappings = GradeSchoolMappings::create([
                                        cn::GRADES_MAPPING_SCHOOL_ID_COL => Auth::user()->{cn::USERS_SCHOOL_ID_COL},
                                        cn::GRADES_MAPPING_GRADE_ID_COL  => $Grade->id,
                                        cn::GRADES_MAPPING_STATUS_COL    => 'active'
                                    ]);
                                    if($GradeSchoolMappings){
                                        $gradeId = $Grade->id;
                                    }
                                }
                            }else{
                                // If in the syaytem grade is not available then create new grade first
                                $Grade = Grades::create([
                                    cn::GRADES_NAME_COL => $importData[5],
                                    cn::GRADES_CODE_COL => $importData[5],
                                    cn::GRADES_STATUS_COL => 1
                                ]);
                                if($Grade){
                                    // Create grade and school mapping
                                    $GradeSchoolMappings = GradeSchoolMappings::create([
                                        cn::GRADES_MAPPING_SCHOOL_ID_COL => Auth::user()->{cn::USERS_SCHOOL_ID_COL},
                                        cn::GRADES_MAPPING_GRADE_ID_COL  => $Grade->id,
                                        cn::GRADES_MAPPING_STATUS_COL   => 'active'
                                    ]);
                                    if($GradeSchoolMappings){
                                        $gradeId = $Grade->id;
                                    }
                                }
                            }

                            // Check class is already available in this school
                            $ClassData = GradeClassMapping::where(cn::GRADE_CLASS_MAPPING_SCHOOL_ID_COL,$this->isSchoolLogin())->where(cn::GRADE_CLASS_MAPPING_GRADE_ID_COL,$gradeId)->where(cn::GRADE_CLASS_MAPPING_NAME_COL,strtoupper($importData[6]))->first();
                            if(isset($ClassData) && !empty($ClassData)){
                                $classId = $ClassData->id;
                            }else{
                                // If the class is not available into this school then create new class
                                $ClassData = GradeClassMapping::create([
                                    cn::GRADE_CLASS_MAPPING_SCHOOL_ID_COL => $this->isSchoolLogin(),
                                    cn::GRADE_CLASS_MAPPING_GRADE_ID_COL  => $gradeId,
                                    cn::GRADE_CLASS_MAPPING_NAME_COL      => $importData[6],
                                    cn::GRADE_CLASS_MAPPING_STATUS_COL    => 'active'
                                ]);
                                if($ClassData){
                                    $classId = $ClassData->id;
                                }
                            }
                        }

                        // Stire one variable into studentNumberWithInClass
                        $studentNumberWithInClass = '';
                        if(isset($importData[7]) && !empty($importData[7])){
                            if(strlen($importData[7]) == 1){
                                $studentNumberWithInClass = '0'.$importData[7];
                            }else{
                                $studentNumberWithInClass = $importData[7];
                            }
                        }
                        $usersClassStudentNumber=$Grade->name.$ClassData->name.$studentNumberWithInClass;
                        /*$ExistsPermanentReferenceNumbers = User::where(cn::USERS_SCHOOL_ID_COL,Auth::user()->{cn::USERS_SCHOOL_ID_COL})->where(cn::USERS_ROLE_ID_COL,cn::STUDENT_ROLE_ID)->where(cn::USERS_PERMANENT_REFERENCE_NUMBER,$importData[4])->get()->toArray();
                        if(!empty($ExistsPermanentReferenceNumbers)){
                            $oldRecode=1;
                        }
                        $ExistsPermanentReferenceNumbers = User::where(cn::USERS_SCHOOL_ID_COL,Auth::user()->{cn::USERS_SCHOOL_ID_COL})->where(cn::USERS_ROLE_ID_COL,cn::STUDENT_ROLE_ID)->where(cn::USERS_PERMANENT_REFERENCE_NUMBER,$importData[4])->get()->toArray();
                        if(!empty($ExistsPermanentReferenceNumbers)){
                            $oldRecode=1;
                        }*/
                        $checkUserExists=User::where(cn::USERS_SCHOOL_ID_COL,Auth::user()->{cn::USERS_SCHOOL_ID_COL})->where(function ($query) use($importData,$usersClassStudentNumber){
                                    $query->where(cn::USERS_PERMANENT_REFERENCE_NUMBER,$importData[4])
                                          ->orWhere(cn::USERS_EMAIL_COL,$importData[0])
                                          ->orWhere(cn::USERS_CLASS_STUDENT_NUMBER,$usersClassStudentNumber);
                                })->first();
                        if(!empty($checkUserExists)){
                            $oldRecode=1;
                        }
                        $dataSet='<table class="table table-bordered '.($oldRecode==1 ? 'bg-warning' : '').'">
                                    <tr>
                                        <td>Email:'.$importData[0].'</td>
                                    <tr>
                                    </tr>
                                        <td>Password:'.$importData[1].'</td>
                                    <tr>
                                    </tr>
                                        <td>English Name:'.$importData[2].'</td>
                                    <tr>
                                    </tr>
                                        <td>Chinese Name:'.$importData[3].'</td>
                                    <tr>
                                    </tr>
                                        <td>Permanent Number:'.$importData[4].'</td>
                                    <tr>
                                    </tr>
                                        <td>Grade:'.$importData[4].'</td>
                                    </tr>
                                    </table>';
                        $dataAllList.=$dataSet;
                        if($oldRecode==1)
                        {
                            $dataExitsList.=$dataSet;
                        }
                        
                        
                    }
                    $dataList.='<div class="col-md-6 border-right"><h4>New Records</h4>'.$dataAllList.'</div><div class="col-md-6"><h4>Update Records</h4>'.$dataExitsList.'</div></div>';
                    return $this->sendResponse(['error_msg'=>'','data'=>$dataList]);

                    //$PostClassStudentNumber = array_column($importData_arr,'7');
                    /*
                    // Find the students perment number for this schools
                    $ExistsPermanentReferenceNumbers = User::where(cn::USERS_SCHOOL_ID_COL,Auth::user()->{cn::USERS_SCHOOL_ID_COL})->where(cn::USERS_ROLE_ID_COL,cn::STUDENT_ROLE_ID)->pluck(cn::USERS_PERMANENT_REFERENCE_NUMBER)->toArray();
                    $duplicatePermanentReferenceNumber = implode(',',array_intersect($PostRefrenceNumbers,$ExistsPermanentReferenceNumbers));
                    if(!empty($duplicatePermanentReferenceNumber)){
                        return $this->sendResponse(['error_msg'=>'Duplicated Records ['.$duplicatePermanentReferenceNumber.']','file'=>$filepath]);
                    }
                    else
                    {
                        // Find the students email for this schools
                        $ExistsEmail = User::where(cn::USERS_SCHOOL_ID_COL,Auth::user()->{cn::USERS_SCHOOL_ID_COL})->where(cn::USERS_ROLE_ID_COL,cn::STUDENT_ROLE_ID)->pluck(cn::USERS_EMAIL_COL)->toArray();
                        $duplicateEmail = implode(',',array_intersect($PostEmail,$ExistsEmail));
                        if(!empty($duplicateEmail)){
                            return $this->sendResponse(['error_msg'=>'Duplicated Records ['.$duplicateEmail.']','file'=>$filepath]);
                        }
                        else
                        {
                            foreach($importData_arr as $importData){

                                // Find classId by classs name
                                if(isset($importData[5]) && !empty($importData[5])){
                                    // Check grade is already available or not
                                    $Grade = Grades::where('name',$importData[5])->first();
                                    if(isset($Grade) && !empty($Grade)){
                                        $GradeClassMapping = GradeSchoolMappings::where(cn::GRADES_MAPPING_SCHOOL_ID_COL,Auth::user()->{cn::USERS_SCHOOL_ID_COL})->where(cn::GRADES_MAPPING_GRADE_ID_COL,$Grade->id)->first();
                                        if(isset($GradeClassMapping) && !empty($GradeClassMapping)){
                                            $gradeId = $Grade->id;
                                        }else{
                                            $GradeSchoolMappings = GradeSchoolMappings::create([
                                                'school_id' => Auth::user()->{cn::USERS_SCHOOL_ID_COL},
                                                'grade_id'  => $Grade->id,
                                                'status'    => 'active'
                                            ]);
                                            if($GradeSchoolMappings){
                                                $gradeId = $Grade->id;
                                            }
                                        }
                                    }else{
                                        // If in the syaytem grade is not available then create new grade first
                                        $Grade = Grades::create([
                                            'name' => $importData[5],
                                            'code' => $importData[5],
                                            'status' => 1
                                        ]);
                                        if($Grade){
                                            // Create grade and school mapping
                                            $GradeSchoolMappings = GradeSchoolMappings::create([
                                                'school_id' => Auth::user()->{cn::USERS_SCHOOL_ID_COL},
                                                'grade_id'  => $Grade->id,
                                                'status'    => 'active'
                                            ]);
                                            if($GradeSchoolMappings){
                                                $gradeId = $Grade->id;
                                            }
                                        }
                                    }

                                    // Check class is already available in this school
                                    $ClassData = GradeClassMapping::where(cn::GRADE_CLASS_MAPPING_SCHOOL_ID_COL,$this->isSchoolLogin())->where(cn::GRADE_CLASS_MAPPING_GRADE_ID_COL,$gradeId)->where(cn::GRADE_CLASS_MAPPING_NAME_COL,strtoupper($importData[6]))->first();
                                    if(isset($ClassData) && !empty($ClassData)){
                                        $classId = $ClassData->id;
                                    }else{
                                        // If the class is not available into this school then create new class
                                        $ClassData = GradeClassMapping::create([
                                            'school_id' => $this->isSchoolLogin(),
                                            'grade_id'  => $gradeId,
                                            'name'      => $importData[6],
                                            'status'    => 'active'
                                        ]);
                                        if($ClassData){
                                            $classId = $ClassData->id;
                                        }
                                    }
                                }

                                // Stire one variable into studentNumberWithInClass
                                $studentNumberWithInClass = '';
                                if(isset($importData[7]) && !empty($importData[7])){
                                    if(strlen($importData[7]) == 1){
                                        $studentNumberWithInClass = '0'.$importData[7];
                                    }else{
                                        $studentNumberWithInClass = $importData[7];
                                    }
                                }

                                $PostClassStudentNumber[]=$Grade->name.$ClassData->name.$studentNumberWithInClass;
                            }
                            // Find the students class student number for this schools
                            $ExistsClassStudentNumber = User::where(cn::USERS_SCHOOL_ID_COL,Auth::user()->{cn::USERS_SCHOOL_ID_COL})->where(cn::USERS_ROLE_ID_COL,cn::STUDENT_ROLE_ID)->pluck(cn::USERS_CLASS_STUDENT_NUMBER)->toArray();
                            $duplicateClassStudentNumber = implode(',',array_intersect($PostClassStudentNumber,$ExistsClassStudentNumber));
                            if(!empty($duplicateClassStudentNumber)){
                                return $this->sendResponse(['error_msg'=>'Duplicated Records ['.$duplicateClassStudentNumber.']','file'=>$filepath]);
                            }
                        }
                    }*/
                }
                else
                {
                    return $this->sendResponse(['error_msg'=>'Please file max size is 2MB']);
                }
            }
        }catch(Exception $exception){
            return $this->sendError($exception->getMessage(), 404);
        }
    }

    /**
    * USE : Import Assign Student Data
    */
    public function ImportStudentsData(Request $request){
        try{
            
            $filepath = "";
            $Grades = '';
            if(isset($request->old_file) && $request->old_file!=""){
                $filepath=$request->old_file;
            }else{
                $file = $request->file('user_file');
                // File Details 
                $filename = $file->getClientOriginalName();
                $fileName_without_ext = \pathinfo($filename, PATHINFO_FILENAME);
                $fileName_with_ext = \pathinfo($filename, PATHINFO_EXTENSION);      
                $filename = $fileName_without_ext.time().'.'.$fileName_with_ext;

                $extension = $file->getClientOriginalExtension();
                $tempPath = $file->getRealPath();
                $fileSize = $file->getSize();
                $mimeType = $file->getMimeType();

                // Valid File Extensions
                $valid_extension = array("csv");

                // 2MB in Bytes
                $maxFileSize = 2097152;
                
                // Check file extension
                if(in_array(strtolower($extension),$valid_extension)){
                    // Check file size
                    if($fileSize <= $maxFileSize){
                        // File upload location
                        $location = 'uploads/import_students';
                        
                        // Upload file
                        $file->move(public_path($location), $filename);

                        // Import CSV to Database
                        $filepath = public_path($location."/".$filename);
                    }
                }
            }
            
            if($filepath!=""){
                    // Reading file
                    $file = fopen($filepath,"r");
                    $importData_arr = array();
                    $i = 0;
                    
                    while (($filedata = fgetcsv($file, 1000, ",")) !== FALSE) {
                        $num = count($filedata );
                        // Skip first row (Remove below comment if you want to skip the first row)
                        if($i != 0){
                            for ($c=0; $c < $num; $c++) {
                                $importData_arr[$i][] = $filedata [$c];
                            }   
                        }
                        $i++;
                    }
                    fclose($file);

                    // Default variable
                    $classId = null;
                    
                    if(isset($importData_arr) && !empty($importData_arr)){
                        // Insert to MySQL database
                        foreach($importData_arr as $importData){

                            // Find classId by classs name
                            if(isset($importData[5]) && !empty($importData[5])){
                                // Check grade is already available or not
                                $Grade = Grades::where(cn::GRADES_NAME_COL,$importData[5])->first();
                                if(isset($Grade) && !empty($Grade)){
                                    $GradeClassMapping = GradeSchoolMappings::where(cn::GRADES_MAPPING_SCHOOL_ID_COL,Auth::user()->{cn::USERS_SCHOOL_ID_COL})->where(cn::GRADES_MAPPING_GRADE_ID_COL,$Grade->id)->first();
                                    if(isset($GradeClassMapping) && !empty($GradeClassMapping)){
                                        $gradeId = $Grade->id;
                                    }else{
                                        $GradeSchoolMappings = GradeSchoolMappings::create([
                                            cn::GRADES_MAPPING_SCHOOL_ID_COL => Auth::user()->{cn::USERS_SCHOOL_ID_COL},
                                            cn::GRADES_MAPPING_GRADE_ID_COL  => $Grade->id,
                                            cn::GRADES_MAPPING_STATUS_COL    => 'active'
                                        ]);
                                        if($GradeSchoolMappings){
                                            $gradeId = $Grade->id;
                                        }
                                    }
                                }else{
                                    // If in the syaytem grade is not available then create new grade first
                                    $Grade = Grades::create([
                                        cn::GRADES_NAME_COL => $importData[5],
                                        cn::GRADES_CODE_COL => $importData[5],
                                        cn::GRADES_STATUS_COL => 1
                                    ]);
                                    if($Grade){
                                        // Create grade and school mapping
                                        $GradeSchoolMappings = GradeSchoolMappings::create([
                                            cn::GRADES_MAPPING_SCHOOL_ID_COL => Auth::user()->{cn::USERS_SCHOOL_ID_COL},
                                            cn::GRADES_MAPPING_GRADE_ID_COL  => $Grade->id,
                                            cn::GRADES_MAPPING_STATUS_COL    => 'active'
                                        ]);
                                        if($GradeSchoolMappings){
                                            $gradeId = $Grade->id;
                                        }
                                    }
                                }

                                // Check class is already available in this school
                                $ClassData = GradeClassMapping::where(cn::GRADE_CLASS_MAPPING_SCHOOL_ID_COL,$this->isSchoolLogin())->where(cn::GRADE_CLASS_MAPPING_GRADE_ID_COL,$gradeId)->where(cn::GRADE_CLASS_MAPPING_NAME_COL,strtoupper($importData[6]))->first();
                                if(isset($ClassData) && !empty($ClassData)){
                                    $classId = $ClassData->id;
                                }else{
                                    // If the class is not available into this school then create new class
                                    $ClassData = GradeClassMapping::create([
                                        cn::GRADE_CLASS_MAPPING_SCHOOL_ID_COL => $this->isSchoolLogin(),
                                        cn::GRADE_CLASS_MAPPING_GRADE_ID_COL  => $gradeId,
                                        cn::GRADE_CLASS_MAPPING_NAME_COL      => $importData[6],
                                        cn::GRADE_CLASS_MAPPING_STATUS_COL    => 'active'
                                    ]);
                                    if($ClassData){
                                        $classId = $ClassData->id;
                                    }
                                }
                            }

                            // Stire one variable into studentNumberWithInClass
                            $studentNumberWithInClass = '';
                            if(isset($importData[7]) && !empty($importData[7])){
                                if(strlen($importData[7]) == 1){
                                    $studentNumberWithInClass = '0'.$importData[7];
                                }else{
                                    $studentNumberWithInClass = $importData[7];
                                }
                            }
                            $usersClassStudentNumber = $Grade->name.$ClassData->name.$studentNumberWithInClass;
                            $checkUserExists = User::where(cn::USERS_SCHOOL_ID_COL,Auth::user()->{cn::USERS_SCHOOL_ID_COL})
                                                ->where(function ($query) use($importData,$usersClassStudentNumber){
                                                    $query->where(cn::USERS_PERMANENT_REFERENCE_NUMBER,$importData[4])
                                                    ->orWhere(cn::USERS_EMAIL_COL,$importData[0])
                                                    ->orWhere(cn::USERS_CLASS_STUDENT_NUMBER,$usersClassStudentNumber);
                                                })->first();
                            if(isset($request->action) && $request->action == 2){
                                if(!empty($checkUserExists)){
                                    User::where(cn::USERS_ID_COL,$checkUserExists->id)->update([
                                        cn::USERS_PASSWORD_COL => ($importData[1]) ? Hash::make($this->setPassword(trim($importData[1]))) : null,
                                        cn::USERS_NAME_EN_COL => ($importData[2]) ? $this->encrypt(trim($importData[2])) : null,
                                        cn::USERS_NAME_CH_COL => ($importData[3]) ? $this->encrypt(trim($importData[3])) : null,
                                        cn::USERS_PERMANENT_REFERENCE_NUMBER => ($importData[4]) ? trim($importData[4]) : null,
                                        cn::USERS_GRADE_ID_COL => $gradeId,
                                        cn::USERS_CLASS_ID_COL => $classId,
                                        cn::STUDENT_NUMBER_WITHIN_CLASS => ($studentNumberWithInClass) ? $studentNumberWithInClass : null,
                                        cn::USERS_CLASS => $Grade->name.''.$ClassData->name,
                                        cn::USERS_CLASS_STUDENT_NUMBER => $usersClassStudentNumber,
                                        cn::USERS_SCHOOL_ID_COL => Auth::user()->{cn::USERS_SCHOOL_ID_COL},
                                        cn::USERS_STATUS_COL => 'active',
                                        cn::USERS_IMPORT_DATE_COL => Carbon::now(),
                                        cn::USERS_CREATED_BY_COL => Auth::user()->{cn::USERS_ID_COL}
                                    ]);

                                    CurriculumYearStudentMappings::create([
                                        cn::CURRICULUM_YEAR_STUDENT_MAPPING_USER_ID_COL => $checkUserExists->id,
                                        cn::CURRICULUM_YEAR_STUDENT_MAPPING_CURRICULUM_YEAR_ID_COL => $request->curriculum_year_id,
                                        cn::CURRICULUM_YEAR_STUDENT_MAPPING_SCHOOL_ID_COL => Auth::user()->{cn::USERS_SCHOOL_ID_COL},
                                        cn::CURRICULUM_YEAR_STUDENT_MAPPING_GRADE_ID_COL => $gradeId,
                                        cn::CURRICULUM_YEAR_STUDENT_MAPPING_CLASS_ID_COL => $classId
                                    ]);
                                }
                            }

                            if(empty($checkUserExists)){
                                // If user is not exists then create new student
                                $newUserData = User::create([
                                    cn::USERS_EMAIL_COL =>   ($importData[0]) ? trim($importData[0]) :null,
                                    cn::USERS_PASSWORD_COL => ($importData[1]) ? Hash::make($this->setPassword(trim($importData[1]))) : null,
                                    cn::USERS_NAME_EN_COL => ($importData[2]) ? $this->encrypt(trim($importData[2])) : null,
                                    cn::USERS_NAME_CH_COL => ($importData[3]) ? $this->encrypt(trim($importData[3])) : null,
                                    cn::USERS_PERMANENT_REFERENCE_NUMBER => ($importData[4]) ? trim($importData[4]) : null,
                                    cn::USERS_GRADE_ID_COL => $gradeId,
                                    cn::USERS_CLASS_ID_COL => $classId,
                                    cn::STUDENT_NUMBER_WITHIN_CLASS => ($studentNumberWithInClass) ? $studentNumberWithInClass : null,
                                    cn::USERS_CLASS => $Grade->name.''.$ClassData->name,
                                    cn::USERS_CLASS_STUDENT_NUMBER => $usersClassStudentNumber,
                                    cn::USERS_ROLE_ID_COL => cn::STUDENT_ROLE_ID,
                                    cn::USERS_SCHOOL_ID_COL => Auth::user()->{cn::USERS_SCHOOL_ID_COL},
                                    cn::USERS_STATUS_COL => 'active',
                                    cn::USERS_IMPORT_DATE_COL => Carbon::now(),
                                    cn::USERS_CREATED_BY_COL => Auth::user()->{cn::USERS_ID_COL}
                                ]);

                                CurriculumYearStudentMappings::create([
                                    cn::CURRICULUM_YEAR_STUDENT_MAPPING_USER_ID_COL => $newUserData->id,
                                    cn::CURRICULUM_YEAR_STUDENT_MAPPING_CURRICULUM_YEAR_ID_COL => $request->curriculum_year_id,
                                    cn::CURRICULUM_YEAR_STUDENT_MAPPING_SCHOOL_ID_COL => Auth::user()->{cn::USERS_SCHOOL_ID_COL},
                                    cn::CURRICULUM_YEAR_STUDENT_MAPPING_GRADE_ID_COL => $gradeId,
                                    cn::CURRICULUM_YEAR_STUDENT_MAPPING_CLASS_ID_COL => $classId
                                ]);
                            }
                        }
                    }
                    $this->StoreAuditLogFunction('','User','','','Student Imported successfully. file name '.$filepath,cn::USERS_TABLE_NAME,'');
                    return redirect('Student')->with('success_msg', __('languages.user_import_successfully'));
            }
        }catch(Exception $exception){
            return $this->sendError($exception->getMessage(), 404);
        }
    }

    /**
     * Use : User can change own password
     */
    public function changePassword(Request $request){
        if(!in_array('change_password_update', Helper::getPermissions(Auth::user()->{cn::USERS_ID_COL}))) {
           return  redirect(Helper::redirectRoleBasedDashboard(Auth::user()->{cn::USERS_ID_COL}));
        }
        if($request->isMethod('get')){
            return view('backend.UsersManagement.change-password');
        }

        if($request->isMethod('post')){
            $request->validate([
                'current_password' => ['required', new MatchOldPassword],
                'new_password' => ['required'],
                'new_confirm_password' => ['same:new_password'],
            ]);

            $updatePassword = User::find(auth()->user()->id)->update(['password'=> Hash::make($request->new_password)]);
            if($updatePassword){
                return redirect('change-password')->with('success_msg', __('languages.password_changed_successfully'));
            }else{
                return redirect('change-password')->with('error_msg', __('languages.problem_was_occur_please_try_again'));
            }
        }
    }

    /**
     * USE : Super admin & School Admin can changed user password
     */
    public function changeUserPassword(Request $request){
        if(!in_array('change_password_update', Helper::getPermissions(Auth::user()->{cn::USERS_ID_COL}))) {
           return  redirect(Helper::redirectRoleBasedDashboard(Auth::user()->{cn::USERS_ID_COL}));
        }
        $params = array();
        parse_str($request->formData, $params);
        if($params['newPassword'] != $params['confirmPassword']){
            return $this->sendError(__('languages.problem_was_occur_please_try_again'), 422);
        }
        $userData = User::where(cn::USERS_ID_COL,$params['userId'])->first();
        if(!empty($userData)){
            if(User::find($params['userId'])->update([cn::USERS_PASSWORD_COL => Hash::make($params['newPassword']) ])){
                // $dataSet = [
                //     'email'     => $userData->email,
                //     'password'  => $params['newPassword']
                // ];
                // $sendEmail = $this->sendMails('email.newCredential', $dataSet, $userData->email, $subject='New Login Credential', [], []);
                return $this->sendResponse([], __('languages.password_changed_successfully'));
            }else{
                return $this->sendError(__('languages.problem_was_occur_please_try_again'), 422);
            }
        }
    }
}