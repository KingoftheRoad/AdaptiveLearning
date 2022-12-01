<?php

namespace App\Http\Controllers;

use App\Traits\ResponseFormat;
use App\Traits\Common;
use Illuminate\Http\Request;
use App\Models\User;
use App\Constants\DbConstant As cn;
use Illuminate\Support\Facades\Hash;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Helpers\Helper;

class SubAdminController extends Controller
{
    use Common;
    
    public function index(Request $request){
        try{
            if(!in_array('sub_admin_management_read', Helper::getPermissions(Auth::user()->{cn::USERS_ID_COL}))) {
                return  redirect(Helper::redirectRoleBasedDashboard(Auth::user()->{cn::USERS_ID_COL}));
            }
            //  Laravel Pagination set in Cookie
            //$this->paginationCookie('SchoolSubAdminList',$request);
            $items = $request->items ?? 10;
            $SubAdminData = User::whereNotNull(cn::USERS_CREATED_BY_COL)->where([cn::USERS_ROLE_ID_COL=>cn::SCHOOL_ROLE_ID,cn::USERS_SCHOOL_ID_COL => $this->isSchoolLogin()])->orWhere(cn::USERS_CREATED_BY_COL ,Auth()->user()->role_id)->sortable()->orderBy(cn::USERS_ID_COL,'DESC')->paginate($items);
            $CountSubAdminData = User::whereNotNull(cn::USERS_CREATED_BY_COL)->where([cn::USERS_ROLE_ID_COL=>cn::SCHOOL_ROLE_ID,cn::USERS_SCHOOL_ID_COL => $this->isSchoolLogin()])->orWhere(cn::USERS_CREATED_BY_COL ,Auth()->user()->role_id)->count();
            $statusList = array(
                ['id' => 'pending',"name" => 'Pending'],
                ['id' => 'active',"name" => 'Active'],
                ['id' => 'inactive',"name" => 'Inactive']
            );
            //Filteration Sub Admin
            $Query = User::select('*');
            if(isset($request->filter)){
                if(isset($request->Search) && !empty($request->Search)){
                    $Query->orwhere(cn::USERS_NAME_EN_COL,$this->encrypt($request->Search));
                    $Query->orwhere(cn::USERS_NAME_CH_COL,$this->encrypt($request->Search));
                    $Query->orwhere(cn::USERS_EMAIL_COL,$request->Search);
                }
                if(isset($request->Status) && !empty($request->Status)){
                    $Query->where(cn::USERS_STATUS_COL,$request->Status);
                }
                $CountSubAdminData = $Query->whereNotNull(cn::USERS_CREATED_BY_COL)->where([cn::USERS_ROLE_ID_COL=>cn::SCHOOL_ROLE_ID,cn::USERS_SCHOOL_ID_COL => $this->isSchoolLogin()])->orWhere(cn::USERS_CREATED_BY_COL ,Auth()->user()->role_id)->count();
                $SubAdminData = $Query->whereNotNull(cn::USERS_CREATED_BY_COL)->where([cn::USERS_ROLE_ID_COL=>cn::SCHOOL_ROLE_ID,cn::USERS_SCHOOL_ID_COL => $this->isSchoolLogin()])->orWhere(cn::USERS_CREATED_BY_COL ,Auth()->user()->role_id)->sortable()->orderBy(cn::USERS_ID_COL,'DESC')->paginate($items);
            }
            return view('backend.sub_admin.list',compact('SubAdminData','items','CountSubAdminData','statusList'));
        }catch(Exception $exception){
            return redirect('sub-admin')->withError($exception->getMessage())->withInput();
        }
    }

    public function create(){
        try{
            if(!in_array('sub_admin_management_create', Helper::getPermissions(Auth::user()->{cn::USERS_ID_COL}))) {
                return  redirect(Helper::redirectRoleBasedDashboard(Auth::user()->{cn::USERS_ID_COL}));
            }
            return view('backend.sub_admin.add');
        }catch(Exception $exception){
            return back()->with('error_msg', 'Problem was error accured.. Please try again..');
        }
    }

    public function store(Request $request){
        try{
            if(!in_array('sub_admin_management_create', Helper::getPermissions(Auth::user()->{cn::USERS_ID_COL}))) {
                return  redirect(Helper::redirectRoleBasedDashboard(Auth::user()->{cn::USERS_ID_COL}));
            }
            // Check validation
            $validator = Validator::make($request->all(), User::rules($request, 'create'), User::rulesMessages('create'));
            if ($validator->fails()) {
                return back()->withErrors($validator)->withInput();
            }
            $postData = User::create([
                cn::USERS_ROLE_ID_COL       => cn::SCHOOL_ROLE_ID,
                cn::USERS_SCHOOL_ID_COL     => $this->isSchoolLogin(),
                cn::USERS_NAME_COL          => $this->encrypt($request->name_en),
                cn::USERS_NAME_EN_COL       => $this->encrypt($request->name_en),
                cn::USERS_NAME_CH_COL       => $this->encrypt($request->name_ch),
                cn::USERS_EMAIL_COL         => $request->email,                    
                cn::USERS_PASSWORD_COL      => Hash::make($request->password),
                cn::USERS_CREATED_BY_COL    => Auth::user()->{cn::USERS_ID_COL},
                cn::USERS_STATUS_COL        => $request->status
            ]);
            if(!empty($postData)){
                $this->StoreAuditLogFunction($postData,'User','','','Create Sub-Admin',cn::USERS_TABLE_NAME,'');
                return redirect('sub-admin')->with('success_msg', __('languages.sub_admin_added_successfully'));
            }else{
                return back()->withInput(); 
            }
        }catch(Exception $exception){
            return back()->withError($exception->getMessage())->withInput(); 
        }
    }

    public function edit($id){
        try{
            if(!in_array('sub_admin_management_update', Helper::getPermissions(Auth::user()->{cn::USERS_ID_COL}))) {
                return  redirect(Helper::redirectRoleBasedDashboard(Auth::user()->{cn::USERS_ID_COL}));
            }
            $subAdmindata = User::find($id);
            return view('backend.sub_admin.edit',compact('subAdmindata'));
        }catch(Exception $exception){
            return back()->withError($exception->getMessage())->withInput(); 
        }
    }

    public function update(Request $request, $id){
        try{
            if(!in_array('sub_admin_management_update', Helper::getPermissions(Auth::user()->{cn::USERS_ID_COL}))) {
                return  redirect(Helper::redirectRoleBasedDashboard(Auth::user()->{cn::USERS_ID_COL}));
            }
            // Check validation
            $validator = Validator::make($request->all(), User::rules($request, 'update', $id), User::rulesMessages('update'));
            if ($validator->fails()) {
                return back()->withErrors($validator)->withInput();
            }
            
            $postData = User::find($id)->update([
                cn::USERS_ROLE_ID_COL       => cn::SCHOOL_ROLE_ID,
                cn::USERS_SCHOOL_ID_COL     => $this->isSchoolLogin(),
                cn::USERS_NAME_COL          => $this->encrypt($request->name_en),
                cn::USERS_NAME_EN_COL       => $this->encrypt($request->name_en),
                cn::USERS_NAME_CH_COL       => $this->encrypt($request->name_ch),
                cn::USERS_EMAIL_COL         => $request->email,                                   
                cn::USERS_STATUS_COL        => $request->status
            ]);

            if(!empty($postData)){
                $this->StoreAuditLogFunction($request->all(),'User',cn::USERS_ID_COL,$id,'Update Sub-Admin',cn::USERS_TABLE_NAME,'');
                return redirect('sub-admin')->with('success_msg', __('languages.sub_admin_updated_successfully'));
            }else{
                return back()->with('error_msg', __('languages.problem_was_occur_please_try_again'));
            }
        }catch(Exception $exception){
            return back()->withError($exception->getMessage())->withInput();
        }
    }

    public function destroy($id){
        try{
            if(!in_array('sub_admin_management_delete', Helper::getPermissions(Auth::user()->{cn::USERS_ID_COL}))) {
                return  redirect(Helper::redirectRoleBasedDashboard(Auth::user()->{cn::USERS_ID_COL}));
            }
            $User = User::find($id);
            if($User->delete()){
                $this->StoreAuditLogFunction('','User','','','Delete Sub Admin ID '.$id,cn::USERS_TABLE_NAME,'');
                return $this->sendResponse([], __('languages.sub_admin_deleted_successfully'));
            }else{
                return $this->sendError(__('languages.problem_was_occur_please_try_again'), 422);
            }
        }catch(Exception $exception){
            return back()->withError($exception->getMessage())->withInput();
        }
    }
}
