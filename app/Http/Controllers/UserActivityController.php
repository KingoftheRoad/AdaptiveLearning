<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserActivities;
use App\Models\User;
use App\Models\School;
use App\Models\Grades;
use App\Models\Role;
use App\Traits\Common;
use App\Constants\DbConstant As cn;
use Auth;
use App\Helpers\Helper;

class UserActivityController extends Controller
{
    use Common;

    public function index(Request $request){
        try{
            //  Laravel Pagination set in Cookie
            //$this->paginationCookie('UserActivityList',$request);
            if(!in_array('user_activity_read', Helper::getPermissions(Auth::user()->{cn::USERS_ID_COL}))) {
                return  redirect(Helper::redirectRoleBasedDashboard(Auth::user()->{cn::USERS_ID_COL}));
            }
            $items = $request->items ?? 10;
            $userList = User::sortable()->orderBy(cn::LOGIN_ACTIVITIES_ID_COL,'DESC')->paginate($items);
            $schoolList = School::all();
            $gradeList = Grades::all();
            $roleList = Role::all();
            $Query = User::select('*');
            if(isset($request->filter)){
                //search by school
                if(isset($request->school_id) && !empty($request->school_id)){
                    $Query->where(cn::USERS_SCHOOL_ID_COL,$request->school_id)->get();
                }
                //search by grade
                if(isset($request->grade_id) && !empty($request->grade_id)){
                    // $Query->where(cn::USERS_GRADE_ID_COL,$request->grade_id)->get();
                    $Query->where(cn::USERS_ID_COL,$this->curriculum_year_mapping_student_ids($request->grade_id,'',''));
                }
                //search by role
                if(isset($request->role_id) && !empty($request->role_id)){
                    $Query->where(cn::USERS_ROLE_ID_COL,$request->role_id)->get();
                }
                $userList = $Query->sortable()->paginate($items);
            }
        
            return view('backend.user_activities.list',compact('userList','schoolList','gradeList','roleList','items')); 
        } catch (\Exception $exception) {
            return redirect('users')->withError($exception->getMessage())->withInput();
        }
    }
   
    public function show(Request $request, $id){
        try{
            if(!in_array('user_activity_read', Helper::getPermissions(Auth::user()->{cn::USERS_ID_COL}))) {
                return  redirect(Helper::redirectRoleBasedDashboard(Auth::user()->{cn::USERS_ID_COL}));
            }
            $items = $request->items ?? 10;
            $UsesDetail = User::find($id); 
            $TotalUserActivityData = UserActivities::with('users')->where(cn::LOGIN_ACTIVITIES_USER_ID_COL,$id)->count();
            $UserActivities = UserActivities::with('users')->where(cn::LOGIN_ACTIVITIES_USER_ID_COL,$id)->orderBy(cn::LOGIN_ACTIVITIES_ID_COL,'DESC')->paginate($items);
            return view('backend.user_activities.activities_report',compact('UserActivities','items','UsesDetail','TotalUserActivityData')); 
        } catch (\Exception $exception) {
            return redirect('users')->withError($exception->getMessage())->withInput();
        }
    }
}
