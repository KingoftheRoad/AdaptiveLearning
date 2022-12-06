<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Constants\DbConstant As cn;
use App\Traits\Common;
use App\Models\User;
use App\Models\OtherRoles;
use App\Models\UserCreditPointHistory;
use Exception;
use Illuminate\Support\Facades\File;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\DB;
class ProfileController extends Controller
{
    use Common;
    
    public function index(){
        try{
            // $strands_id = array();
            // $learning_units_id = array();
            // $learning_objectives_id = array();
            //$studyFocusTreeOption = $this->getSubjectMapping($strands_id,$learning_units_id,$learning_objectives_id);
            $user = User::with('roles')->find($this->LoggedUserId());
            return view('backend.profile.change_profile_student',compact('user'));
            //return view('backend.profile.change_profile_student',compact('user','studyFocusTreeOption'));
        }
        catch(Exception $exception){
            return back()->withError($exception->getMessage())->withInput();
        }  
    }

    public function update_student_profile(Request $request, $id){
        try{
            if ($request->isMethod('patch')){
                $destinationPath = public_path('uploads\\profile_image');
                $userDetail = User::find($id);
                // If logo image is existing into request params
                if($request->file('profile_photo')){
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
                    $profile_image->move($destinationPath, $profileImageName);
                    $ProfileImageFullPath = 'uploads\\profile_image\\'.$profileImageName;
                }
                $PostData = array(
                    // cn::USERS_NAME_COL          => $request->user_name,
                    cn::USERS_NAME_EN_COL       => $this->encrypt($request->name_en),
                    cn::USERS_NAME_CH_COL       => $this->encrypt($request->name_ch),
                    cn::USERS_EMAIL_COL         => $request->email,
                    cn::USERS_MOBILENO_COL      => $this->encrypt($request->mobile_no),
                    cn::USERS_DATE_OF_BIRTH_COL => $this->DateConvertToYMD($request->date_of_birth),
                    cn::USERS_GENDER_COL        => ($request->gender) ? $request->gender : null,
                    cn::USERS_CITY_COL          => ($request->city) ? $this->encrypt($request->city) : null,
                    cn::USERS_ADDRESS_COL       => ($request->address) ? $this->encrypt($request->address)  : null,
                    cn::USERS_PROFILE_PHOTO_COL => $request->file('profile_photo') ? $ProfileImageFullPath : $userDetail->profile_photo,
                );
                $update = User::where(cn::USERS_ID_COL,$id)->update($PostData);
                if(!empty($update)){
                    $this->StoreAuditLogFunction($PostData,'User','','','Update Profile',cn::USERS_TABLE_NAME,'');
                    return redirect('profile')->with('success_msg', __('languages.profile_updated_successfully'));
                }else{
                    return back()->with('error_msg', __('languages.problem_was_occur_please_try_again'));
                }
            }else{
                $user = User::find($id);
                return view('backend.profile.change_profile',compact('user'));

            }
        }catch(Exception $exception){
            return back()->withError($exception->getMessage())->withInput();
        }
    }
    
    public function destroy($id){
        //
    }

    public function teacher_profile(){
        try{
            $user = User::with('roles')->find($this->LoggedUserId());
            $otherRole = array();
            if(!empty($user->other_roles_id)){
                $otherRole = OtherRoles::select(DB::raw('group_concat('.cn::OTHER_ROLE_NAME_COL.') as roles'))->whereIn(cn::OTHER_ROLE_ID_COL,explode(',',$user->other_roles_id))->first();
            }
            return view('backend.profile.change_profile_teacher',compact('user','otherRole'));
        }catch(Exception $exception){
            return back()->withError($exception->getMessage())->withInput();
        }
    }

    public function update_teacher_profile(Request $request,$id){
        try{
            if ($request->isMethod('patch')){
                $destinationPath = public_path('uploads\\profile_image');
                $userDetail = User::find($id);
                // If logo image is existing into request params
                if($request->file('profile_photo')){
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
                    $ProfileImageFullPath = 'uploads\\profile_image\\'.$profileImageName;
                }
                $PostData = array(
                    // cn::USERS_NAME_COL          => $request->user_name,
                    cn::USERS_NAME_EN_COL       => $this->encrypt($request->name_en),
                    cn::USERS_NAME_CH_COL       => $this->encrypt($request->name_ch),
                    cn::USERS_EMAIL_COL         => $request->email,
                    cn::USERS_MOBILENO_COL      => $this->encrypt($request->mobile_no),
                    cn::USERS_DATE_OF_BIRTH_COL => $this->DateConvertToYMD($request->date_of_birth),
                    cn::USERS_GENDER_COL        => ($request->gender) ? $request->gender : null,
                    cn::USERS_CITY_COL          => ($request->city) ? $this->encrypt($request->city) : null,
                    cn::USERS_ADDRESS_COL       => ($request->address) ? $this->encrypt($request->address) : null,
                    cn::USERS_PROFILE_PHOTO_COL => $request->file('profile_photo') ? $ProfileImageFullPath : $userDetail->profile_photo,
                );
                $update = User::where(cn::USERS_ID_COL,$id)->update($PostData);
                if(!empty($update)){
                    return redirect('teacher/profile')->with('success_msg', __('languages.profile_updated_successfully'));
                }else{
                    return back()->with('error_msg', __('languages.problem_was_occur_please_try_again'));
                }
            }else{
                $user = User::find($id);
                return view('backend.profile.change_profile',compact('user'));

            }
        }catch(Exception $exception){
            return back()->withError($exception->getMessage())->withInput();
        }
    }

    /**
     * USE : Page for student credit points
     */
    public function creditPointHistory(Request $request,$user_id){
        try{
            $studentId = $user_id;
            $items = $request->items ?? 10;
            $UserData = User::with('getUserCreditPointHistory')
                    ->withSum(['getUserCreditPointHistory' => function ($query) use($user_id){
                        $query->where(['user_id' => $user_id]);
                    }], 'no_of_credit_point')
                    ->find($user_id);
            $TotalFilterData ='';
            $CreditPointHistoryList = UserCreditPointHistory::with('getExam')->where(cn::USER_CREDIT_POINT_HISTORY_USER_ID_COL,$user_id)
                                                            ->orderBy(cn::USER_CREDIT_POINT_HISTORY_ID_COL,'DESC')
                                                            ->groupBy(cn::EXAM_CREDIT_POINT_RULES_MAPPING_EXAM_ID_COL)->groupBy(cn::CREATED_AT_COL)
                                                            ->sortable()
                                                            ->paginate($items);
            return view('backend.profile.credit_point_history',compact('CreditPointHistoryList','items','TotalFilterData','UserData','studentId'));
        }
        catch(Exception $exception){
            return back()->withError($exception->getMessage())->withInput();
        }  
    }
}
