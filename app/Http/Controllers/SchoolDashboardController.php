<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use App\Models\School;
use App\Models\User;
use App\Constants\DbConstant As cn;
use App\Traits\Common;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\File;
use Intervention\Image\Facades\Image;
use Exception;
class SchoolDashboardController extends Controller
{
    use Common;
    
    public function __construct(){
        $this->middleware('auth');
        $this->middleware('preventBackHistory');
    }
    
    public function index(){
        return view('backend.school_dashboard');
    }

    public function SchoolProfile(){        
        try{
            $id = auth()->user()->{cn::USERS_SCHOOL_ID_COL} ;
            $uid = auth()->user()->{cn::USERS_ID_COL};
            $SchoolData = School::where(cn::SCHOOL_ID_COLS,$id)->first();
            $UserData = User::where(cn::USERS_ID_COL,$uid)->first();
            (!empty($UserData)) ? $Schoolemail['email'] = $UserData->{cn::USERS_EMAIL_COL} :  $Schoolemail['email'] = '';
            return view('backend.schools.school_profile',compact('SchoolData','Schoolemail','UserData'));
        }catch(Exception $exception){
            return redirect('backend.schools.school_profile')->withError($exception->getMessage())->withInput(); 
        }
    }
    
    public function SchoolProfileUpdate(Request $request){
       try{
            $id = auth()->user()->{cn::USERS_SCHOOL_ID_COL} ;
            $destinationPath = public_path('uploads/profile_image');
            $userDetail = User::find(auth()->user()->{cn::USERS_ID_COL});
            $validator = Validator::make($request->all(), School::rules($request, 'update'), School::rulesMessages('update'));
            if ($validator->fails()) {
                return back()->withErrors($validator)->withInput();
            }
            if($request->File('profile_photo')){
                if($userDetail->profile_photo != NULL){
                    $image_path = public_path("{$userDetail->profile_photo}");
                    if (File::exists($image_path)) {
                        unlink($image_path);
                    }
                }
                
                $profile_image = $request->file('profile_photo');
                $profileImageName = rand(10,100000).time().'.'.$profile_image->extension();
                $img = Image::make($profile_image->path());
                $img->resize(100, 100, function ($constraint) {
                    $constraint->aspectRatio();
                })->save($destinationPath.'/'.$profileImageName);
                //$destinationPath = public_path('/images');
                $profile_image->move($destinationPath, $profileImageName);
                $ProfileImageFullPath = 'uploads/profile_image/'.$profileImageName;
            }
            $PostData = array(
            cn::SCHOOL_SCHOOL_NAME_EN_COL => $this->encrypt($request->school_name_en),
            cn::SCHOOL_SCHOOL_NAME_CH_COL => $this->encrypt($request->school_name_ch),
            cn::SCHOOL_SCHOOL_CODE_COL => $request->school_code,
            cn::SCHOOL_SCHOOL_ADDRESS  => ($request->address) ? $this->encrypt($request->address) : null,
            cn::SCHOOL_SCHOOL_CITY     => ($request->city) ? $this->encrypt($request->city) : null,
            cn::SCHOOL_STARTTIME_COL   => (!empty($request->starttime) ? $this->DateConvertToYMD($request->starttime) : ''),
            cn::SCHOOL_SCHOOL_STATUS   => $request->status
        );
        $this->StoreAuditLogFunction($PostData,'School',cn::SCHOOL_ID_COLS,$id,'Update School Profile',cn::SCHOOL_TABLE_NAME,'');
        $Schools = School::where(cn::SCHOOL_ID_COLS,$id)->update($PostData);
        //in user table 
        if(!empty($Schools)){
                if(User::where(cn::USERS_ROLE_ID_COL,auth()->user()->role_id)->where(cn::USERS_SCHOOL_ID_COL,$id)->exists()){
                    $school_add_in_user_table = array(
                        cn::USERS_NAME_COL      => $request->school_name,
                        cn::USERS_ADDRESS_COL   => ($request->address) ? $this->encrypt($request->address) : null,
                        cn::USERS_CITY_COL      => ($request->city) ? $this->encrypt($request->city) : null,
                        cn::USERS_PROFILE_PHOTO_COL => $request->file('profile_photo') ? $ProfileImageFullPath : $userDetail->profile_photo
                     );
                }
                $this->StoreAuditLogFunction($school_add_in_user_table,'User',cn::USERS_ROLE_ID_COL,auth()->user()->role_id,'Update School Profile',cn::USERS_TABLE_NAME,'');
                $update = User::where(cn::USERS_ROLE_ID_COL,auth()->user()->role_id)->where(cn::USERS_SCHOOL_ID_COL,$id)->update($school_add_in_user_table);
                return redirect('schoolprofile')->with('success_msg', __('languages.profile_updated_successfully'));
         }else{
            return back()->with('error_msg', __('languages.problem_was_occur_please_try_again'));
         }
       }catch(Exception $exception){
            return $this->sendError($exception->getMessage(), 404);
       }
    }
}
