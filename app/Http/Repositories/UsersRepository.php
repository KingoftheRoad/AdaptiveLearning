<?php

namespace App\Http\Repositories;

use Illuminate\Support\Facades\Hash;
use App\Constants\DbConstant as cn;
use App\Traits\Common;
use App\Traits\ResponseFormat;
use App\Models\User;
use App\Models\Grades;
use App\Models\GradeClassMapping;
use Exception;
use Log;
use App\Models\School;
use Illuminate\Support\Facades\Crypt;

class UsersRepository
{
    use Common, ResponseFormat;

    /**
     * USE : Get all users from user table
     */
    public function getAllUsersList($items){
        try {
            // Default Parameter define
            $UserList = [];
            $UserList = User::with('roles')->with('grades')->sortable()->orderBy(cn::USERS_ID_COL,'DESC')->paginate($items);
            return $UserList;
        } catch (\Exception $exception) {
            return back()->withError($exception->getMessage())->withInput();
        }
    }

    /**
     * USE : Store user data
     */
    public function storeUserDetails($request){
        try {
            $classData = null;
            $classnumber = null;
            $Grades = null;
            $schoolId = null;
            
            if (isset($request->class_number)) {
                // $classarray = explode('+',$request->class_number);
                // $class = $classarray[0];
                // $classnumber = $classarray[1];
                $classarray = explode('+',$request->class_number);
                $classnumber = $classarray[1];
                // $Grades = Grades::where(cn::GRADES_NAME_COL,4)->first();
                $Grades = Grades::find($request->grade_id);
                if(!empty($Grades)){
                    $classData = GradeClassMapping::where([cn::GRADE_CLASS_MAPPING_SCHOOL_ID_COL => $request->school,cn::GRADE_CLASS_MAPPING_GRADE_ID_COL => $Grades->id,cn::GRADE_CLASS_MAPPING_NAME_COL => strtoupper($classarray[0])])->first();
                }   
            }
            
            // If role type is school then create first school
            if($request->role == 5){
                $SchoolData = array(
                    cn::SCHOOL_SCHOOL_NAME_COL => $this->encrypt($request->name_en),
                    cn::SCHOOL_SCHOOL_NAME_EN_COL => $this->encrypt($request->name_en),
                    cn::SCHOOL_SCHOOL_NAME_CH_COL => $this->encrypt($request->name_ch),
                    cn::SCHOOL_SCHOOL_EMAIL_COL=> $request->email,
                    cn::SCHOOL_SCHOOL_ADDRESS  => ($request->address_en) ? $this->encrypt($request->address_en) : null,
                    cn::SCHOOL_SCHOOL_ADDRESS_EN_COL  => ($request->address_en) ? $this->encrypt($request->address) : null,
                    cn::SCHOOL_SCHOOL_ADDRESS_CH_COL  => ($request->address_ch) ? $this->encrypt($request->address_ch) : null,
                    cn::SCHOOL_SCHOOL_CITY     => ($request->city) ? $this->encrypt($request->city) : null,
                    cn::SCHOOL_SCHOOL_STATUS   => $request->status
                );
                $School = School::create($SchoolData);
                if($School){
                    $schoolId = $School->id;
                }
            }
            

            $PostData = array(
                cn::USERS_ROLE_ID_COL       => $request->role,
                cn::USERS_GRADE_ID_COL      => $request->grade_id,
                cn::USERS_SCHOOL_ID_COL     => ($request->role == 5) ? $schoolId: $request->school,
                //cn::USERS_NAME_COL          => $request->user_name,
                cn::USERS_NAME_EN_COL       => $this->encrypt($request->name_en),
                cn::USERS_NAME_CH_COL       => $this->encrypt($request->name_ch),
                cn::USERS_EMAIL_COL         => $request->email,
                cn::USERS_MOBILENO_COL      => ($request->mobile_no) ? $this->encrypt($request->mobile_no) : null,
                cn::USERS_ADDRESS_COL       => ($request->address) ? $this->encrypt($request->address) : null,
                cn::USERS_GENDER_COL        => $request->gender ?? null,
                cn::USERS_CITY_COL          => ($request->city) ? $this->encrypt($request->city) : null,
                cn::USERS_DATE_OF_BIRTH_COL => ($request->date_of_birth) ? $this->DateConvertToYMD($request->date_of_birth) : null,
                cn::USERS_STUDENT_NUMBER            =>($request->student_number) ? ($request->student_number) : null,
                cn::USERS_CLASS_ID_COL                 => ($classData) ? $classData->id : null,
                cn::USERS_CLASS_CLASS_STUDENT_NUMBER => $classnumber,
                cn::USERS_PASSWORD_COL      => Hash::make($request->password),
                cn::USERS_STATUS_COL        => $request->status ?? 'active',
                cn::USERS_CREATED_BY_COL    => auth()->user()->id,
                cn::USERS_OTHER_ROLES_COL   => ($request->other_role) ? implode(',',$request->other_role) : null,
            );
            $Users = User::create($PostData);
            return $Users;
        } catch (\Exception $exception) {
            return back()->withError($exception->getMessage())->withInput();
        }
    }

    /**
     * USE : Store user data
     */
    public function UpdateUserDetails($request, $id){
        try {
            $class = null;
            $classnumber = null;
            $classData = null;
            $User = '';
            if (isset($request->class_number)) {
                $classarray = explode('+',$request->class_number);
                $class = $classarray[0];
                $classnumber = $classarray[1];
                
                $Grades = Grades::find($request->grade_id);
                if(!empty($Grades)){
                    $classData = GradeClassMapping::where([cn::GRADE_CLASS_MAPPING_SCHOOL_ID_COL => $request->school,cn::GRADE_CLASS_MAPPING_GRADE_ID_COL => $Grades->id,cn::GRADE_CLASS_MAPPING_NAME_COL => strtoupper($classarray[0])])->first();
                }
            }
            $userData = User::find($id);
            
            $PostData = array(
                cn::USERS_ROLE_ID_COL       => $request->role,
                cn::USERS_GRADE_ID_COL      => $request->grade_id,
                cn::USERS_SCHOOL_ID_COL     => $request->school,
                //cn::USERS_NAME_COL        => $request->user_name,
                cn::USERS_NAME_EN_COL       => $this->encrypt($request->name_en),
                cn::USERS_NAME_CH_COL       => $this->encrypt($request->name_ch),
                cn::USERS_EMAIL_COL         => $request->email,
                cn::USERS_MOBILENO_COL      => ($request->mobile_no) ? $this->encrypt($request->mobile_no) : null,
                cn::USERS_ADDRESS_COL       => ($request->address) ? $this->encrypt($request->address) : null,
                cn::USERS_GENDER_COL        =>$request->gender ?? null,
                cn::USERS_CITY_COL          => ($request->city) ? $this->encrypt($request->city) : null,
                cn::USERS_DATE_OF_BIRTH_COL => ($request->date_of_birth) ? $this->DateConvertToYMD($request->date_of_birth) : null,
                cn::USERS_STATUS_COL        => $request->status ?? 'active',
                cn::USERS_OTHER_ROLES_COL   => ($request->other_role) ? implode(',',$request->other_role) : null,
                  
                cn::USERS_STUDENT_NUMBER            =>($request->student_number) ? ($request->student_number) : null,
                cn::USERS_CLASS_ID_COL                 => (!empty($classData->id)) ? $classData->id : null,
                cn::USERS_CLASS_CLASS_STUDENT_NUMBER => $classnumber,
               
            );
            if($request->role == cn::SCHOOL_ROLE_ID){
                $SchoolData = array(
                    cn::SCHOOL_SCHOOL_NAME_COL => $this->encrypt($request->name_en),
                    cn::SCHOOL_SCHOOL_NAME_EN_COL => $this->encrypt($request->name_en),
                    cn::SCHOOL_SCHOOL_NAME_CH_COL => $this->encrypt($request->name_ch),
                    cn::SCHOOL_SCHOOL_EMAIL_COL => $request->email,
                );
                $school = School::where(cn::SCHOOL_ID_COLS,$userData->school_id)->update($SchoolData);
                unset($PostData[cn::USERS_SCHOOL_ID_COL]);
                $User = User::where(cn::USERS_ID_COL,$id)->Update($PostData);
            }else{
                $User = User::where(cn::USERS_ID_COL,$id)->Update($PostData);
            }
            
            return $User;
        } catch (\Exception $exception) {
            return back()->withError($exception->getMessage())->withInput();
        }
    }
}