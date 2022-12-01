<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Role;
use App\Models\Modules;
use App\Constants\DbConstant As cn;
use Exception;
use Illuminate\Support\Facades\Validator;
use App\Traits\Common;
use Auth;
use App\Helpers\Helper;

class RolesManagementController extends Controller
{
    use Common;
    
    public function index(Request $request){
        try{
            //  Laravel Pagination set in Cookie
            //$this->paginationCookie('RolesList',$request);
            if(!in_array('roles_management_read', Helper::getPermissions(Auth::user()->{cn::USERS_ID_COL}))) {
                return  redirect(Helper::redirectRoleBasedDashboard(Auth::user()->{cn::USERS_ID_COL}));
            }
            $items = $request->items ?? 10;
            $TotalRoleData = Role::all()->count(); 
             $RoleList = Role::sortable()->orderBy(cn::ROLES_ID_COL,'DESC')->paginate($items);
            return view('backend.rolesmanagement.list',compact('RoleList','TotalRoleData','items'));
        }
        catch(Exception $exception){
            return redirect('rolesmanagement')->withError($exception->getMessage())->withInput();
        }
    }

    public function create(){
        try{
            if(!in_array('roles_management_create', Helper::getPermissions(Auth::user()->{cn::USERS_ID_COL}))) {
                return  redirect(Helper::redirectRoleBasedDashboard(Auth::user()->{cn::USERS_ID_COL}));
            }
            $modules = Modules::all();
            return view('backend.rolesmanagement.add',compact('modules'));
        }catch(Exception $exception){
            return back()->withError($exception->getMessage())->withInput();
        }
    }

    public function store(Request $request){
        if(!in_array('roles_management_create', Helper::getPermissions(Auth::user()->{cn::USERS_ID_COL}))) {
            return  redirect(Helper::redirectRoleBasedDashboard(Auth::user()->{cn::USERS_ID_COL}));
        }
        //  Check validation
        $validator = Validator::make($request->all(), Role::rules($request, 'create'), Role::rulesMessages('create'));
        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }
        
        $PostData =array(
            cn::ROLES_ROLE_NAME_COL => ucwords($request->role_name),
            cn::ROLES_ROLE_SLUG_COL => strtolower(str_replace(' ','_',trim($request->role_name,' '))),
            cn::ROLES_STATUS_COL    => $request->status,
            cn::ROLES_PERMISSION_COL => (!empty($request->permissions)) ? implode(',',$request->permissions) : NULL,
         );
         $this->StoreAuditLogFunction($PostData,'Role','','','Create Role',cn::ROLES_TABLE_NAME,'');
         $Roles = Role::create($PostData);
         if(!empty($Roles)){
            return redirect('rolesmanagement')->with('success_msg', __('languages.role_added_successfully'));
         }
         else{
            return back()->with('error_msg', __('languages.problem_was_occur_please_try_again'));
         }
    }

    public function edit($id){
        try{
            if(!in_array('roles_management_update', Helper::getPermissions(Auth::user()->{cn::USERS_ID_COL}))) {
                return  redirect(Helper::redirectRoleBasedDashboard(Auth::user()->{cn::USERS_ID_COL}));
            }
            $role = Role::find($id);
            $modules = Modules::all();
            return view('backend.rolesmanagement.edit',compact('role','modules'));
        }
        catch(Exception $exception){
            return back()->withError($exception->getMessage())->withInput();
        }
    }

    public function update(Request $request, $id){
        try{
            if(!in_array('roles_management_update', Helper::getPermissions(Auth::user()->{cn::USERS_ID_COL}))) {
                return  redirect(Helper::redirectRoleBasedDashboard(Auth::user()->{cn::USERS_ID_COL}));
            }
            $validator = Validator::make($request->all(), Role::rules($request, 'update'), Role::rulesMessages('update'));
            if ($validator->fails()) {
                return back()->withErrors($validator)->withInput();
            }
            $PostData = array(
                cn::ROLES_ROLE_NAME_COL => ucwords($request->role_name),
                cn::ROLES_ROLE_SLUG_COL => strtolower(str_replace(' ','_',trim($request->role_name,' '))),
                cn::ROLES_STATUS_COL    => $request->status,
                cn::ROLES_PERMISSION_COL => (!empty($request->permissions)) ? implode(',',$request->permissions) : NULL,
            );
            $this->StoreAuditLogFunction($PostData,'Role',cn::ROLES_ID_COL,$id,'Update Role',cn::ROLES_TABLE_NAME,'');
            $update = Role::where(cn::ROLES_ID_COL,$id)->update($PostData);
            if(!empty($update)){
                return redirect('rolesmanagement')->with('success_msg', __('languages.role_updated_successfully'));
            }
            else{
                return back()->with('error_msg', __('languages.problem_was_occur_please_try_again'));
            }
        }catch(\Exception $exception){
            return back()->withError($exception->getMessage())->withInput();
        }
    }

    public function destroy($id){
        try{
            if(!in_array('roles_management_delete', Helper::getPermissions(Auth::user()->{cn::USERS_ID_COL}))) {
                return  redirect(Helper::redirectRoleBasedDashboard(Auth::user()->{cn::USERS_ID_COL}));
            }
            $Role = Role::find($id);
            $this->StoreAuditLogFunction('','Role','','','Delete Role ID '.$id,cn::ROLES_TABLE_NAME,'');
            if($Role->delete()){
                return $this->sendResponse([], __('languages.role_deleted_successfully'));
            }else{
                return $this->sendError(__('languages.please_try_again'), 422);
            }
        }catch (\Exception $exception) {
            return $this->sendError($exception->getMessage(), 404);
        }
    }
}
